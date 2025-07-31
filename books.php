<?php
require_once __DIR__ . 'includes/helpers.php';

// --- Define visible and merged grades ---
$visibleGrades = ['9', '10', '11', '12'];
$mergedFolders = [
    '9'  => ['9', '9-10'],
    '10' => ['10', '9-10'],
    '11' => ['11', '11-12'],
    '12' => ['12', '11-12']
];

// --- Get current grade key from URL ---
$gradeKey = filter_input(INPUT_GET, 'grade', FILTER_SANITIZE_STRING);
$pageTitle = "Grade Books - 360 Education";
$pageHeading = "Books";
$errorMsg = '';
$books = [];

// --- Helper: safely fetch JSON data from URL ---
function safeFetchJson($url) {
    $context = stream_context_create(['http' => ['timeout' => 5]]);
    $response = @file_get_contents($url, false, $context);
    if (!$response) return null;
    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

// --- Helper: fetch books from all folders assigned to the grade ---
function fetchBooksForGrade($folders) {
    $allBooks = [];
    foreach ($folders as $folder) {
        $apiUrl = "https://360muslimexperts.com/panel/edu_api.php?type=books&grade=" . rawurlencode($folder);
        $data = safeFetchJson($apiUrl);
        if ($data && !isset($data['error'])) {
            foreach ($data as $item) {
                if ($item['type'] === 'file' && !empty($item['url'])) {
                    $allBooks[$item['url']] = [
                        'file' => $item['name'],
                        'url'  => $item['url'],
                        'size' => $item['size'] ?? 0,
                    ];
                }
            }
        }
    }
    return array_values($allBooks);
}

// --- Helper: render each list item ---
function generateListItem($file, $url, $size) {
    // Get the filename without the extension.
    $name = pathinfo($file, PATHINFO_FILENAME);
    $formattedSize = is_numeric($size) ? formatBytes($size) : 'N/A';

    $output = '<li class="item-list__item">';
    $output .= '<div class="item-list__content">';
    $output .= '<span class="item-list__title">' . htmlspecialchars($name) . '</span>';
    $output .= '<span class="file-meta">(' . $formattedSize . ')</span>';
    $output .= '</div>';
    $output .= '<div class="item-list__actions download-actions">';
    $output .= '<a href="/view-pdf.php?file=' . rawurlencode($url) . '" class="btn btn--primary" target="_blank" rel="noopener">';
    $output .= '<span class="btn__text">View PDF</span></a>';
    $output .= '<a href="' . htmlspecialchars($url) . '" class="btn btn--primary download-button" download>';
    $output .= '<span class="btn__text">Download PDF</span></a>';
    $output .= '</div></li>';
    return $output;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="stylesheet" href="/css/style.css" />
  <link rel="stylesheet" href="/css/header-footer.css" />
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-J9TMPM9XPW"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'G-J9TMPM9XPW');
  </script>
</head>
<body>
<?php include 'header.php'; ?>
<?php
$grade = $_GET['grade'] ?? null;
$pageHeading = "Books";
$breadcrumbItems = [
  "Books" => "/books"
];
if (!empty($grade)) {
  $pageHeading = "Books - Grade $grade";
  $breadcrumbItems["Grade $grade"] = null;
}
include("includes/breadcrumb.php");
?>
<section class="page-header centered">
  <h1><?php echo htmlspecialchars($pageHeading); ?></h1>
  <p class="subtitle">Explore and download official PCTB textbooks.</p>
</section>

<main class="container" id="main-content">
<section class="content centered">
<?php
if (!$gradeKey) {
    echo '<h2>Select a Grade</h2>';
    echo '<ul class="item-list grade-selector">';
    foreach ($visibleGrades as $grade) {
        echo '<li><a class="btn btn--primary" href="/books/' . $grade . '">Grade ' . $grade . '</a></li>';
    }
    echo '</ul>';
} elseif (!in_array($gradeKey, $visibleGrades)) {
    echo '<div class="message message--error">Invalid grade specified.</div>';
} else {
    $pageHeading = "Grade $gradeKey Books";
    $pageTitle = "$pageHeading - 360 Education";

    $books = fetchBooksForGrade($mergedFolders[$gradeKey]);

    if (empty($books)) {
        echo '<div class="message message--info">No books found for Grade ' . htmlspecialchars($gradeKey) . ' yet.</div>';
    } else {
        echo '<ul class="item-list" role="list">';
        foreach ($books as $entry) {
            echo generateListItem($entry['file'], $entry['url'], $entry['size']);
        }
        echo '</ul>';
    }
}
?>
</section>
</main>
<div class="back-button-container">
  <a href="/" class="btn btn--secondary">← Back to Home</a>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
