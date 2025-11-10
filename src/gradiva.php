<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$uporabnik_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];

// Samo učitelji in administratorji lahko dostopajo
if ($vloga !== 'ucitelj' && $vloga !== 'administrator') {
    header('Location: index.php');
    exit;
}

global $pdo;
$sporocilo = '';

$id_predmeta = isset($_GET['id_predmeta']) ? (int)$_GET['id_predmeta'] : 0;

// Preveri, če učitelj poučuje ta predmet (razen če je administrator)
if ($vloga === 'ucitelj' && $id_predmeta > 0) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ucitelji_predmeti WHERE id_predmeta = ? AND id_ucitelja = ?");
    $stmt->execute([$id_predmeta, $uporabnik_id]);
    if ($stmt->fetchColumn() == 0) {
        die("Dostop zavrnjen. Ne poučujete tega predmeta.");
    }
}

// Pridobi predmete, ki jih učitelj poučuje
if ($vloga === 'ucitelj') {
    $stmt = $pdo->prepare("
        SELECT p.id, p.ime, p.koda 
        FROM predmeti p
        INNER JOIN ucitelji_predmeti up ON p.id = up.id_predmeta
        WHERE up.id_ucitelja = ? AND p.status = 'aktiven'
        ORDER BY p.ime
    ");
    $stmt->execute([$uporabnik_id]);
    $predmeti = $stmt->fetchAll();
} else {
    // Administrator vidi vse predmete
    $stmt = $pdo->prepare("SELECT id, ime, koda FROM predmeti WHERE status = 'aktiven' ORDER BY ime");
    $stmt->execute();
    $predmeti = $stmt->fetchAll();
}

// Če je izbran predmet, pridobi gradiva
$gradiva = [];
if ($id_predmeta > 0) {
    $stmt = $pdo->prepare("
        SELECT g.id, g.naslov, g.tip, g.pot_do_datoteke, g.datum_objave, g.id_avtorja,
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
        .btn-add {
            background-color: var(--color-dark-beige);
            color: var(--color-text);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .btn-add:hover {
            background-color: #c9b48c;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .gradivo-info {
            flex: 1;
        }
        .gradivo-naslov {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .gradivo-meta {
            color: #666;
            font-size: 14px;
        }
        .gradivo-akcije {
            display: flex;
            gap: 10px;
        }
        .btn-view, .btn-delete {
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-view {
            background-color: #4CAF50;
            color: white;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        .btn-view:hover {
            background-color: #45a049;
        }
        .btn-delete:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
    <nav class="main-nav">
        <a href="ocene.php">Ocene</a>
        <a href="urnik.php">Urnik</a>
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
        <div class="content-box">
            <h3>Gradiva</h3>
            
            <div class="predmet-selector">
                <label for="predmet">Izberi predmet:</label>
                <select id="predmet" name="predmet" onchange="window.location.href='gradiva.php?id_predmeta=' + this.value">
                    <option value="0">-- Izberi predmet --</option>
                    <?php foreach ($predmeti as $predmet): ?>
                        <option value="<?php echo $predmet['id']; ?>" <?php echo ($id_predmeta == $predmet['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($predmet['koda'] . ' - ' . $predmet['ime']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if ($id_predmeta > 0): ?>
                <button class="btn-add" onclick="window.location.href='dodaj_gradivo.php?id_predmeta=<?php echo $id_predmeta; ?>'">+ Dodaj gradivo</button>
                
                <div class="gradiva-list">
                    <?php if (empty($gradiva)): ?>
                        <p>Za ta predmet še ni gradiv.</p>
                    <?php else: ?>
                        <?php foreach ($gradiva as $gradivo): ?>
                            <div class="gradivo-item">
                                <div class="gradivo-info">
                                    <div class="gradivo-naslov"><?php echo htmlspecialchars($gradivo['naslov']); ?></div>
                                    <div class="gradivo-meta">
                                        Tip: <?php echo htmlspecialchars($gradivo['tip']); ?> | 
                                        Avtor: <?php echo htmlspecialchars($gradivo['avtor_ime'] . ' ' . $gradivo['avtor_priimek']); ?> | 
                                        Datum: <?php echo date('d.m.Y', strtotime($gradivo['datum_objave'])); ?>
                                    </div>
                                </div>
                                <div class="gradivo-akcije">
                                    <?php if ($gradivo['pot_do_datoteke']): ?>
                                        <a href="<?php echo htmlspecialchars($gradivo['pot_do_datoteke']); ?>" target="_blank" class="btn-view">Prikaži</a>
                                    <?php endif; ?>
                                    <?php if ($gradivo['id_avtorja'] == $uporabnik_id || $vloga === 'administrator'): ?>
                                        <button class="btn-delete" onclick="if(confirm('Ali ste prepričani, da želite izbrisati to gradivo?')) window.location.href='brisi_gradivo.php?id=<?php echo $gradivo['id']; ?>&id_predmeta=<?php echo $id_predmeta; ?>'">Briši</button>
                                    <?php endif; ?>
                                </div>
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

