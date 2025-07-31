<?php
// Set the 404 status code
http_response_code(404);

// Define page title for the header
$pageTitle = "404 Page Not Found - 360 Education";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle; ?></title>
  <link rel="stylesheet" href="/css/style.css">
  <link rel="stylesheet" href="/css/header-footer.css">
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <meta name="robots" content="noindex"> <!-- Tell search engines not to index this page -->
  <meta name="description" content="Page not found. 360 Education – Free 2025 PCTB new syllabus, textbooks, notes, past papers, model papers, solved exercises, and study material for Matric, FSC, and all grades.">
  <meta name="keywords" content="404, not found, 2025 new books, pctb new syllabus, notes, PCTB, Punjab Textbook Board, free download, latest syllabus, updated books, study material, past papers, model papers, guess papers, solved exercises, pdf, all subjects, Urdu notes, English notes, Matric, FSC, 2025 notes, 2025 syllabus, Punjab curriculum, textbook download, PCTB 2025">
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-J9TMPM9XPW"></script>
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
  <a href="#main-content" class="visually-hidden focusable skip-link">Skip to main content</a>
  <?php include 'includes/header.php'; ?>

  <main id="main-content" class="container">
    <section class="error-page">
      <h1 class="error-title">404</h1>
      <p class="error-message">Oops! The page you are looking for does not exist. Try searching for 2025 new books, PCTB new syllabus, notes, past papers, or other study material using the navigation below.</p>
      <p class="error-suggestion">Go back to one of the links below:</p>

      <!-- Navigation Links -->
      <div class="error-links">
        <a href="index.php" class="btn btn--secondary">Go to Home</a>
        <a href="downloads.php" class="btn btn--secondary">Browse Downloads</a>
        <a href="https://wa.me/923212584393" class="btn btn--secondary">Contact Us</a>
      </div>
    </section>
  </main>

  <?php include 'includes/footer.php'; ?>
</body>
</html>
