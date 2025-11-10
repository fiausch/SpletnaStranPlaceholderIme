<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$uporabnik_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];

if ($vloga !== 'ucitelj' && $vloga !== 'administrator') {
    header('Location: index.php');
    exit;
}

global $pdo;

$id_gradiva = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$id_predmeta = isset($_GET['id_predmeta']) ? (int)$_GET['id_predmeta'] : 0;

if ($id_gradiva <= 0) {
    header('Location: gradiva.php');
    exit;
}

// Pridobi podatke o gradivu
$stmt = $pdo->prepare("SELECT id, id_avtorja, pot_do_datoteke FROM gradiva WHERE id = ?");
$stmt->execute([$id_gradiva]);
$gradivo = $stmt->fetch();

if (!$gradivo) {
    header('Location: gradiva.php');
    exit;
}

// Preveri dovoljenja
if ($vloga === 'ucitelj' && $gradivo['id_avtorja'] != $uporabnik_id) {
    die("Dostop zavrnjen. Lahko brišete samo svoja gradiva.");
}

try {
    // Izbriši datoteko, če obstaja
    if ($gradivo['pot_do_datoteke']) {
        $pot = ltrim($gradivo['pot_do_datoteke'], '/');
        // Poskusi z različnimi potmi
        if (file_exists($pot)) {
            unlink($pot);
        } elseif (file_exists(__DIR__ . '/../' . $pot)) {
            unlink(__DIR__ . '/../' . $pot);
        }
    }
    
    // Nastavi status na arhiviran namesto brisanja
    $stmt = $pdo->prepare("UPDATE gradiva SET status = 'arhiviran' WHERE id = ?");
    $stmt->execute([$id_gradiva]);
    
    header('Location: gradiva.php?id_predmeta=' . $id_predmeta);
    exit;
} catch (PDOException $e) {
    die("Napaka pri brisanju: " . $e->getMessage());
}
?>

