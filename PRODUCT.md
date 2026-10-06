# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Stack

Delegated: a dependency-free static HTML/CSS/JavaScript interface with a small PHP extraction endpoint, chosen because it can be uploaded to ordinary PHP hosting and needs no build step.

## Users

People checking when a public LinkedIn job listing was posted, often while researching a role or recording it in another system.

## Product Purpose

Accept a LinkedIn job URL and reveal the page's structured `datePosted` timestamp in a readable local format and in its original UTC value.

## Positioning

The browser never needs permission to retrieve LinkedIn: the optional same-origin PHP endpoint fetches the public page server-side and extracts only the structured posting date.

## Capabilities and Constraints

- Accepts only public `linkedin.com/jobs/view/` URLs.
- The PHP endpoint follows redirects, uses a short timeout, and returns a small JSON response.
- The app reports that a timestamp is LinkedIn's current `datePosted` value and may reflect a repost or renewal.
- GitHub Pages cannot run PHP; it can use a separately hosted PHP endpoint when configured.

## Evidence on Hand

The supplied example URL publicly exposes a JSON-LD `JobPosting` object with a `datePosted` field.

## Product Principles

- Make the answer and its provenance obvious.
- Fail with a concrete recovery path.
- Keep the deployment footprint small and inspectable.
- Do not overstate what the timestamp proves.

## Accessibility & Inclusion

Use semantic controls, visible keyboard focus, readable contrast, status announcements, and a responsive single-column layout.
