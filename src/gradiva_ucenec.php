<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$uporabnik_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];

if ($vloga !== 'ucenec') {
    header('Location: index.php');
    exit;
}

global $pdo;
$sporocilo = '';

$id_predmeta = isset($_GET['id_predmeta']) ? (int)$_GET['id_predmeta'] : 0;

// Preveri, če učenec obiskuje ta predmet
if ($id_predmeta > 0) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ucenci_predmeti WHERE id_predmeta = ? AND id_ucenca = ? AND status = 'vpisano'");
    $stmt->execute([$id_predmeta, $uporabnik_id]);
    if ($stmt->fetchColumn() == 0) {
        die("Dostop zavrnjen. Ne obiskujete tega predmeta.");
    }
}

// Pridobi predmete, ki jih učenec obiskuje
$stmt = $pdo->prepare("
    SELECT p.id, p.ime, p.koda 
    FROM predmeti p
    INNER JOIN ucenci_predmeti up ON p.id = up.id_predmeta
    WHERE up.id_ucenca = ? AND up.status = 'vpisano' AND p.status = 'aktiven'
    ORDER BY p.ime
");
$stmt->execute([$uporabnik_id]);
$predmeti = $stmt->fetchAll();

// Če je izbran predmet, pridobi gradiva
$gradiva = [];
if ($id_predmeta > 0) {
    $stmt = $pdo->prepare("
        SELECT g.id, g.naslov, g.tip, g.pot_do_datoteke, g.datum_objave, g.vsebina,
               u.ime as avtor_ime, u.priimek as avtor_priimek
        FROM gradiva g
        INNER JOIN uporabniki u ON g.id_avtorja = u.id
        WHERE g.id_predmeta = ? AND g.status = 'aktiven'
        ORDER BY g.datum_objave DESC
    ");
    $stmt->execute([$id_predmeta]);
    $gradiva = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gradiva</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .content-box {
            max-width: 1000px;
            margin: 20px auto;
            padding: 30px;
            background-color: var(--color-light-beige);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .content-box h3 {
            margin-bottom: 20px;
            color: var(--color-text);
        }
        .predmet-selector {
            margin-bottom: 20px;
        }
        .predmet-selector select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .gradiva-list {
            margin-top: 20px;
        }
        .gradivo-item {
            background-color: var(--color-white);
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .gradivo-naslov {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .gradivo-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .gradivo-vsebina {
            margin-top: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .btn-view {
            padding: 5px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .btn-view:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <nav class="main-nav">
        <a href="ocene.php">Ocene</a>
        <a href="urnik.php">Urnik</a>
        <a href="predmeti.php">Spletna učilnica</a>
        <a href="meni.php">Meni</a>
    </nav>
    
    <div class="content-container">
        <div class="content-box">
            <h3>Gradiva</h3>
            
            <div class="predmet-selector">
                <label for="predmet">Izberi predmet:</label>
                <select id="predmet" name="predmet" onchange="window.location.href='gradiva_ucenec.php?id_predmeta=' + this.value">
                    <option value="0">-- Izberi predmet --</option>
                    <?php foreach ($predmeti as $predmet): ?>
                        <option value="<?php echo $predmet['id']; ?>" <?php echo ($id_predmeta == $predmet['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($predmet['koda'] . ' - ' . $predmet['ime']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if ($id_predmeta > 0): ?>
                <div class="gradiva-list">
                    <?php if (empty($gradiva)): ?>
                        <p>Za ta predmet še ni gradiv.</p>
                    <?php else: ?>
                        <?php foreach ($gradiva as $gradivo): ?>
                            <div class="gradivo-item">
                                <div class="gradivo-naslov"><?php echo htmlspecialchars($gradivo['naslov']); ?></div>
                                <div class="gradivo-meta">
                                    Tip: <?php echo htmlspecialchars($gradivo['tip']); ?> | 
                                    Avtor: <?php echo htmlspecialchars($gradivo['avtor_ime'] . ' ' . $gradivo['avtor_priimek']); ?> | 
                                    Datum: <?php echo date('d.m.Y', strtotime($gradivo['datum_objave'])); ?>
                                </div>
                                <?php if ($gradivo['vsebina']): ?>
                                    <div class="gradivo-vsebina"><?php echo nl2br(htmlspecialchars($gradivo['vsebina'])); ?></div>
                                <?php endif; ?>
                                <?php if ($gradivo['pot_do_datoteke']): ?>
                                    <a href="<?php echo htmlspecialchars($gradivo['pot_do_datoteke']); ?>" target="_blank" class="btn-view">Prikaži/Prenesi</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p>Izberite predmet za prikaz gradiv.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

