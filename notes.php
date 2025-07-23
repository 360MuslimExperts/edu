<?php
include 'header.php';
require_once __DIR__ . '/helpers.php';

$notesRoot = __DIR__ . "/notes";
$grade = isset($_GET['grade']) ? basename($_GET['grade']) : '';
$subject = isset($_GET['subject']) ? basename($_GET['subject']) : '';

$pageTitle = "Notes";
$errorMsg = '';
$items = [];
$mode = 'grades'; // grades, subjects, or files

if ($grade && !$subject) {
    // List subjects for this grade
    $subjectsDir = "$notesRoot/$grade";
    if (is_dir($subjectsDir)) {
        $dirs = array_filter(scandir($subjectsDir), function($d) use ($subjectsDir) {
            return $d[0] !== '.' && is_dir("$subjectsDir/$d");
        });
        if ($dirs) {
            $mode = 'subjects';
            $items = $dirs;
        } else {
            $errorMsg = "No subjects found for Grade <b>" . htmlspecialchars($grade) . "</b>.";
        }
    } else {
        $errorMsg = "No notes found for Grade <b>" . htmlspecialchars($grade) . "</b>.";
    }
} elseif ($grade && $subject) {
    // List files for this grade/subject
    $notesDir = "$notesRoot/$grade/$subject";
    if (is_dir($notesDir)) {
        $files = array_filter(scandir($notesDir), function($f) use ($notesDir) {
            return is_file("$notesDir/$f") && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf';
        });
        if ($files) {
            $mode = 'files';
            $items = $files;
        } else {
            $errorMsg = "No notes found for Grade <b>" . htmlspecialchars($grade) . "</b>, Subject <b>" . htmlspecialchars(ucfirst($subject)) . "</b>.";
        }
    } else {
        $errorMsg = "No notes found for Grade <b>" . htmlspecialchars($grade) . "</b>, Subject <b>" . htmlspecialchars(ucfirst($subject)) . "</b>.";
    }
} else {
    // List grades
    if (is_dir($notesRoot)) {
        $dirs = array_filter(scandir($notesRoot), function($d) use ($notesRoot) {
            return $d[0] !== '.' && is_dir("$notesRoot/$d");
        });
        if ($dirs) {
            $mode = 'grades';
            $items = $dirs;
        } else {
            $errorMsg = "No grades found in notes.";
        }
    } else {
        $errorMsg = "Notes directory not found.";
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
    <?php if ($mode === 'files' && !$errorMsg): ?>
    <section id="notes-search-section">
        <input type="text" id="notes-search-input" placeholder="Search notes..." aria-label="Search notes" autocomplete="off" />
        <p id="notes-no-results-message">No notes found.</p>
    </section>
    <?php endif; ?>
    <section class="content centered">
        <?php if ($errorMsg): ?>
            <div class="message message--error"><?php echo $errorMsg; ?></div>
        <?php elseif ($mode === 'grades'): ?>
            <ul class="item-list" role="list">
                <?php foreach ($items as $g): ?>
                    <li class="item-list__item">
                        <a href="notes.php?grade=<?php echo urlencode($g); ?>" class="btn btn--secondary">
                            Notes for Grade <?php echo htmlspecialchars($g); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($mode === 'subjects'): ?>
            <ul class="item-list" role="list">
                <?php foreach ($items as $s): ?>
                    <li class="item-list__item">
                        <a href="notes.php?grade=<?php echo urlencode($grade); ?>&subject=<?php echo urlencode($s); ?>" class="btn btn--primary">
                            <?php echo htmlspecialchars(ucfirst($s)); ?> Notes for Grade <?php echo htmlspecialchars($grade); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($mode === 'files'): ?>
            <ul class="item-list" role="list">
                <?php foreach ($items as $file):
                    $display = ucwords(str_replace(['_', '-'], ' ', pathinfo($file, PATHINFO_FILENAME)));
                    $fileUrl = "notes/$grade/$subject/" . rawurlencode($file);
                    $viewUrl = "view-pdf.php?file=" . rawurlencode("notes/$grade/$subject/$file");
                    $fileSize = filesize("$notesRoot/$grade/$subject/$file");
                    $formattedSize = $fileSize ? formatBytes($fileSize) : 'N/A';
                ?>
                <li class="item-list__item">
                    <div class="item-list__content">
                        <span class="item-list__title"><?php echo htmlspecialchars($display); ?></span>
                        <span class="file-meta">(<?php echo $formattedSize; ?>)</span>
                    </div>
                    <div class="item-list__actions download-actions">
                        <a href="<?php echo $viewUrl; ?>" class="btn btn--primary" target="_blank" rel="noopener noreferrer" aria-label="View <?php echo $display; ?>">
                            <span class="btn__text">View PDF</span>
                        </a>
                        <a href="<?php echo htmlspecialchars($fileUrl); ?>" class="btn btn--primary download-button" download aria-label="Download <?php echo $display; ?> (<?php echo $formattedSize; ?>)">
                            <span class="btn__text">Download PDF</span>
                        </a>
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