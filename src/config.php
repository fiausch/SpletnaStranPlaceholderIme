<?php
// Konfiguracija podatkovne baze

define('DB_HOST', ' sql310.infinityfree.com');
define('DB_USER', 'if0_40317569');
define('DB_PASS', '6R0VTKGC9FZWpK');
define('DB_NAME', ' if0_40317569_placeholder_baza');
define('DB_CHARSET', 'utf8mb4');

// Vzpostavitev povezave z bazo
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Napaka pri povezavi z bazo: " . $e->getMessage());
}
?>
