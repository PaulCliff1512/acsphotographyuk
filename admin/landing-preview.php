<?php
require __DIR__ . '/../includes/admin-auth.php';
requireAdminLogin();

$allowedLandingPages = [
    'landing1.html',
    'landing2.html',
    'landing3.html',
    'landing4.html',
    'landing5.html',
    'landing6.html',
];

$selectedLanding = $_GET['file'] ?? '';

if (!in_array($selectedLanding, $allowedLandingPages, true)) {
    http_response_code(404);
    exit('Landing page not found.');
}

$landingPath = realpath(__DIR__ . '/landing-pages/' . $selectedLanding);
$landingRoot = realpath(__DIR__ . '/landing-pages');

if ($landingPath === false || $landingRoot === false || strpos($landingPath, $landingRoot) !== 0 || !is_file($landingPath)) {
    http_response_code(404);
    exit('Landing page not found.');
}

header('Content-Type: text/html; charset=UTF-8');
readfile($landingPath);
