<?php
// Placeholder for search results
$query = htmlspecialchars($_GET['query'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Results</title>
  <meta name="robots" content="noindex, follow">
  <link rel="canonical" href="https://edu.360muslimexperts.com/search_results.php" />
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="header-footer.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>
  <main class="container">
    <h1>Search Results</h1>
    <p>Showing results for: <strong><?php echo $query; ?></strong></p>
    <!-- Add logic to display search results here -->
  </main>
  <?php include 'includes/footer.php'; ?>
</body>
</html>
