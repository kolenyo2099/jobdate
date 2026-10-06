<?php
declare(strict_types=1);

/*
 * Server-side LinkedIn JobPosting metadata reader.
 * Requires PHP's cURL and DOM extensions, which are enabled on most shared hosts.
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['error' => 'Use POST with a JSON body containing a LinkedIn job URL.']);
}

if (!extension_loaded('curl') || !class_exists('DOMDocument')) {
    respond(500, ['error' => 'This server needs PHP cURL and DOM enabled.']);
}

$input = json_decode((string) file_get_contents('php://input'), true);
$url = is_array($input) ? trim((string) ($input['url'] ?? '')) : '';

if (!isLinkedInJobUrl($url)) {
    respond(422, ['error' => 'Provide a public https://*.linkedin.com/jobs/view/ URL.']);
}

$html = fetchPage($url);
$datePosted = extractDatePosted($html);

if ($datePosted === null) {
    respond(404, ['error' => 'No JobPosting datePosted value was found on this public page.']);
}

respond(200, ['datePosted' => $datePosted]);

function respond(int $status, array $body) {
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function isLinkedInJobUrl(string $url): bool {
    if ($url === '' || strlen($url) > 2048) {
        return false;
    }
    $parts = parse_url($url);
    if (!is_array($parts) || ($parts['scheme'] ?? '') !== 'https') {
        return false;
    }
    $host = strtolower((string) ($parts['host'] ?? ''));
    $validHost = preg_match('/(^|\\.)linkedin\\.com$/', $host) === 1;
    $path = (string) ($parts['path'] ?? '');
    return $validHost && preg_match('#^/jobs/view(?:/|$)#', $path) === 1;
}

function fetchPage(string $url): string {
    $curl = curl_init($url);
    if ($curl === false) {
        respond(500, ['error' => 'Could not initialize the retrieval service.']);
    }

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 18,
        CURLOPT_ENCODING => '',
        CURLOPT_USERAGENT => 'PostedAtMetadataReader/1.0 (+https://github.com/kolenyo2099/jobdate)',
        CURLOPT_HTTPHEADER => ['Accept: text/html,application/xhtml+xml'],
    ]);

    $body = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $effectiveUrl = (string) curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    $error = curl_error($curl);
    if (!is_string($body) || $status < 200 || $status >= 300 || !isLinkedInJobUrl($effectiveUrl)) {
        respond(502, ['error' => $error !== '' ? 'LinkedIn retrieval failed: ' . $error : 'LinkedIn did not return a usable public job page.']);
    }
    return $body;
}

function extractDatePosted(string $html): ?string {
    libxml_use_internal_errors(true);
    $document = new DOMDocument();
    $document->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
    $xpath = new DOMXPath($document);
    $scripts = $xpath->query('//script[contains(@type, "ld+json")]');

    if ($scripts !== false) {
        foreach ($scripts as $script) {
            $payload = json_decode($script->textContent, true);
            $date = findJobPostingDate($payload);
            if ($date !== null) {
                return $date;
            }
        }
    }

    // Fallback for a valid datePosted field present in the HTML but not parseable JSON-LD.
    if (preg_match('~"datePosted"\s*:\s*"([^"]+)"~i', $html, $match) === 1) {
        return normalizeDate($match[1]);
    }
    return null;
}

function findJobPostingDate($node): ?string {
    if (!is_array($node)) {
        return null;
    }
    $type = $node['@type'] ?? null;
    $isJobPosting = $type === 'JobPosting' || (is_array($type) && in_array('JobPosting', $type, true));
    if ($isJobPosting && isset($node['datePosted']) && is_string($node['datePosted'])) {
        return normalizeDate($node['datePosted']);
    }
    foreach ($node as $value) {
        $date = findJobPostingDate($value);
        if ($date !== null) {
            return $date;
        }
    }
    return null;
}

function normalizeDate(string $value): ?string {
    try {
        return (new DateTimeImmutable($value))->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\\TH:i:s.v\\Z');
    } catch (Exception $exception) {
        return null;
    }
}
