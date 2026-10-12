<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

$pdo = getPDOConnection();

// Fetch all active products
$stmt = $pdo->query("SELECT id, name, updated_at FROM products WHERE status = 'active' ORDER BY id DESC");
$products = $stmt->fetchAll();

$baseUrl = 'https://gpower-store.com';

$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Core static pages
$pages = [
    ['url' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'daily'],
    ['url' => $baseUrl . '/products.php', 'priority' => '0.9', 'changefreq' => 'daily'],
    ['url' => $baseUrl . '/about.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => $baseUrl . '/contact.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
    ['url' => $baseUrl . '/legal.php?section=terms', 'priority' => '0.3', 'changefreq' => 'monthly'],
    ['url' => $baseUrl . '/legal.php?section=privacy', 'priority' => '0.3', 'changefreq' => 'monthly'],
    ['url' => $baseUrl . '/legal.php?section=mentions', 'priority' => '0.3', 'changefreq' => 'monthly']
];

foreach ($pages as $p) {
    $xml .= "  <url>\n";
    $xml .= "    <loc>" . htmlspecialchars($p['url']) . "</loc>\n";
    $xml .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    $xml .= "    <changefreq>" . $p['changefreq'] . "</changefreq>\n";
    $xml .= "    <priority>" . $p['priority'] . "</priority>\n";
    $xml .= "  </url>\n";
}

// Product pages
foreach ($products as $prod) {
    $lastmod = !empty($prod['updated_at']) ? date('Y-m-d', strtotime($prod['updated_at'])) : date('Y-m-d');
    $prodUrl = $baseUrl . '/product-details.php?id=' . $prod['id'];
    $xml .= "  <url>\n";
    $xml .= "    <loc>" . htmlspecialchars($prodUrl) . "</loc>\n";
    $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
    $xml .= "    <changefreq>weekly</changefreq>\n";
    $xml .= "    <priority>0.85</priority>\n";
    $xml .= "  </url>\n";
}

$xml .= '</urlset>';

file_put_contents(__DIR__ . '/sitemap.xml', $xml);
echo "Sitemap generated successfully with " . count($products) . " products!\n";
