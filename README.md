# Posted At

A tiny PHP app that reads the structured `datePosted` metadata from a public LinkedIn job page. It is designed for normal PHP hosting, where the server can retrieve the public LinkedIn page without the browser’s cross-origin restrictions.

## Deploy on a PHP website

1. Upload these files to a PHP-enabled directory on your site.
2. Visit `index.html` in that directory.
3. Ensure the host runs PHP 7.1+ with the `curl` and `dom` extensions enabled.

No database, build process, or API key is required. The server validates requests to LinkedIn job URLs only, uses HTTPS, and returns just the extracted timestamp.

## Important limitation

The result reports the `datePosted` value in the page's current JSON-LD metadata. It can indicate a reposted or renewed listing; it is not independent proof of the first time an employer advertised a role.
