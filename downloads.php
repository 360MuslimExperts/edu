<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
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
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="header-footer.css" />
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
<?php include 'header.php'; ?>
<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
?>
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
      // TODO: Move $baseDir, $folders, $gradeDescriptions to a config file/array
      $baseDir = 'books';
      $webBooksBase = '/education/books'; // new web root for books
      // Define the folders and their display names
      $folders = [
          '9' => 'Grade 9',
          '10' => 'Grade 10',
          '9-10' => 'Grade (9 & 10)',
          '11' => 'Grade 11',
          '12' => 'Grade 12',
          '11-12' => 'Grade (11 & 12)'
      ];
      $gradeDescriptions = [
        '9' => 'All official textbooks and guides for Grade 9.',
        '10' => 'All official textbooks and guides for Grade 10.',
        '9-10' => 'Combined resources for Grades 9 & 10.',
        '11' => 'All official textbooks and guides for Grade 11 (FSc Part 1).',
        '12' => 'All official textbooks and guides for Grade 12 (FSc Part 2).',
        '11-12' => 'Combined resources for Grades 11 & 12.'
      ];
      foreach ($folders as $folder => $heading) {
        $isLastFolder = ($folder === array_key_last($folders)); // Check if it's the last folder
        $path = "$baseDir/$folder";
        $webPath = "$webBooksBase/$folder";
        if (is_dir($path)) {
          echo "<section class='download-section' aria-labelledby='section-$folder'>";
          echo "<h2 id='section-$folder'>$heading</h2>";
          if (isset($gradeDescriptions[$folder])) {
            // Use class instead of inline style
            echo "<p class='section-desc' style='color:var(--clr-text-secondary);margin-bottom:1rem;font-size:1rem;'>" . $gradeDescriptions[$folder] . "</p>";
          }

          // Scan the directory for files
          $files = array_filter(scandir($path), function ($file) use ($path) {
              return is_file("$path/$file");
          });

          // Check if any files were found for this grade
          if (empty($files)) {
              echo "<p class='message message--info'>No books found for this grade yet.</p>";
          } else {
              $fileList = []; // Array to hold file details for potential sorting later

              // Process each item found in the directory
              foreach ($files as $file) {
                $filePath = "$path/$file";
                $filename = pathinfo($file, PATHINFO_FILENAME); // Get filename without extension
                // Clean up filename (optional, similar to grade_books.php if needed)
                // $filename = ucwords(strtolower(str_ireplace(['-'.$folder, '_'.$folder.'th'], '', $filename)));
                // $filename = trim($filename, ' -_');

                $extension = strtoupper(pathinfo($file, PATHINFO_EXTENSION)); // Get extension, uppercase
                // Check file existence/readability before getting size
                $filesize = (is_readable($filePath)) ? filesize($filePath) : false;

                // Add file details to our list (only if PDF, for example)
                $fileList[] = [
                    'path' => $filePath,
                    'name' => $filename,
                    'ext' => $extension,
                    'size' => $filesize !== false ? formatBytes($filesize) : 'N/A' // Format size or show N/A on error
                ];
              }

              // Optional: Sort files alphabetically by name (case-insensitive)
              usort($fileList, function($a, $b) { return strcasecmp($a['name'], $b['name']); });

              echo "<h3 class='visually-hidden'>List of downloadable books for $heading</h3>";
              echo "<ul class='download-list' role='list'>";
              // Output each file as a list item
              foreach ($fileList as $fileData) {
                  $safeName = htmlspecialchars($fileData['name']);
                  $safePath = htmlspecialchars($webPath . '/' . rawurlencode($fileData['name'] . '.' . strtolower($fileData['ext'])));
                  echo "<li class='download-item'>";
                  echo "<a href='{$safePath}' download title='Download {$safeName}' aria-label='Download {$safeName} ({$fileData['ext']}, {$fileData['size']})'>";
                  echo "<span class='item-title'>{$safeName}</span>";
                  echo "<span class='file-meta'>({$fileData['ext']}, {$fileData['size']})</span>";
                  // SVG download icon for crispness and accessibility
                  // Use currentColor for stroke if defined in CSS, otherwise keep explicit color
                  echo "<span class='download-icon' aria-hidden='true' style='margin-left:0.5em;display:inline-flex;align-items:center;'>";
                  echo '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" style="display:block" xmlns="http://www.w3.org/2000/svg"><path d="M10 3v10m0 0l-4-4m4 4l4-4M4 17h12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
                  echo "</span>";
                  echo "</a>";
                  echo "</li>";
              }
              echo "</ul>";
          }
          echo "</section>";
          // Add divider using class, only if not the last section
          if (!$isLastFolder) {
              echo "<hr class='section-divider' />"; // Style this class in style.css
          }
        } else {
            echo "<p class='message message--error'>Directory for $heading not found.</p>";
        }
      }
    ?>
    <?php
      // Add notes section
      $notesBase = __DIR__ . '/notes';
      $notesWebBase = '/education/notes'; // new web root for notes
      $grades = [9, 10, 11, 12];
      foreach ($grades as $grade) {
        $gradeDir = "$notesBase/$grade";
        if (is_dir($gradeDir)) {
          $subjects = array_filter(scandir($gradeDir), function($d) use ($gradeDir) {
            return $d[0] !== '.' && is_dir("$gradeDir/$d");
          });
          foreach ($subjects as $subject) {
            $subjectDir = "$gradeDir/$subject";
            $files = array_filter(scandir($subjectDir), function($f) use ($subjectDir) {
              return is_file("$subjectDir/$f") && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf';
            });
            if (!empty($files)) {
              echo "<section class='download-section' aria-labelledby='notes-$grade-$subject'>";
              echo "<h2 id='notes-$grade-$subject'>Notes: Grade $grade - " . htmlspecialchars(ucfirst($subject)) . "</h2>";
              echo "<ul class='download-list' role='list'>";
              foreach ($files as $file) {
                $filename = ucwords(str_replace(['_', '-'], ' ', pathinfo($file, PATHINFO_FILENAME)));
                $filePath = "$notesWebBase/$grade/$subject/" . rawurlencode($file);
                $fileSize = filesize("$subjectDir/$file");
                $formattedSize = $fileSize ? formatBytes($fileSize) : 'N/A';
                echo "<li class='download-item'>";
                echo "<a href='" . htmlspecialchars($filePath) . "' download title='Download $filename' aria-label='Download $filename (PDF, $formattedSize)'>";
                echo "<span class='item-title'>$filename</span>";
                echo "<span class='file-meta'>(PDF, $formattedSize)</span>";
                echo "<span class='download-icon' aria-hidden='true'><svg width='20' height='20' viewBox='0 0 20 20" fill="none" style="display:block" xmlns="http://www.w3.org/2000/svg"><path d='M10 3v10m0 0l-4-4m4 4l4-4M4 17h12' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/></svg></span>";
                echo "</a>";
                echo "</li>";
              }
              echo "</ul>";
              echo "</section>";
            }
          }
        }
      }
    ?>
    <script>
    // Unified search for books and notes
    document.addEventListener('DOMContentLoaded', function() {
      var searchInput = document.getElementById('search-input');
      var allSections = Array.from(document.querySelectorAll('.download-section'));
      var allLists = Array.from(document.querySelectorAll('.download-list'));
      var noResults = document.getElementById('no-results-message');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          var filter = searchInput.value.trim().toLowerCase();
          var found = 0;
          allLists.forEach(function(list) {
            Array.from(list.children).forEach(function(li) {
              var text = li.textContent.toLowerCase();
              if (text.includes(filter)) {
                li.style.display = '';
                found++;
              } else {
                li.style.display = 'none';
              }
            });
          });
          // Hide sections with no visible items
          allSections.forEach(function(section) {
            var visible = section.querySelectorAll('.download-list li:not([style*="display: none"])').length;
            section.style.display = visible ? '' : 'none';
          });
          noResults.style.display = found === 0 ? 'block' : 'none';
        });
      }
    });
    </script>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
