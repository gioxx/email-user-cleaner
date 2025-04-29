<?php

// Simple converter from Checkstyle XML to pretty HTML
$xmlFile = __DIR__ . '/phpcs-report.xml';
$htmlFile = __DIR__ . '/phpcs-report.html';

if (!file_exists($xmlFile)) {
    echo "XML report not found.";
    exit(1);
}

$xml = simplexml_load_file($xmlFile);

$html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP CodeSniffer Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; }
        th { background: #f4f4f4; }
        tr:nth-child(even) { background: #f9f9f9; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; }
    </style>
</head>
<body>
<h1>PHP CodeSniffer Report</h1>
<table>
    <thead>
        <tr>
            <th>File</th>
            <th>Line</th>
            <th>Severity</th>
            <th>Message</th>
        </tr>
    </thead>
    <tbody>
HTML;

foreach ($xml->file as $file) {
    $fileName = (string) $file['name'];
    foreach ($file->error as $error) {
        $line = (string) $error['line'];
        $severity = (string) $error['severity'];
        $message = (string) $error['message'];
        $class = ((int)$severity >= 5) ? 'error' : 'warning';

        $html .= <<<ROW
<tr>
    <td>{$fileName}</td>
    <td>{$line}</td>
    <td class="{$class}">{$severity}</td>
    <td>{$message}</td>
</tr>
ROW;
    }
}

$html .= <<<HTML
    </tbody>
</table>
</body>
</html>
HTML;

file_put_contents($htmlFile, $html);

echo "Generated PHPCS HTML report successfully!\n";
