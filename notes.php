<?php
include 'header.php';
require_once __DIR__ . '/helpers.php';

$grade = isset($_GET['grade']) ? basename($_GET['grade']) : '';
$subject = isset($_GET['subject']) ? basename($_GET['subject']) : '';

$pageTitle = "Notes";
$errorMsg = '';
$items = [];
$mode = 'grades';

$apiBase = 'https://360muslimexperts.com/panel/notes_api.php';
$query = http_build_query(array_filter(['grade' => $grade, 'subject' => $subject]));
$apiUrl = $apiBase . ($query ? "?$query" : '');

$response = @file_get_contents($apiUrl);
$data = json_decode($response, true);

$data = json_decode($response, true);
if (!$response || !$data || !is_array($data)) {
    $errorMsg = 'Could not load notes.';
} elseif (isset($data['error'])) {
    $errorMsg = $data['error'];
}
 else {
    if ($grade && $subject) {
        $mode = 'files';
        $items = array_filter($data, fn($item) => $item['type'] === 'file');
    } elseif ($grade) {
        $mode = 'subjects';
        $items = array_filter($data, fn($item) => $item['type'] === 'folder');
    } else {
        $mode = 'grades';
        $items = array_filter($data, fn($item) => $item['type'] === 'folder');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Notes<?php if($grade) echo " - Grade $grade"; if($subject) echo " - " . ucfirst($subject); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="header-footer.css" />
</head>
<body>
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
            <div class="message message--error"><?php echo $errorMsg; ?></div>
        <?php elseif ($mode === 'grades'): ?>
            <ul class="item-list">
                <?php foreach ($items as $item): ?>
                <li class="item-list__item">
                    <a href="notes.php?grade=<?php echo urlencode($item['name']); ?>" class="btn btn--secondary">
                        Notes for Grade <?php echo htmlspecialchars($item['name']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($mode === 'subjects'): ?>
            <ul class="item-list">
                <?php foreach ($items as $item): ?>
                <li class="item-list__item">
                    <a href="notes.php?grade=<?php echo urlencode($grade); ?>&subject=<?php echo urlencode($item['name']); ?>" class="btn btn--primary">
                        <?php echo htmlspecialchars(ucfirst($item['name'])); ?> Notes for Grade <?php echo htmlspecialchars($grade); ?>
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
                        <a href="<?php echo $file['url']; ?>" class="btn btn--primary" target="_blank" rel="noopener noreferrer">View PDF</a>
                        <a href="<?php echo $file['url']; ?>" class="btn btn--primary" download>Download PDF</a>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>
<div class="back-button-container">
    <a href="index.php" class="btn btn--secondary">← Back to Home</a>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
