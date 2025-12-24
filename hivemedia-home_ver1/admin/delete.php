<?php

/**
 * Delete Article
 * HIVEMEDIA Portfolio System
 */
require_once 'config.php';
requireLogin();

if (isset($_GET['id'])) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM portfolio_articles WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header('Location: dashboard.php');
exit;
