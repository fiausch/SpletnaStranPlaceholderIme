<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$uporabnik_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];

// Funkcija za preverjanje ali lahko uporabnik ureja predmet
function lahko_uredi_predmet($id_predmeta, $uporabnik_id, $vloga) {
    if ($vloga === 'administrator') {
        return true;
    }
    if ($vloga === 'ucitelj') {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ucitelji_predmeti WHERE id_predmeta = ? AND id_ucitelja = ?");
        $stmt->execute([$id_predmeta, $uporabnik_id]);
        return $stmt->fetchColumn() > 0;
    }
    return false;
}

// Pridobivanje predmetov glede na vlogo
global $pdo;
if ($vloga === 'ucenec') {
    // Za učence: pridobimo predmete iz tabele ucenci_predmeti
    $stmt = $pdo->prepare("
        SELECT p.id, p.ime, p.koda, p.opis 
        FROM predmeti p
        INNER JOIN ucenci_predmeti up ON p.id = up.id_predmeta
        WHERE up.id_ucenca = ? AND up.status = 'vpisano' AND p.status = 'aktiven'
        ORDER BY p.ime
    ");
    $stmt->execute([$uporabnik_id]);
    $predmeti = $stmt->fetchAll();
} elseif ($vloga === 'ucitelj') {
    // Za učitelje: pridobimo predmete iz tabele ucitelji_predmeti
    $stmt = $pdo->prepare("
        SELECT p.id, p.ime, p.koda, p.opis 
        FROM predmeti p
        INNER JOIN ucitelji_predmeti up ON p.id = up.id_predmeta
        WHERE up.id_ucitelja = ? AND p.status = 'aktiven'
        ORDER BY p.ime
    ");
    $stmt->execute([$uporabnik_id]);
    $predmeti = $stmt->fetchAll();
} else {
    // Za administratorje: prikažemo vse predmete
    $stmt = $pdo->prepare("SELECT id, ime, koda, opis FROM predmeti WHERE status = 'aktiven' ORDER BY ime");
    $stmt->execute();
    $predmeti = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moji Predmeti</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="main-nav">
        <a href="ocene.php">Ocene</a>
        <a href="urnik.php">Urnik</a>
        <a href="#" class="active">Spletna učilnica</a>
        <?php if ($vloga === 'ucitelj' || $vloga === 'administrator'): ?>
            <a href="list_ucencov.php">Učenci</a>
        <?php endif; ?>
        <?php if ($vloga === 'administrator'): ?>
            <a href="#">Profesorji</a>
        <?php endif; ?>
        <a href="meni.php">Meni</a>
    </nav>
    
    <div class="content-container">
        <div class="subjects-list-box">
            
            <h3>Moji predmeti</h3>
            
            <?php if (empty($predmeti)): ?>
                <p>Nimate vpisanih predmetov.</p>
            <?php else: ?>
                <?php foreach ($predmeti as $predmet): ?>
                    <div class="subject-row-item">
                        <span class="subject-name-display">
                            <?php echo htmlspecialchars($predmet['koda'] . ' - ' . $predmet['ime']); ?>
                        </span>
                        <?php if (lahko_uredi_predmet($predmet['id'], $uporabnik_id, $vloga)): ?>
                            <button class="btn-subject-edit" onclick="window.location.href='uredi_predmet.php?id=<?php echo $predmet['id']; ?>'">Uredi</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($vloga === 'administrator' || ($vloga === 'ucitelj' && !empty($predmeti))): ?>
                <button class="btn-edit-main">Uredi</button>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
