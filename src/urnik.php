<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$uporabnik_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];
global $pdo;

$urnik_podatki = [];
$sporocilo = "";

try {
    if ($vloga === 'ucenec') {
        $stmt_urnik = $pdo->prepare(
            "SELECT u.dan, u.ura, u.ucilnica, p.koda, p.ime
             FROM urnik u
             JOIN predmeti p ON p.id = u.id_predmeta
             JOIN ucenci_predmeti up ON up.id_predmeta = p.id
             WHERE up.id_ucenca = ?
               AND up.status = 'vpisano'
               AND u.status = 'aktiven'
               AND p.status = 'aktiven'
             ORDER BY FIELD(u.dan,'pon','tor','sre','cet','pet'), u.ura, p.ime"
        );
        $stmt_urnik->execute([$uporabnik_id]);
        $vrstice = $stmt_urnik->fetchAll(PDO::FETCH_ASSOC);

        $dni = ['pon','tor','sre','cet','pet'];
        $urnik_podatki = [];
        $maxUra = 0;
        foreach ($vrstice as $r) {
            $dan = $r['dan'];
            $ura = (int)$r['ura'];
            $vsebina = $r['koda'] . (isset($r['ucilnica']) && $r['ucilnica'] !== '' ? ' (' . $r['ucilnica'] . ')' : '');
            if (!isset($urnik_podatki[$ura])) { $urnik_podatki[$ura] = []; }
            if (!isset($urnik_podatki[$ura][$dan])) { $urnik_podatki[$ura][$dan] = []; }
            $urnik_podatki[$ura][$dan][] = $vsebina;
            if ($ura > $maxUra) { $maxUra = $ura; }
        }
        if ($maxUra === 0) {
            $sporocilo = "Za vašo vlogo trenutno ni podatkov za urnik.";
        }

    } elseif ($vloga === 'ucitelj') {
        $sporocilo = "Trenutno ni podatkov za urnik. Dodajte tabelo 'urnik' v bazo.";
        
    } elseif ($vloga === 'administrator') {
        $sporocilo = "Trenutno ni podatkov za urnik. Dodajte tabelo 'urnik' v bazo.";
    }

} catch (PDOException $e) {
    $sporocilo = "Napaka pri pridobivanju urnika: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urnik</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Dodatni stili za tabelo urnika, če jih ni v styles.css */
        .schedule-box { min-height: 500px; padding: 30px; background-color: var(--color-light-beige); border-radius: 15px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); position: relative; }
        .schedule-box table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: var(--color-white); border-radius: 8px; overflow: hidden; }
        .schedule-box th, .schedule-box td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .schedule-box th { background-color: var(--color-dark-beige); color: var(--color-text); font-weight: bold; }
        .schedule-box tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <nav class="main-nav">
        <a href="ocene.php">Ocene</a>
        <a href="#" class="active">Urnik</a>
        <a href="predmeti.php">Spletna učilnica</a>
        <?php if ($vloga === 'ucitelj' || $vloga === 'administrator'): ?>
            <a href="list_ucencov.php">Učenci</a>
        <?php endif; ?>
        <?php if ($vloga === 'administrator'): ?>
            <a href="upravljanje_ucitelji.php">Profesorji</a>
        <?php endif; ?>
        <a href="meni.php">Meni</a>
    </nav>
    
    <div class="content-container">
        <div class="schedule-box">
            <h3>Urnik</h3>
            
            <?php if (!empty($sporocilo)): ?>
                <p style="color: red; text-align: center; margin-top: 20px;"><?php echo htmlspecialchars($sporocilo); ?></p>
            <?php elseif (!empty($urnik_podatki) && $vloga === 'ucenec'): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Ura</th>
                            <th>Ponedeljek</th>
                            <th>Torek</th>
                            <th>Sreda</th>
                            <th>Četrtek</th>
                            <th>Petek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $dniVrstniRed = ['pon','tor','sre','cet','pet'];
                        $zgornjaUra = isset($maxUra) && $maxUra > 0 ? max(10, $maxUra) : 10;
                        for ($h = 1; $h <= $zgornjaUra; $h++): ?>
                            <tr>
                                <td><?php echo $h; ?></td>
                                <?php foreach ($dniVrstniRed as $d): ?>
                                    <td>
                                        <?php
                                        $celice = $urnik_podatki[$h][$d] ?? [];
                                        if ($celice) {
                                            echo nl2br(htmlspecialchars(implode("\n", $celice)));
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            <?php else: ?>
                 <p style="text-align: center; margin-top: 20px;">Za vašo vlogo trenutno ni podatkov za urnik.</p>
            <?php endif; ?>

            <?php if ($vloga === 'ucitelj' || $vloga === 'administrator'): ?>
                <button class="btn-edit" onclick="window.location.href='uredi_urnik.php'">Uredi</button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>