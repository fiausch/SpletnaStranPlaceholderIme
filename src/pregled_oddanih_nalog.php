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
    $stmt = $pdo->prepare("SELECT id, ime, koda FROM predmeti WHERE status = 'aktiven' ORDER BY ime");
    $stmt->execute();
    $predmeti = $stmt->fetchAll();
}

// Če je izbran predmet, pridobi naloge in oddaje
$naloge = [];
$oddaje = [];
if ($id_predmeta > 0) {
    // Pridobi naloge za ta predmet
    $stmt = $pdo->prepare("
        SELECT id, naslov, rok_addaje as rok_oddaje
        FROM naloge
        WHERE id_predmeta = ? AND status = 'aktiven'
        ORDER BY datum_objave DESC
    ");
    $stmt->execute([$id_predmeta]);
    $naloge = $stmt->fetchAll();
    
    // Pridobi oddaje za vse naloge tega predmeta
    if (!empty($naloge)) {
        $naloge_ids = array_column($naloge, 'id');
        $placeholders = implode(',', array_fill(0, count($naloge_ids), '?'));
        $stmt = $pdo->prepare("
            SELECT o.id, o.id_naloge, o.id_ucenca, o.datum_oddaje, o.pot_do_datoteke, 
                   o.originalno_ime_datoteke, o.status, o.ocena,
                   u.ime as ucenec_ime, u.priimek as ucenec_priimek,
                   n.naslov as naloga_naslov
            FROM oddaje o
            INNER JOIN uporabniki u ON o.id_ucenca = u.id
            INNER JOIN naloge n ON o.id_naloge = n.id
            WHERE o.id_naloge IN ($placeholders)
            ORDER BY o.datum_oddaje DESC
        ");
        $stmt->execute($naloge_ids);
        $oddaje = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pregled oddanih nalog</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .content-box {
            max-width: 1200px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--color-white);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: var(--color-dark-beige);
            color: var(--color-text);
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn-view, .btn-grade {
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        .btn-view {
            background-color: #4CAF50;
            color: white;
        }
        .btn-grade {
            background-color: #2196F3;
            color: white;
        }
        .btn-view:hover {
            background-color: #45a049;
        }
        .btn-grade:hover {
            background-color: #0b7dda;
        }
        .status-oddano {
            color: orange;
            font-weight: bold;
        }
        .status-ocenjeno {
            color: green;
            font-weight: bold;
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
            <h3>Pregled oddanih nalog</h3>
            
            <div class="predmet-selector">
                <label for="predmet">Izberi predmet:</label>
                <select id="predmet" name="predmet" onchange="window.location.href='pregled_oddanih_nalog.php?id_predmeta=' + this.value">
                    <option value="0">-- Izberi predmet --</option>
                    <?php foreach ($predmeti as $predmet): ?>
                        <option value="<?php echo $predmet['id']; ?>" <?php echo ($id_predmeta == $predmet['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($predmet['koda'] . ' - ' . $predmet['ime']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if ($id_predmeta > 0): ?>
                <?php if (empty($naloge)): ?>
                    <p>Za ta predmet še ni nalog.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Učenec</th>
                                <th>Naloga</th>
                                <th>Datum oddaje</th>
                                <th>Datoteka</th>
                                <th>Status</th>
                                <th>Ocena</th>
                                <th>Akcije</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($oddaje)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center;">Še ni oddanih nalog za ta predmet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($oddaje as $oddaja): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($oddaja['ucenec_ime'] . ' ' . $oddaja['ucenec_priimek']); ?></td>
                                        <td><?php echo htmlspecialchars($oddaja['naloga_naslov']); ?></td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($oddaja['datum_oddaje'])); ?></td>
                                        <td><?php echo htmlspecialchars($oddaja['originalno_ime_datoteke']); ?></td>
                                        <td>
                                            <span class="status-<?php echo $oddaja['status']; ?>">
                                                <?php echo ucfirst($oddaja['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $oddaja['ocena'] ? $oddaja['ocena'] : '-'; ?></td>
                                        <td>
                                            <?php if ($oddaja['pot_do_datoteke']): ?>
                                                <a href="<?php echo htmlspecialchars($oddaja['pot_do_datoteke']); ?>" target="_blank" class="btn-view">Prikaži</a>
                                            <?php endif; ?>
                                            <button class="btn-grade" onclick="window.location.href='dodajOceno.php?id_oddaje=<?php echo $oddaja['id']; ?>&id_predmeta=<?php echo $id_predmeta; ?>'">Oceni</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php else: ?>
                <p>Izberite predmet za pregled oddanih nalog.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

