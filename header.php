<?php
function isActive($path) {
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return rtrim($currentPath, '/') === rtrim($path, '/') ? 'active' : '';
}
?>
<header class="site-header" role="banner">
  <div class="container">
    <a href="/" class="logo-link" aria-label="360 Education Home">
      <img src="https://360muslimexperts.com/assets/school-logo.png" alt="360 Muslim Experts Logo" class="logo" width="50" height="50" loading="lazy" />
      <span class="site-title">360 Education</span>
    </a>
    <!-- Hamburger menu toggle -->
    <input type="checkbox" id="menu-toggle" class="menu-toggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="navbar-menu" />
    <label for="menu-toggle" class="hamburger" aria-label="Toggle navigation menu">
      <span></span>
      <span></span>
      <span></span>
    </label>
    <!-- Navigation menu -->
    <nav class="navbar" id="navbar-menu" role="navigation" aria-label="Main navigation">
      <ul>
        <li>
          <a href="/" class="<?php echo isActive('/'); ?>" <?php if (isActive('/')) echo 'aria-current="page"'; ?>>Home</a>
        </li>
        <li>
          <a href="/books" class="<?php echo isActive('/books'); ?>" <?php if (isActive('/books')) echo 'aria-current="page"'; ?>>Books</a>
        </li>
        <li>
          <a href="/notes" class="<?php echo isActive('/notes'); ?>" <?php if (isActive('/notes')) echo 'aria-current="page"'; ?>>Notes</a>
        </li>
        <!-- Optionally add a syllabus link if available -->
        <!--
        <li>
          <a href="/syllabus" class="<?php echo isActive('/syllabus'); ?>" <?php if (isActive('/syllabus')) echo 'aria-current="page"'; ?>>2025 Syllabus</a>
        </li>
        -->
        <li>
          <a href="https://ibt.360muslimexperts.com" target="_blank" rel="noopener noreferrer" aria-label="About 360 Muslim Experts">About</a>
        </li>
        <li>
          <a href="https://wa.me/923212584393" target="_blank" rel="noopener noreferrer" aria-label="Contact on WhatsApp">Contact</a>
        </li>
      </ul>
    </nav>
  </div>
</header>
