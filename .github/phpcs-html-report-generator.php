<?php
// Simple PHPCS report generator from checkstyle XML
$xmlFile = 'phpcs-report.xml';
$htmlFile = 'phpcs-report.html';

if (!file_exists($xmlFile)) {
    echo "XML report not found: $xmlFile\n";
    exit(1);
}

$xml = simplexml_load_file($xmlFile);

$html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHPCS Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f1f1f1; }
        .error { color: red; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <h1>PHPCS Report</h1>
    <table>
        <thead>
            <tr>
                <th>File</th>
                <th>Line</th>
                <th>Severity</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>';

foreach ($xml->file as $file) {
    $filePath = (string) $file['name'];
    foreach ($file->error as $error) {
        $line = (string) $error['line'];
        $severity = 'Error';
        $message = htmlspecialchars((string) $error['message']);
        $html .= "<tr><td>$filePath</td><td>$line</td><td class=\"error\">$severity</td><td>$message</td></tr>";
    }
    foreach ($file->warning as $warning) {
        $line = (string) $warning['line'];
        $severity = 'Warning';
        $message = htmlspecialchars((string) $warning['message']);
        $html .= "<tr><td>$filePath</td><td>$line</td><td class=\"warning\">$severity</td><td>$message</td></tr>";
    }
}

$html .= '</tbody></table></body></html>';

file_put_contents($htmlFile, $html);

echo "HTML report generated: $htmlFile\n";
