<?php
// Skripta za varno prenašanje datotek
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$vloga = $uporabnik['vloga'];
$uporabnik_id = $uporabnik['id'];

$tip = $_GET['tip'] ?? ''; // 'oddaja' ali 'gradivo'
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($tip === 'oddaja') {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT o.pot_do_datoteke, o.originalno_ime_datoteke, o.id_ucenca, n.id_predmeta
        FROM oddaje o
        INNER JOIN naloge n ON o.id_naloge = n.id
        WHERE o.id = ?
    ");
    $stmt->execute([$id]);
    $oddaja = $stmt->fetch();
    
    if (!$oddaja) {
        die("Datoteka ni najdena.");
    }
    
    // Preveri dovoljenja
    if ($vloga === 'ucenec' && $oddaja['id_ucenca'] != $uporabnik_id) {
        die("Dostop zavrnjen.");
    }
    
    if ($vloga === 'ucitelj') {
        // Preveri, če učitelj poučuje ta predmet
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ucitelji_predmeti WHERE id_predmeta = ? AND id_ucitelja = ?");
        $stmt->execute([$oddaja['id_predmeta'], $uporabnik_id]);
        if ($stmt->fetchColumn() == 0 && $vloga !== 'administrator') {
            die("Dostop zavrnjen.");
        }
    }
    
    $pot = $oddaja['pot_do_datoteke'];
    $ime_datoteke = $oddaja['originalno_ime_datoteke'];
    
} elseif ($tip === 'gradivo') {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT g.pot_do_datoteke, g.naslov, g.id_predmeta, g.id_avtorja
        FROM gradiva g
        WHERE g.id = ? AND g.status = 'aktiven'
    ");
    $stmt->execute([$id]);
    $gradivo = $stmt->fetch();
    
    if (!$gradivo) {
        die("Gradivo ni najdeno.");
    }
    
    // Preveri dovoljenja
    if ($vloga === 'ucenec') {
        // Preveri, če učenec obiskuje ta predmet
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ucenci_predmeti WHERE id_predmeta = ? AND id_ucenca = ? AND status = 'vpisano'");
        $stmt->execute([$gradivo['id_predmeta'], $uporabnik_id]);
        if ($stmt->fetchColumn() == 0) {
            die("Dostop zavrnjen.");
        }
    }
    
    $pot = $gradivo['pot_do_datoteke'];
    $ime_datoteke = $gradivo['naslov'];
    
} else {
    die("Napačen tip datoteke.");
}

// Poišči datoteko
$pot_datoteke = null;
if (file_exists(ltrim($pot, '/'))) {
    $pot_datoteke = ltrim($pot, '/');
} elseif (file_exists(__DIR__ . '/../' . ltrim($pot, '/'))) {
    $pot_datoteke = __DIR__ . '/../' . ltrim($pot, '/');
} else {
    die("Datoteka ni najdena na strežniku.");
}

// Preveri, če datoteka obstaja
if (!file_exists($pot_datoteke)) {
    die("Datoteka ni najdena.");
}

// Pošlji datoteko
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($ime_datoteke) . '"');
header('Content-Length: ' . filesize($pot_datoteke));
readfile($pot_datoteke);
exit;
?>

