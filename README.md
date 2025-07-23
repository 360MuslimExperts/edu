# 360 Education Codebase Overview

## Purpose
This project provides free access to official Punjab Curriculum & Textbook Board (PCTB) books, notes, past papers, and study materials for grades 9-12 (Matric & FSC), in both Urdu and English mediums.

## Main Features
- **SEO Optimized**: Meta tags, keywords, and Open Graph/Twitter tags for better search visibility.
- **Sitemap & Robots**: `/sitemap.xml` and `/robots.txt` for search engine crawling.
- **Responsive Design**: Mobile-friendly layouts using CSS variables and media queries.
- **PDF Viewer**: In-browser PDF viewing via PDF.js (`view-pdf.php`).
- **Search Functionality**: Instant search for books and notes.
- **Download & View**: Direct download and online viewing of PDFs.
- **Accessibility**: Skip links, ARIA labels, and visually hidden elements for screen readers.

## Directory Structure
- `/books/` — PDF textbooks organized by grade.
- `/notes/` — PDF notes organized by grade and subject.
- `/assets/` — Images and static assets.
- `/white-css/` — Light theme CSS files.
- `/style.css`, `/header-footer.css`, `/view-pdf.css` — Main stylesheets.
- `/index.php` — Homepage.
- `/downloads.php` — All books and notes for download.
- `/grade_books.php` — Books by grade.
- `/notes.php` — Notes by grade and subject.
- `/view-pdf.php` — PDF viewer.
- `/header.php`, `/footer.php` — Shared header/footer.
- `/config.php` — Configuration (API keys, etc.).
- `/helpers.php` — Utility functions.
- `/404.php` — Custom error page.
- `/sitemap.xml` — Sitemap for SEO.
- `/robots.txt` — Robots file for SEO.

## Key Files Explained
- **index.php**: Main landing page, highlights features and navigation.
- **downloads.php**: Lists all available books and notes for download.
- **grade_books.php**: Shows books for a specific grade.
- **notes.php**: Shows notes for a grade/subject, with search.
- **view-pdf.php**: Secure PDF viewer using PDF.js.
- **header.php/footer.php**: Navigation and footer, included on all pages.
- **style.css/header-footer.css/view-pdf.css**: CSS for layout, colors, and components.
- **config.php**: Sensitive config (API keys, etc.).
- **helpers.php**: Functions for formatting, security, etc.
- **404.php**: Custom error page with navigation.
- **sitemap.xml/robots.txt**: SEO and search engine crawling.

## SEO Best Practices Used
- Descriptive meta titles and descriptions.
- Rich keywords including "2025 new books", "PCTB new syllabus", "notes", "past papers", etc.
- Canonical URLs and robots meta tags.
- Open Graph and Twitter meta tags for sharing.
- Sitemap and robots.txt for search engines.
- Alt attributes for images.
- Semantic HTML and accessibility.

## How to Add New Books/Notes
1. Place new PDF files in the appropriate `/books/{grade}/` or `/notes/{grade}/{subject}/` directory.
2. The system will automatically detect and list them for download/viewing.
3. Update `/sitemap.xml` if you add new pages.

## How to Run Locally
- Requires PHP (for server-side logic).
- Place the project in your web server's root directory.
- Access via `http://localhost/edu/index.php`.

## Security
- PDF viewing is restricted to `/books/` and `/notes/` directories.
- Security headers are set in PHP files.
- User input is sanitized.

## Contact & Support
- WhatsApp: [Contact Us](https://wa.me/923212584393)
- About: [About 360 Muslim Experts](https://360muslimexperts.com/about-us)

## Contributing
- Follow the existing file structure and naming conventions.
- Use semantic HTML and accessible components.
- Keep SEO meta tags up-to-date.

---

**For any questions, check the code comments or contact the maintainer.**
