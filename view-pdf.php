<?php
require_once __DIR__ . '/helpers.php';
// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');

// --- Sanitize and validate PDF file parameter ---
$fileUrl = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_URL);

// --- PDF Proxy ---
// If 'proxy=1' is in the URL, this script fetches the remote PDF and serves it.
// This is necessary to bypass browser Cross-Origin (CORS) restrictions when the
// PDF viewer (on this domain) tries to load a file from another domain.
if (isset($_GET['proxy']) && $_GET['proxy'] === '1') {
    if (!$fileUrl || !filter_var($fileUrl, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        die('Error: Invalid PDF URL specified for proxy.');
    }

    $parsedUrl = parse_url($fileUrl);
    // Security: Only proxy files from the allowed domain.
    if (!isset($parsedUrl['host']) || !in_array($parsedUrl['host'], ['360muslimexperts.com', 'www.360muslimexperts.com'])) {
        http_response_code(403);
        die('Error: Access denied. Proxy can only fetch from allowed domains.');
    }

    // Use cURL for a more robust request.
    $ch = curl_init($fileUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 45); // Increased timeout for large files
    $pdfContent = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || $pdfContent === false) {
        http_response_code(502); // Bad Gateway
        die('Error: Could not fetch the PDF file from the remote server.');
    }

    // Serve the fetched PDF content to the browser.
    header('Content-Type: application/pdf');
    header('Content-Length: ' . strlen($pdfContent));
    header('Content-Disposition: inline; filename="' . basename($parsedUrl['path']) . '"');
    echo $pdfContent;
    exit;
}

// Initialize an error message variable
$errorMsg = '';
$pageTitle = "PDF Viewer - 360 Education"; // Default title
$fileName = ''; // Initialize fileName
$pdfPathForJs = ''; // Initialize path for JS

if (!$fileUrl || !filter_var($fileUrl, FILTER_VALIDATE_URL)) {
    $errorMsg = 'Invalid PDF URL specified.';
} else {
    $parsedUrl = parse_url($fileUrl);
    // Security check: Ensure the file is from the allowed domain.
    if (!isset($parsedUrl['host']) || !in_array($parsedUrl['host'], ['360muslimexperts.com', 'www.360muslimexperts.com'])) {
        $errorMsg = 'Access denied. PDFs can only be loaded from the allowed domain.';
    } elseif (!preg_match('/\.pdf$/i', $parsedUrl['path'])) {
        $errorMsg = 'The file must have a .pdf extension.';
    } else {
        // The URL is valid and from the correct domain.
        $fileName = basename($parsedUrl['path']);
        $pageTitle = htmlspecialchars(urldecode($fileName)) . " - PDF Viewer";
        // Create a proxied URL to serve the PDF from our own domain to avoid CORS issues.
        $proxiedUrl = 'view-pdf.php?proxy=1&file=' . rawurlencode($fileUrl);
        // We pass the proxied URL directly to JavaScript.
        // htmlspecialchars() is NOT used here because it would convert '&' to '&amp;',
        // which breaks the URL parameter parsing for the fetch request made by PDF.js.
        $pdfPathForJs = $proxiedUrl;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<link rel="stylesheet" href="style.css" />
<link rel="stylesheet" href="header-footer.css" />
<link rel="stylesheet" href="view-pdf.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
</head>
<body>
  <?php include 'header.php'; ?>

<?php if (!empty($errorMsg)): ?><div class="pdf-viewer-body"> <!-- Apply themed body class -->
<div id="header">
  <div class="filename">Error</div>
</div>
<div id="viewerContainer" class="error-container"> <!-- Use class for styling -->
  <h2>Viewer Error</h2>
  <p><?php echo htmlspecialchars($errorMsg); ?></p>
  <p>
    <a href="javascript:history.back()" class="error-page-button primary" title="Go to previous page">Go Back</a>
    <a href="downloads.php" class="error-page-button secondary" title="Return to downloads page">Return to Downloads</a>
  </p>
</div>
<div id="controls" style="display:none;"></div> <?php // Hide controls if error ?>
</div>

<?php else: ?><div class="pdf-viewer-body"> <!-- Apply themed body class -->
<div id="header">
  <div class="filename"><?php echo htmlspecialchars(urldecode($fileName)); ?></div>
  <button id="downloadBtn" title="Download PDF">⬇ Download</button>
</div>

<div id="viewerContainer">
  <canvas id="pdfCanvas"></canvas>
  <div id="loading"></div>
</div>

<div id="controls" aria-label="PDF Navigation Controls">
  <button id="prevBtn" title="Previous Page">&laquo; Prev</button>
  
  <label for="pageNumInput" style="user-select:none;">Page:</label>
  <input type="number" id="pageNumInput" min="1" value="1" aria-live="polite" aria-atomic="true" />
  <span id="pageCount" aria-label="Total pages" style="display: none;"></span> <!-- Initially hidden -->
  
  <button id="nextBtn" title="Next Page">Next &raquo;</button>
  <button id="resetZoomBtn" title="Reset Zoom">Reset Zoom</button>
  <button id="fullscreenBtn" title="Toggle Fullscreen">⛶ Fullscreen</button>
</div>

<script>
(function() { // IIFE Start
  // The URLs are passed directly from PHP. We avoid htmlspecialchars on the PHP side
  // because it would break the URL structure by converting '&' to '&amp;'.
  const proxiedUrl = "<?php echo $pdfPathForJs; ?>";
  const originalUrl = "<?php echo $fileUrl; ?>";
  
  // PDF.js worker source
  let pdfDoc = null,
      pageNum = 1,
      pageCount = 0,
      scale = 1.5,
      DEFAULT_SCALE = 1.5,
      MIN_SCALE = 0.5,
      MAX_SCALE = 4,
      RENDER_THROTTLE = 100, // ms
      lastRenderTime = 0;
  
  let isInitialLoad = true; // Flag for initial PDF load and first page render
  const canvas = document.getElementById('pdfCanvas');
  const ctx = canvas.getContext('2d');
  // const pageNumSpan = document.getElementById('pageNum'); // This element doesn't exist / not used
  const pageCountSpan = document.getElementById('pageCount');
  const loadingSpinner = document.getElementById('loading');
  const pageNumInput = document.getElementById('pageNumInput');
  const viewerContainer = document.getElementById('viewerContainer');

  let renderTask = null;
  let renderQueued = false;

  // Show spinner immediately for initial document loading
  showLoading(true);

  function showLoading(show) {
    loadingSpinner.style.display = show ? 'block' : 'none';
  }

  function queueRender(num) {
    // Ensure num is within valid page range
    if (num < 1) num = 1;
    if (pageCount > 0 && num > pageCount) num = pageCount;
    
    // throttle rendering to avoid flood on zoom
    const now = Date.now();
    if (now - lastRenderTime > RENDER_THROTTLE) {
      renderPage(num);
      lastRenderTime = now;
    } else {
      if (!renderQueued) {
        renderQueued = true;
        setTimeout(() => {
          renderPage(num);
          lastRenderTime = Date.now();
          renderQueued = false;
        }, RENDER_THROTTLE);
      }
    }
  }

  function updateControls() {
    document.getElementById('prevBtn').disabled = (pageNum <= 1);
    document.getElementById('nextBtn').disabled = (pageNum >= pageCount);
    pageNumInput.max = pageCount > 0 ? pageCount : 1; // Set max for input
  }

  document.getElementById('prevBtn').addEventListener('click', () => {
    if (pageNum <= 1) return;
    queueRender(pageNum - 1);
  });

  document.getElementById('nextBtn').addEventListener('click', () => {
    if (pageNum >= pageCount) return;
    queueRender(pageNum + 1);
  });

  pageNumInput.addEventListener('change', () => {
    let val = parseInt(pageNumInput.value);
    if (isNaN(val) || val < 1) val = 1;
    else if (val > pageCount) val = pageCount;
    if (val !== pageNum) {
      queueRender(val);
    }
    pageNumInput.value = val;
  });

  // Ctrl + Wheel to zoom
  document.addEventListener('wheel', (e) => {
    if (!e.ctrlKey) return;
    e.preventDefault();
    if (e.deltaY < 0) {
      scale = Math.min(scale + 0.1, MAX_SCALE);
    } else {
      scale = Math.max(scale - 0.1, MIN_SCALE);
    }
    queueRender(pageNum);
  }, { passive: false });

  document.getElementById('resetZoomBtn').addEventListener('click', () => {
    scale = DEFAULT_SCALE;
    queueRender(pageNum);
  });

  // Fullscreen toggle for viewer container
  document.getElementById('fullscreenBtn').addEventListener('click', () => {
    if (!document.fullscreenElement) {
      viewerContainer.requestFullscreen().catch(err => {
        alert(`Fullscreen error: ${err.message}`);
      });
    } else {
      document.exitFullscreen();
    }
  });

  // Download button
  document.getElementById('downloadBtn').addEventListener('click', () => {
    window.open(originalUrl, '_blank');
  });

  // Load PDF
  // Consider self-hosting pdf.worker.min.js for production to avoid CDN dependency/issues.
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

  pdfjsLib.getDocument(proxiedUrl).promise.then(pdfDoc_ => {
    pdfDoc = pdfDoc_;
    pageCount = pdfDoc.numPages;
    pageCountSpan.textContent = ` / ${pageCount}`;
    pageCountSpan.style.display = 'inline'; // Show page count
    pageNumInput.max = pageCount;

    if (pageCount > 0) {
        renderPage(pageNum); // Initial render
    } else {
        showLoading(false);
        isInitialLoad = false; // No pages to load
        displayJsError("PDF has no pages or could not be loaded properly.");
    }
  }).catch(err => {
    isInitialLoad = false; // Loading failed
    showLoading(false);
    displayJsError('Error loading PDF: ' + err.message);
    console.error("PDF Loading Error:", err);
  });
  
// Keyboard navigation and shortcuts
document.addEventListener('keydown', (e) => {
  const active = document.activeElement;
  const isInputFocused = (active && (active.tagName === 'INPUT' || active.tagName === 'TEXTAREA'));

  if (isInputFocused) return; // don't hijack input fields

  switch(e.key) {
    case 'ArrowLeft':
    case 'ArrowUp':
      e.preventDefault();
      if (pageNum > 1) queueRender(pageNum - 1);
      break;

    case 'ArrowRight':
    case 'ArrowDown':
      e.preventDefault();
      if (pageNum < pageCount) queueRender(pageNum + 1);
      break;

    case '+':
    case '=': // = key is often used with shift for +
      e.preventDefault();
      scale = Math.min(scale + 0.1, MAX_SCALE);
      queueRender(pageNum);
      break;

    case '-':
      e.preventDefault();
      scale = Math.max(scale - 0.1, MIN_SCALE);
      queueRender(pageNum);
      break;

    case 'r':
    case 'R':
      e.preventDefault();
      scale = DEFAULT_SCALE;
      queueRender(pageNum);
      break;

    case 'f':
    case 'F':
      e.preventDefault();
      if (!document.fullscreenElement) {
        viewerContainer.requestFullscreen().catch(() => {});
      } else {
        document.exitFullscreen();
      }
      break;
  }
});

// Swipe detection on touch devices
let touchStartX = 0;
let touchStartY = 0;
let touchEndX = 0;
let touchEndY = 0;

const SWIPE_THRESHOLD = 50; // Minimum px swipe distance to count

viewerContainer.addEventListener('touchstart', (e) => {
  if (e.touches.length === 1) {
    touchStartX = e.touches[0].clientX;
    touchStartY = e.touches[0].clientY;
  }
});

viewerContainer.addEventListener('touchend', (e) => {
  if (e.changedTouches.length === 1) {
    touchEndX = e.changedTouches[0].clientX;
    touchEndY = e.changedTouches[0].clientY;

    const dx = touchEndX - touchStartX;
    const dy = touchEndY - touchStartY;

    // Detect horizontal vs vertical swipe direction
    if (Math.abs(dx) > Math.abs(dy)) {
      // Horizontal swipe
      if (dx > SWIPE_THRESHOLD) {
        // Swipe right → previous page
        if (pageNum > 1) queueRender(pageNum - 1);
      } else if (dx < -SWIPE_THRESHOLD) {
        // Swipe left → next page
        if (pageNum < pageCount) queueRender(pageNum + 1);
      }
    } else {
      // Vertical swipe
      if (dy > SWIPE_THRESHOLD) {
        // Swipe down → previous page
        if (pageNum > 1) queueRender(pageNum - 1);
      } else if (dy < -SWIPE_THRESHOLD) {
        // Swipe up → next page
        if (pageNum < pageCount) queueRender(pageNum + 1);
      }
    }
  }
});

// Page number fade animation
function animatePageNumber() {
  pageNumInput.style.transition = 'opacity 0.3s ease';
  pageNumInput.style.opacity = '0.3';
  setTimeout(() => {
    pageNumInput.style.opacity = '1';
  }, 300);
}

function renderPage(num) {
  if (!pdfDoc) return; // Don't render if PDF isn't loaded
  if (renderTask) {
    renderTask.cancel();
  }

  if (isInitialLoad) { // Only show spinner for the very first page render
    showLoading(true);
  }
  pdfDoc.getPage(num).then(page => {
    const viewport = page.getViewport({ scale: scale });
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    const renderContext = {
      canvasContext: ctx,
      viewport: viewport
    };

    renderTask = page.render(renderContext);
    return renderTask.promise;
  }).then(() => {
    pageNum = num;
    pageNumInput.value = num;
    pageCountSpan.textContent = ` / ${pageCount}`;
    renderTask = null;

    updateControls();
    animatePageNumber();
    
    if (isInitialLoad) {
        showLoading(false); // Hide spinner after initial load is complete
        isInitialLoad = false; // Subsequent renders won't show main spinner this way
    }
  }).catch(err => {
    if (err?.name === 'RenderingCancelledException') return;
    displayJsError('Error rendering page: ' + err.message);
    console.error("Page Rendering Error:", err);
    showLoading(false); // Ensure spinner is hidden on error
    if (isInitialLoad) isInitialLoad = false;
  });
}

function displayJsError(message) {
    // You can make this more sophisticated, e.g., showing it in a dedicated div
    document.getElementById('controls').style.display = 'none'; // Hide PDF controls
    console.error("PDF Viewer JS Error:", message);
    const errorDiv = document.createElement('div');
    errorDiv.setAttribute('role', 'alert'); // For accessibility
    errorDiv.textContent = message;
    errorDiv.style.color = 'var(--error-color-text)';
    errorDiv.style.textAlign = 'center';
    errorDiv.style.padding = '10px';
    // Potentially add a class here for more complex styling via CSS
    viewerContainer.innerHTML = ''; // Clear canvas
    viewerContainer.appendChild(errorDiv);
}
})(); // IIFE End

</script>
<?php endif; ?>
<?php include 'footer.php'; ?>
</body>
</html>
