<?php
require_once __DIR__ . '/helpers.php'; // Include the helpers file

// --- Determine the Grade from URL parameter ---
// Use strict filtering if possible, e.g., FILTER_VALIDATE_INT with options
$gradeKey = filter_input(INPUT_GET, 'grade', FILTER_SANITIZE_STRING); // Sanitize input

// --- Configuration based on Grade ---
$pageTitle = "Grade Books - 360 Education"; // Default title
$pageHeading = "Books"; // Default heading
$booksDirs = []; // Will hold all relevant directories
$gradeIdentifiers = []; // For cleaning filenames
$errorMsg = '';

// TODO: Move this configuration to a config file or array

switch ($gradeKey) {
    case '9':
        $pageHeading = "Grade 9 Books";
        $booksDirs = ['books/9', 'books/9-10'];
        $gradeIdentifiers = ['-9', '_9th', '9-10'];
        break;
    case '10':
        $pageHeading = "Grade 10 Books";
        $booksDirs = ['books/10', 'books/9-10'];
        $gradeIdentifiers = ['-10', '_10th', '9-10'];
        break;
    case '11':
        $pageHeading = "Grade 11 Books";
        $booksDirs = ['books/11', 'books/11-12'];
        $gradeIdentifiers = ['-11', '_11th', '11-12'];
        break;
    case '12':
        $pageHeading = "Grade 12 Books";
        $booksDirs = ['books/12', 'books/11-12'];
        $gradeIdentifiers = ['-12', '_12th', '11-12'];
        break;
    default:
        $errorMsg = "Invalid grade specified or grade parameter missing.";
        break;
}

// Set the full page title based on the heading
if (empty($errorMsg)) {
    $pageTitle = $pageHeading . " - 360 Education";
}

// --- Function to generate list item HTML ---
function generateListItem($file, $directory, $identifiers) {
    $displayName = htmlspecialchars(pathinfo($file, PATHINFO_FILENAME));

    // Clean up display name by removing grade identifiers
    if (!empty($identifiers)) {
        foreach ($identifiers as $id) {
            $displayName = str_ireplace($id, '', $displayName);
        }
    }
    $displayName = trim($displayName, ' -_');
    $displayName = ucwords(strtolower($displayName));

    $fileUrlEncoded = rawurlencode($file);
    // Use absolute URL for download link
    $webBooksBase = 'https://360muslimexperts.com/education/books';
    $relativeDir = str_replace('books/', '', $directory);
    $filePath = htmlspecialchars("$webBooksBase/$relativeDir/$fileUrlEncoded");

    $fullPath = "$directory/$file";
    $fileSize = file_exists($fullPath) ? filesize($fullPath) : false; // Check existence before getting size
    $formattedSize = ($fileSize !== false) ? formatBytes($fileSize) : 'N/A'; // Use formatBytes

    $output = '<li class="item-list__item">';
    $output .= '<div class="item-list__content">';
    $output .= '<span class="item-list__title">' . $displayName . '</span>';
    $output .= '<span class="file-meta">(' . $formattedSize . ')</span>';
    $output .= '</div>';
    // Add download icon consistent with downloads.php
    $iconSVG = '<svg width="18" height="18" viewBox="0 0 20 20" fill="none" style="display:block" xmlns="http://www.w3.org/2000/svg"><path d="M10 3v10m0 0l-4-4m4 4l4-4M4 17h12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'; // Use currentColor

    $output .= '<div class="item-list__actions download-actions">';
    // View PDF button (opens PDF.js viewer)
    $viewUrl = "view-pdf.php?file=" . rawurlencode("$directory/$file");
    $output .= '<a href="' . $viewUrl . '" class="btn btn--primary" target="_blank" rel="noopener noreferrer" aria-label="View ' . $displayName . '">';
    $output .= '<span class="btn__text">View PDF</span>';
    $output .= '</a> ';
    // Download PDF button (direct download)
    $output .= '<a href="' . $filePath . '" class="btn btn--primary download-button" download aria-label="Download ' . $displayName . ' (' . $formattedSize . ')">';
    $output .= '<span class="btn__text">Download PDF</span>';
    $output .= '</a>';
    $output .= '</div>';
    $output .= '</li>';
    return $output;
}

// --- Main Logic: Scan directory and prepare file list ---
$gradeFiles = [];
if (empty($errorMsg) && !empty($booksDirs)) {
    foreach ($booksDirs as $booksDir) {
        if (is_dir($booksDir)) {
            try {
                $iterator = new DirectoryIterator($booksDir);
                foreach ($iterator as $fileinfo) {
                    if ($fileinfo->isFile() && strtolower($fileinfo->getExtension()) === 'pdf') {
                        // Use full relative path for uniqueness
                        $gradeFiles[$booksDir . '/' . $fileinfo->getFilename()] = [
                            'file' => $fileinfo->getFilename(),
                            'dir' => $booksDir
                        ];
                    }
                }
            } catch (Exception $e) {
                $errorMsg = "Error accessing the book directory. Please try again later.";
                error_log("Directory access error: " . $e->getMessage());
            }
        }
    }
    if (!empty($gradeFiles)) {
        // Sort by filename for consistency
        ksort($gradeFiles);
    } else {
        $errorMsg = "No PDF books found in the directory for this grade yet.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="header-footer.css" />
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-J9TMPM9XPW"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-J9TMPM9XPW');
  </script>
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb">
        <div class="container">
            <a href="index.php">Home</a> &gt; 
            <a href="downloads.php">Downloads</a> &gt; 
            <span><?php echo htmlspecialchars($pageHeading); ?></span>
        </div>
    </nav>

    <!-- Page Title Section -->
    <section class="page-header centered">
        <h1><?php echo htmlspecialchars($pageHeading); ?></h1>
        <p class="subtitle">Explore and download official PCTB textbooks.</p>
    </section>

    <!-- Content Section -->
    <main class="container" id="main-content">
        <section class="content centered">
            <?php
            if (!empty($errorMsg)) {
                echo '<div class="message message--error">' . htmlspecialchars($errorMsg) . '</div>';
            } elseif (empty($gradeFiles)) {
                echo '<div class="message message--info">No PDF books found for this grade yet.</div>';
            } else {
                echo '<h2 class="visually-hidden">List of downloadable books for ' . htmlspecialchars($pageHeading) . '</h2>';
                echo '<ul class="item-list" role="list">';
                foreach ($gradeFiles as $entry) {
                    echo generateListItem($entry['file'], $entry['dir'], $gradeIdentifiers);
                }
                echo '</ul>';
            }
            ?>
        </section>
    </main>

    <!-- Back Button Section -->
    <div class="back-button-container">
        <a href="downloads.php" class="btn btn--secondary">← Back to All Downloads</a>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>