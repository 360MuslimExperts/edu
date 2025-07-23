document.addEventListener('DOMContentLoaded', () => {
  // --- Search Functionality ---
  const searchInput = document.querySelector('#search-input');
  const items = document.querySelectorAll('.download-item');
  const noResultsMessage = document.querySelector('#no-results-message');
  // Add clear button inside input
  const clearButton = document.createElement('button');
  clearButton.textContent = '✕';
  clearButton.classList.add('btn', 'btn--secondary');
  clearButton.style.marginLeft = '8px';
  clearButton.style.fontSize = '1.1em';
  clearButton.style.padding = '0.2em 0.7em';
  clearButton.setAttribute('aria-label', 'Clear search');
  searchInput.parentNode.appendChild(clearButton);

  let debounceTimeout;
  const debounce = (callback, delay) => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(callback, delay);
  };

  searchInput.addEventListener('input', () => {
    debounce(() => {
      const query = searchInput.value.trim().toLowerCase();
      let hasResults = false;
      items.forEach(item => {
        const titleElement = item.querySelector('.item-title');
        const title = titleElement.textContent;
        const matches = title.toLowerCase().includes(query);
        if (matches && query.length > 0) {
          hasResults = true;
          // Highlight match
          titleElement.innerHTML = title.replace(
            new RegExp(query, 'gi'),
            match => `<mark style="background:var(--clr-accent);color:var(--clr-dark);border-radius:4px;">${match}</mark>`
          );
        } else {
          titleElement.innerHTML = title;
        }
        item.style.display = matches || query.length === 0 ? '' : 'none';
      });
      noResultsMessage.style.display = hasResults || query.length === 0 ? 'none' : 'block';
    }, 200);
  });

  clearButton.addEventListener('click', () => {
    searchInput.value = '';
    items.forEach(item => {
      item.style.display = '';
      const titleElement = item.querySelector('.item-title');
      titleElement.innerHTML = titleElement.textContent;
    });
    noResultsMessage.style.display = 'none';
    searchInput.focus();
  });

  // --- Scroll-to-top Button ---
  const scrollToTopButton = document.createElement('button');
  scrollToTopButton.classList.add('btn', 'btn--primary');
  scrollToTopButton.style.position = 'fixed';
  scrollToTopButton.style.bottom = '2.5rem';
  scrollToTopButton.style.right = '2.5rem';
  scrollToTopButton.style.zIndex = '1001';
  scrollToTopButton.style.display = 'none';
  scrollToTopButton.style.borderRadius = '50%';
  scrollToTopButton.style.width = '48px';
  scrollToTopButton.style.height = '48px';
  scrollToTopButton.style.boxShadow = '0 2px 8px #0003';
  scrollToTopButton.innerHTML = '↑';
  document.body.appendChild(scrollToTopButton);

  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      scrollToTopButton.style.display = 'block';
      scrollToTopButton.style.opacity = '1';
    } else {
      scrollToTopButton.style.opacity = '0';
      setTimeout(() => { scrollToTopButton.style.display = 'none'; }, 200);
    }
  });

  scrollToTopButton.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // --- Scroll Progress Bar ---
  const progressBar = document.createElement('div');
  progressBar.classList.add('scroll-progress');
  progressBar.style.position = 'fixed';
  progressBar.style.top = '0';
  progressBar.style.left = '0';
  progressBar.style.height = '4px';
  progressBar.style.backgroundColor = 'var(--clr-accent)';
  progressBar.style.zIndex = '1000';
  progressBar.style.transition = 'width 0.2s ease';
  progressBar.style.width = '0';
  document.body.appendChild(progressBar);

  window.addEventListener('scroll', () => {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrollPercent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    progressBar.style.width = `${scrollPercent}%`;
  });
});
