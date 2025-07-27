[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

# 360 Education - Free PECTAA/PCTB Textbooks & Notes

Welcome to the codebase for **360 Education**, a web platform dedicated to providing free and easy access to official educational resources for students in Pakistan.

[![View Live Demo](https://img.shields.io/badge/Live_Demo-View_Site-brightgreen?style=for-the-badge&logo=google-chrome)](https://edu.360muslimexperts.com/)

---

<!-- Add a screenshot of the homepage here -->
<!-- ![Homepage Screenshot](https://example.com/screenshot.png) -->

## Purpose
This project provides free access to official **Punjab Curriculum & Textbook Board (PCTB)** books, notes, past papers, and study materials for grades 9-12 (Matric & FSC), in both Urdu and English mediums. The goal is to create a centralized, user-friendly, and fast platform for students to find the resources they need for the 2025 new syllabus and beyond.

## Key Features
- **Comprehensive Resource Hub**: Access textbooks, notes, past papers, and model papers for grades 9, 10, 11, and 12.
- **Blazing Fast PDF Viewer**: A custom-built, in-browser PDF viewer powered by PDF.js with features like zoom, page navigation, and fullscreen mode.
- **SEO Optimized**: Thoroughly optimized with meta tags, canonical URLs, Open Graph/Twitter cards, and a sitemap for maximum search engine visibility.
- **Fully Responsive**: A clean, mobile-first design that works beautifully on all devices, from desktops to smartphones.
- **Dynamic Content**: Books and notes are fetched dynamically from an external API, making content updates seamless without code changes.
- **Search & Filter**: Easily search for specific books or notes on the downloads page.
- **Accessibility Focused**: Built with accessibility in mind, featuring skip links, ARIA labels, and semantic HTML.
- **Newsletter Subscription**: Integrated with Brevo API for users to subscribe to updates.

## Technology Stack
- **Backend**: Vanilla PHP
- **Frontend**: HTML5, CSS3 (with CSS Variables), Vanilla JavaScript
- **PDF Rendering**: PDF.js
- **APIs**:
  - **Content API**: Fetches file lists from a private endpoint on `360muslimexperts.com`.
  - **Newsletter**: Brevo (formerly Sendinblue) API for managing email subscriptions.

## Getting Started

### Prerequisites
- A web server with PHP support (e.g., Apache, Nginx).

### Installation
1.  Clone the repository to your local machine:
    ```bash
    git clone https://github.com/ibtisam-shahid-kh/edu-360ME.git
    ```
2.  Place the project files in your web server's root directory (e.g., `/var/www/html/` or `htdocs/`).
3.  Create a `config.php` file in the root directory by copying the example structure below. This file is ignored by Git to protect your secrets.
    ```php
    <?php
    // config.php

    // Brevo API configuration for the newsletter
    define('BREVO_API_KEY', 'YOUR_BREVO_API_KEY');
    define('BREVO_LIST_ID', YOUR_BREVO_LIST_ID); // This is a number
    ```
4.  Access the project via your local server URL (e.g., `http://localhost/edu-360ME/`).

## API Dependency
This website is a **frontend client** and does not host the PDF files or the file listings itself. All content (lists of books and notes) is fetched from a private API endpoint:
`https://360muslimexperts.com/panel/edu_api.php`

This means that to run the project locally, the live API must be accessible. The PDF files themselves are also hosted on the `360muslimexperts.com` domain.

## Security
The application includes several security measures:
- **PDF Proxy**: The `view-pdf.php` script includes a proxy to securely fetch PDFs from the allowed domain (`360muslimexperts.com`), preventing Cross-Origin (CORS) issues while restricting access to other domains.
- **Security Headers**: Standard security headers like `Content-Security-Policy`, `X-Frame-Options`, and `X-XSS-Protection` are set to mitigate common web vulnerabilities.
- **Input Sanitization**: All user input (e.g., `$_GET` parameters) is sanitized using PHP filters to prevent XSS and other injection attacks.
- **Secret Management**: The `config.php` file, which contains API keys, is explicitly excluded from version control via `.gitignore`.

## Project Structure
```
edu-360ME/
├── assets/              # Images and static assets
├── index.php            # Homepage
├── books.php            # Lists books by grade
├── notes.php            # Lists notes by grade/subject
├── downloads.php        # Lists all available content
├── view-pdf.php         # The PDF.js viewer and proxy
├── header.php           # Shared header component
├── footer.php           # Shared footer component (includes newsletter logic)
├── subscribe.php        # Handles newsletter form submission
├── helpers.php          # Utility functions (e.g., formatBytes)
├── config.php           # (You must create this) API keys and secrets
├── sitemap.xml          # Sitemap for SEO
├── robots.txt           # Instructions for web crawlers
└── README.md            # This file
```

## Contributing
Contributions are welcome! If you have suggestions for improvements, please follow these steps:
1.  Fork the repository.
2.  Create a new branch (`git checkout -b feature/your-feature-name`).
3.  Make your changes.
4.  Commit your changes (`git commit -m 'Add some feature'`).
5.  Push to the branch (`git push origin feature/your-feature-name`).
6.  Open a Pull Request.

Please adhere to the existing code style and keep SEO and accessibility in mind.

## License
This project is licensed under the MIT License.

## Contact & Support
----
- **Primary Contact**: Ibtisam Shahid
- **Organization**: 360 Muslim Experts
- **WhatsApp**: +92 321 2584393

