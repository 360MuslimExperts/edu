<footer class="site-footer">
  <div class="container">
    
    <!-- <div class="newsletter" id="newsletter-section">
      <h3>Subscribe to our Newsletter</h3>
      <form id="newsletter-form" action="includes/subscribe.php" method="POST" autocomplete="off" novalidate>
        <label for="newsletter-email" class="visually-hidden">Email address</label>
        <input type="email" id="newsletter-email" name="email" placeholder="Enter your email" required>
        <button type="submit" class="btn btn--primary">Subscribe</button>
      </form>
      <div id="newsletter-message" class="newsletter-message" aria-live="polite"></div>
    </div> -->
    
    <div class="footer-social">
      <?php
      $socialLinks = [
        ['url' => 'https://www.facebook.com/360MuslimExpertsPak', 'icon' => 'facebook', 'label' => 'Visit our Facebook page'],
        ['url' => 'https://www.instagram.com/360_muslimexperts/', 'icon' => 'instagram', 'label' => 'Visit our Instagram profile'],
        ['url' => 'https://www.youtube.com/c/360MuslimExperts', 'icon' => 'youtube', 'label' => 'Visit our YouTube channel'],
        ['url' => 'https://wa.me/923212584393', 'icon' => 'whatsapp', 'label' => 'Contact us on WhatsApp']
      ];
      foreach ($socialLinks as $link) {
        echo '<a href="' . htmlspecialchars($link['url']) . '" target="_blank" aria-label="' . htmlspecialchars($link['label']) . '">';
        echo '<img src="https://cdn.simpleicons.org/' . htmlspecialchars($link['icon']) . '/ffffff" alt="' . ucfirst($link['icon']) . ' Logo" width="24" height="24" loading="lazy">';
        echo '</a>';
      }
      ?>
    </div>

    <p>&copy; <?php echo date("Y"); ?> |
      <a href="https://360muslimexperts.com" aria-label="Visit 360 Muslim Experts website" class="footer-link">360 Muslim Experts</a> | Made by <a href="https://salamprojects.github.io/" target="_blank" aria-label="Made by SalamProjects" class="footer-link">SalamProjects</a>
    </p>

    <div class="back-to-top">
      <button onclick="window.scrollTo({ top: 0, behavior: 'smooth' });" class="btn btn--secondary">Back to Top</button>
    </div>

  </div>
  <script>
    // Helper to set a cookie
    function setCookie(name, value, days) {
      let expires = "";
      if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }
    // Helper to get a cookie
    function getCookie(name) {
      const value = "; " + document.cookie;
      const parts = value.split("; " + name + "=");
      if (parts.length === 2) return parts.pop().split(";").shift();
    }

    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('newsletter-form');
      const msgDiv = document.getElementById('newsletter-message');
      const newsletterSection = document.getElementById('newsletter-section');

      // The newsletter section is hidden if the 'newsletter_subscribed' cookie is set.
      if (getCookie('newsletter_subscribed') === '1') {
        newsletterSection.style.display = 'none';
      }

      form.addEventListener('submit', function (e) {
        e.preventDefault();
        msgDiv.textContent = '';
        msgDiv.className = 'newsletter-message'; // reset

        const formData = new FormData(form);
        fetch('subscribe.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            msgDiv.textContent = data.message;
            msgDiv.classList.add('success');
            form.reset();
            setCookie('newsletter_subscribed', '1', 365);
            setTimeout(() => {
              newsletterSection.style.display = 'none';
            }, 1500);
          } else {
            msgDiv.textContent = data.message;
            msgDiv.classList.add('error');
          }
        })
        .catch(() => {
          msgDiv.textContent = 'An error occurred. Please try again.';
          msgDiv.classList.add('error');
        });
      });
    });
  </script>
</footer>
