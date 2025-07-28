<?php
require_once __DIR__ . '/helpers.php';

// --- Configuration ---
$visibleGrades = ['9', '10', '11', '12'];
$apiBase = 'https://360muslimexperts.com/panel/edu_api.php';

// --- Get parameters from URL ---
$grade = filter_input(INPUT_GET, 'grade', FILTER_SANITIZE_STRING);
$subject = filter_input(INPUT_GET, 'subject', FILTER_SANITIZE_STRING);

// --- Determine page state ---
$errorMsg = '';
$items = [];
$mode = 'grades'; // Default mode

if ($grade && $subject) {
    $mode = 'files';
} elseif ($grade) {
    $mode = 'subjects';
}

// --- Fetch data based on mode ---
if ($mode !== 'grades') {
    if (!in_array($grade, $visibleGrades)) {
        $errorMsg = 'Invalid grade specified.';
        $mode = 'error';
    } else {
        $queryParams = ['type' => 'notes', 'grade' => $grade];
        if ($mode === 'files') {
            $queryParams['subject'] = $subject;
        }
        $apiUrl = $apiBase . '?' . http_build_query($queryParams);

        $context = stream_context_create(['http' => ['timeout' => 5]]);
        $response = @file_get_contents($apiUrl, false, $context);
        $data = $response ? json_decode($response, true) : null;

        if (!$data || !is_array($data)) {
            $errorMsg = 'No notes available yet.';
        } elseif (isset($data['error'])) {
            $errorMsg = htmlspecialchars($data['error']);
        } else {
            $filterType = ($mode === 'files') ? 'file' : 'folder';
            $items = array_filter($data, fn($item) => $item['type'] === $filterType);
            if (empty($items) && $mode === 'files') {
                $errorMsg = 'No notes found for this subject yet.';
            } elseif (empty($items) && $mode === 'subjects') {
                $errorMsg = 'No subjects found for this grade yet.';
            }
        }
    }
}

// --- Page Title & Breadcrumbs ---
$pageTitle = "Notes";
$breadcrumbs = [
    ['url' => '/', 'text' => 'Home'],
    ['url' => '/notes', 'text' => 'Notes']
];

if ($mode === 'subjects') {
    $pageTitle = "Grade " . htmlspecialchars($grade) . " Subjects - Notes";
    $breadcrumbs[] = ['text' => 'Grade ' . htmlspecialchars($grade)];
} elseif ($mode === 'files') {
    $pageTitle = "Grade " . htmlspecialchars($grade) . " " . htmlspecialchars(ucfirst($subject)) . " Notes";
    $breadcrumbs[] = ['url' => '/notes/' . urlencode($grade), 'text' => 'Grade ' . htmlspecialchars($grade)];
    $breadcrumbs[] = ['text' => htmlspecialchars(ucfirst($subject))];
} else {
    $pageTitle = "Select Grade - Notes";
}

// --- Back Button Logic ---
$backLink = 'downloads.php';
$backLinkText = '← Back to All Downloads';

if ($mode === 'files') {
    $backLink = "/notes/" . urlencode($grade);
    $backLinkText = "← Back to Subjects for Grade " . htmlspecialchars($grade);
} elseif ($mode === 'subjects') {
    $backLink = "/notes";
    $backLinkText = "← Back to Grade Selection";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?php echo htmlspecialchars($pageTitle); ?> - 360 Education</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/style.css" />
    <link rel="stylesheet" href="/header-footer.css" />
</head>
<body>
<?php include 'header.php'; ?>

<nav class="breadcrumb">
  <div class="container">
    <?php foreach ($breadcrumbs as $i => $crumb): ?>
      <?php if ($i > 0) echo ' &gt; '; ?>
      <?php if (isset($crumb['url']) && $i < count($breadcrumbs) - 1): ?>
        <a href="<?php echo $crumb['url']; ?>"><?php echo $crumb['text']; ?></a>
      <?php else: ?>
        <span><?php echo $crumb['text']; ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</nav>

<main class="container" id="main-content">
    <section class="page-header centered">
        <h1>
            <?php
            if ($mode === 'grades') echo "Select Grade";
            elseif ($mode === 'subjects') echo "Subjects for Grade " . htmlspecialchars($grade);
            elseif ($mode === 'files') echo "Notes for Grade " . htmlspecialchars($grade) . " - " . htmlspecialchars(ucfirst($subject));
            ?>
        </h1>
        <p class="subtitle">
            <?php
            if ($mode === 'grades') echo "Choose a grade to view available subjects.";
            elseif ($mode === 'subjects') echo "Choose a subject to view notes.";
            elseif ($mode === 'files') echo "Download or view chapter-wise notes as PDF.";
            ?>
        </p>
    </section>
    <section class="content centered">
        <?php if ($errorMsg): ?>
            <div class="message message--error"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php elseif ($mode === 'grades'): ?>
            <ul class="item-list grade-selector">
                <?php foreach ($visibleGrades as $g): ?>
                <li class="item-list__item">
                    <a href="/notes/<?php echo urlencode($g); ?>" class="btn btn--primary">
                        Grade <?php echo htmlspecialchars($g); ?> Notes
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($mode === 'subjects'): ?>
            <ul class="item-list">
                <?php foreach ($items as $item): ?>
                <li class="item-list__item">
                    <a href="/notes/<?php echo urlencode($grade); ?>/<?php echo urlencode($item['name']); ?>" class="btn btn--primary">
                        <?php echo htmlspecialchars(ucfirst($item['name'])); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($mode === 'files'): ?>
            <ul class="item-list">
                <?php foreach ($items as $file):
                    $display = ucwords(str_replace(['_', '-'], ' ', pathinfo($file['name'], PATHINFO_FILENAME)));
                    $formattedSize = $file['size'] ? formatBytes($file['size']) : 'N/A';
                ?>
                <li class="item-list__item">
                    <div class="item-list__content">
                        <span class="item-list__title"><?php echo htmlspecialchars($display); ?></span>
                        <span class="file-meta">(<?php echo $formattedSize; ?>)</span>
                    </div>
                    <div class="item-list__actions download-actions">
                        <a href="view-pdf.php?file=<?php echo rawurlencode($file['url']); ?>" class="btn btn--primary" target="_blank" rel="noopener">View PDF</a>
                        <a href="<?php echo htmlspecialchars($file['url']); ?>" class="btn btn--primary" download>Download PDF</a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>
<div class="back-button-container">
    <a href="<?php echo $backLink; ?>" class="btn btn--secondary"><?php echo $backLinkText; ?></a>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
