<?php
// --- SETUP AND HELPERS ---
require_once __DIR__ . 'includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

// Security headers - MUST be before any output
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
   <meta name="robots" content="index, follow">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Downloads - 360 Education</title>
  <meta name="description" content="Download 2025 new books, PCTB new syllabus, official Punjab Textbook Board (PCTB) textbooks, notes, past papers, model papers, guess papers, solved exercises, and study material for Matric, FSC, and all grades. Free PDF downloads for Urdu and English medium.">
  <meta name="keywords" content="2025 new books, pctb new syllabus, notes, PCTB, Punjab Textbook Board, free download, latest syllabus, updated books, study material, past papers, model papers, guess papers, solved exercises, pdf, all subjects, Urdu notes, English notes, Matric, FSC, 2025 notes, 2025 syllabus, class 9, class 10, class 11, class 12, Punjab curriculum, textbook download, PCTB 2025, 2025 textbooks, 2025 study notes, 2025 solved papers">
  <link rel="canonical" href="https://edu.360muslimexperts.com/downloads.php" />
  <meta property="og:title" content="Download 2025 PCTB Books, Notes, Syllabus, Past Papers | 360 Education">
  <meta property="og:description" content="Free download of 2025 PCTB new syllabus, textbooks, notes, past papers, model papers, and study material for Matric, FSC, and all grades.">
  <meta property="og:url" content="https://edu.360muslimexperts.com/downloads.php">
  <meta property="og:type" content="website">
  <meta property="og:image" content="https://edu.360muslimexperts.com/assets/og-image.jpg">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Download 2025 PCTB Books, Notes, Syllabus, Past Papers | 360 Education">
  <meta name="twitter:description" content="Free download of 2025 PCTB new syllabus, textbooks, notes, past papers, model papers, and study material for Matric, FSC, and all grades.">
  <meta name="twitter:image" content="https://edu.360muslimexperts.com/assets/og-image.jpg">
  <link rel="stylesheet" href="/css/style.css" />
  <link rel="stylesheet" href="/css/header-footer.css" />
  <style>
    /* Utility: Visually hidden (for accessibility) */
    .visually-hidden {
      position: absolute !important;
      width: 1px !important;
      height: 1px !important;
      padding: 0 !important;
      margin: -1px !important;
      overflow: hidden !important;
      clip: rect(0, 0, 0, 0) !important;
      white-space: nowrap !important;
      border: 0 !important;
    }
    /* Improve spacing for download sections */
    .download-section {
      margin-top: 2.5rem;
      margin-bottom: 2.5rem;
    }
    .section-divider {
      border: none;
      border-top: 1px solid #333;
      margin: 2rem 0;
      opacity: 0.2;
    }
    /* Focus style for search input */
    #search-input:focus {
      border-color: var(--clr-accent-hover, #00c896);
      box-shadow: 0 0 0 2px rgba(0,200,150,0.15);
      outline: none;
    }
    /* Responsive: reduce spacing on mobile */
    @media (max-width: 768px) {
      .download-section {
        margin-top: 1.2rem;
        margin-bottom: 1.2rem;
      }
      .section-divider {
        margin: 1rem 0;
      }
    }
  </style>
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>
<body>
<a href="#main-content" class="visually-hidden focusable skip-link">Skip to main content</a>
<?php include 'includes/header.php'; ?>
  <main class="downloads-page" id="main-content" role="main">
    <section class="page-title-section">
      <h2>Download Your Books & Notes</h2>
      <p>Find and download 2025 new books, PCTB new syllabus, official Punjab Textbook Board (PCTB) textbooks, notes, past papers, model papers, solved exercises, and study material for FSC and Matric. Free PDF downloads for Urdu and English medium.</p>
    </section>

    <!-- Add search bar -->
    <section class="search-section">
      <input type="text" id="search-input" placeholder="Search books or notes..." aria-label="Search books or notes" autocomplete="off" />
      <p id="no-results-message" style="display: none; text-align: center; color: var(--clr-medium);" aria-live="polite">No results found.</p>
      <noscript>
        <p style="text-align: center; color: var(--clr-medium);">Search functionality requires JavaScript to be enabled.</p>
      </noscript>
    </section>

<?php
// --- Helper: safely fetch JSON data from URL ---
function safeFetchJson($url) {
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $response = @file_get_contents($url, false, $context);
    if (!$response) return null;
    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

// --- Helper: render a download section ---
function render_download_section($title, $items, $description = '') {
    if (empty($items)) {
        return; // Don't render empty sections
    }

    $sectionId = 'section-' . strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));

    $output = "<section class='download-section' aria-labelledby='{$sectionId}'>";
    $output .= "<h2 id='{$sectionId}'>" . htmlspecialchars($title) . "</h2>";
    if ($description) {
        $output .= "<p class='section-desc' style='color:var(--clr-text-secondary);margin-bottom:1rem;font-size:1rem;'>" . htmlspecialchars($description) . "</p>";
    }
    $output .= "<ul class='download-list' role='list'>";

    // Sort items alphabetically by name
    usort($items, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });

    foreach ($items as $item) {
        $displayName = ucwords(str_replace(['_', '-'], ' ', pathinfo($item['name'], PATHINFO_FILENAME)));
        $fileUrl = htmlspecialchars($item['url']);
        $fileSize = isset($item['size']) ? formatBytes($item['size']) : 'N/A';
        $fileExt = strtoupper(pathinfo($item['name'], PATHINFO_EXTENSION));

        $output .= "<li class='download-item'>";
        $output .= "<a href='{$fileUrl}' download title='Download {$displayName}' aria-label='Download {$displayName} ({$fileExt}, {$fileSize})'>";
        $output .= "<span class='item-title'>{$displayName}</span>";
        $output .= "<span class='file-meta'>({$fileExt}, {$fileSize})</span>";
        $output .= "<span class='download-icon' aria-hidden='true' style='margin-left:0.5em;display:inline-flex;align-items:center;'>";
        $output .= '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 3v10m0 0l-4-4m4 4l4-4M4 17h12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        $output .= "</span></a></li>";
    }

    $output .= "</ul></section>";
    return $output;
}

// --- Configuration ---
$grades = ['9', '10', '11', '12'];
$mergedBookFolders = [
    '9'  => ['9', '9-10'],
    '10' => ['10', '9-10'],
    '11' => ['11', '11-12'],
    '12' => ['12', '11-12']
];
$apiBase = 'https://360muslimexperts.com/panel/edu_api.php';
$anyContentFound = false;

// --- Main Loop ---
foreach ($grades as $i => $grade) {
    // --- Fetch and Display Books ---
    $allBooks = [];
    $foldersToFetch = $mergedBookFolders[$grade] ?? [$grade];
    foreach ($foldersToFetch as $folder) {
        $apiUrl = "$apiBase?type=books&grade=" . rawurlencode($folder);
        $data = safeFetchJson($apiUrl);
        if ($data && !isset($data['error'])) {
            foreach ($data as $item) {
                if ($item['type'] === 'file' && !empty($item['url'])) {
                    // Use URL as key to prevent duplicates
                    $allBooks[$item['url']] = [
                        'name' => $item['name'],
                        'url'  => $item['url'],
                        'size' => $item['size'] ?? 0,
                    ];
                }
            }
        }
    }
    $books = array_values($allBooks);
    if (!empty($books)) {
        $anyContentFound = true;
        echo render_download_section(
            "Grade $grade Books",
            $books,
            "Official PCTB textbooks for Grade $grade."
        );
    }

    // --- Fetch and Display Notes ---
    $subjectsApiUrl = "$apiBase?type=notes&grade=" . rawurlencode($grade);
    $subjectsData = safeFetchJson($subjectsApiUrl);

    if ($subjectsData && !isset($subjectsData['error'])) {
        $subjects = array_filter($subjectsData, fn($item) => $item['type'] === 'folder');

        foreach ($subjects as $subject) {
            $subjectName = $subject['name'];
            $notesApiUrl = "$apiBase?type=notes&grade=" . rawurlencode($grade) . "&subject=" . rawurlencode($subjectName);
            $notesData = safeFetchJson($notesApiUrl);

            $allNotes = [];
            if ($notesData && !isset($notesData['error'])) {
                foreach ($notesData as $item) {
                    if ($item['type'] === 'file' && !empty($item['url'])) {
                        $allNotes[$item['url']] = [
                            'name' => $item['name'],
                            'url'  => $item['url'],
                            'size' => $item['size'] ?? 0,
                        ];
                    }
                }
            }

            $notes = array_values($allNotes);
            if (!empty($notes)) {
                $anyContentFound = true;
                echo render_download_section(
                    "Grade $grade " . ucfirst($subjectName) . " Notes",
                    $notes,
                    "Chapter-wise notes for " . ucfirst($subjectName) . "."
                );
            }
        }
    }

    // Add a divider if it's not the last grade
    if ($i < count($grades) - 1) {
        echo "<hr class='section-divider' />";
    }
} // end foreach grade

if (!$anyContentFound) {
    echo "<p class='message message--info'>No books or notes found at the moment. Please check back later.</p>";
}
?>
  </main>
  <script>
  // Unified search for books and notes
  document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('search-input');
    var noResults = document.getElementById('no-results-message');
    var allSections = document.querySelectorAll('.download-section');
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        var filter = searchInput.value.trim().toLowerCase();
        var found = 0;
        allSections.forEach(function(section) {
          var sectionFound = false;
          var listItems = section.querySelectorAll('.download-list li');
          listItems.forEach(function(li) {
            var text = li.textContent.toLowerCase();
            if (text.includes(filter)) {
              li.style.display = '';
              sectionFound = true;
              found++;
            } else {
              li.style.display = 'none';
            }
          });
          section.style.display = sectionFound ? '' : 'none';
        });
        noResults.style.display = found === 0 ? 'block' : 'none';
      });
    }
  });
  </script>
  <?php include 'includes/footer.php'; ?>
</body>
</html>
