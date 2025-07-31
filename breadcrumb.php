<nav class="breadcrumb">
  <div class="container">
    <a href="/">Home</a>
    <?php
    if (!empty($breadcrumbItems)) {
      foreach ($breadcrumbItems as $label => $link) {
        if ($link === null) {
          echo " &gt; <span>" . htmlspecialchars($label) . "</span>";
        } else {
          echo " &gt; <a href=\"" . htmlspecialchars($link) . "\">" . htmlspecialchars($label) . "</a>";
        }
      }
    }
    ?>
  </div>
</nav>
