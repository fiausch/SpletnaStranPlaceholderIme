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

// Obdelava opuščanja predmeta
if (isset($_GET['opusci']) && is_numeric($_GET['opusci'])) {
    $id_predmeta = (int)$_GET['opusci'];
    try {
        $stmt = $pdo->prepare("UPDATE ucenci_predmeti SET status = 'opuščeno' WHERE id_ucenca = ? AND id_predmeta = ?");
        $stmt->execute([$uporabnik_id, $id_predmeta]);
        $sporocilo = 'Predmet uspešno opuščen.';
    } catch (PDOException $e) {
        $sporocilo = 'Napaka pri opuščanju: ' . $e->getMessage();
    }
}

// Obdelava ponovnega vpisa
if (isset($_GET['vpisi']) && is_numeric($_GET['vpisi'])) {
    $id_predmeta = (int)$_GET['vpisi'];
    try {
        $stmt = $pdo->prepare("UPDATE ucenci_predmeti SET status = 'vpisano', datum_vpisa = CURDATE() WHERE id_ucenca = ? AND id_predmeta = ?");
        $stmt->execute([$uporabnik_id, $id_predmeta]);
        $sporocilo = 'Uspešno vpisan v predmet.';
    } catch (PDOException $e) {
        $sporocilo = 'Napaka pri vpisu: ' . $e->getMessage();
    }
}

// Obdelava dodajanja predmeta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_predmet'])) {
    $id_predmeta = (int)($_POST['id_predmeta'] ?? 0);
    if ($id_predmeta > 0) {
        try {
            // Preveri, če povezava že obstaja
            $stmt = $pdo->prepare("SELECT id, status FROM ucenci_predmeti WHERE id_ucenca = ? AND id_predmeta = ?");
            $stmt->execute([$uporabnik_id, $id_predmeta]);
            $obstojeca = $stmt->fetch();
            
            if ($obstojeca) {
                if ($obstojeca['status'] === 'opuščeno') {
                    // Ponovno vpiši
                    $stmt = $pdo->prepare("UPDATE ucenci_predmeti SET status = 'vpisano', datum_vpisa = CURDATE() WHERE id = ?");
                    $stmt->execute([$obstojeca['id']]);
                    $sporocilo = 'Predmet uspešno dodan.';
                } else {
                    $sporocilo = 'Ta predmet že obiskujete.';
                }
            } else {
                $stmt = $pdo->prepare("INSERT INTO ucenci_predmeti (id_ucenca, id_predmeta, datum_vpisa, status) VALUES (?, ?, CURDATE(), 'vpisano')");
                $stmt->execute([$uporabnik_id, $id_predmeta]);
                $sporocilo = 'Predmet uspešno dodan.';
            }
        } catch (PDOException $e) {
            $sporocilo = 'Napaka pri dodajanju: ' . $e->getMessage();
        }
    }
}

// Pridobi predmete, ki jih učenec obiskuje
$stmt = $pdo->prepare("
    SELECT p.id, p.ime, p.koda, up.status, up.datum_vpisa
    FROM predmeti p
    INNER JOIN ucenci_predmeti up ON p.id = up.id_predmeta
    WHERE up.id_ucenca = ?
    ORDER BY p.ime
");
$stmt->execute([$uporabnik_id]);
$moji_predmeti = $stmt->fetchAll();

// Pridobi vse predmete za izbiro
$stmt = $pdo->prepare("SELECT id, ime, koda FROM predmeti WHERE status = 'aktiven' ORDER BY ime");
$stmt->execute();
$vsi_predmeti = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moji predmeti</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .content-box {
            max-width: 900px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--color-white);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
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
        .btn-action {
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-opusci {
            background-color: #f44336;
            color: white;
        }
        .btn-vpisi {
            background-color: #4CAF50;
            color: white;
        }
        .btn-opusci:hover {
            background-color: #da190b;
        }
        .btn-vpisi:hover {
            background-color: #45a049;
        }
        .form-add {
            margin-top: 20px;
            padding: 20px;
            background-color: var(--color-white);
            border-radius: 8px;
        }
        .form-add select, .form-add button {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
        }
        .form-add select {
            width: 300px;
            border: 1px solid #ccc;
        }
        .form-add button {
            background-color: var(--color-dark-beige);
            color: var(--color-text);
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        .form-add button:hover {
            background-color: #c9b48c;
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
            <h3>Moji predmeti</h3>
            
            <?php if (!empty($sporocilo)): ?>
                <p style="color: <?php echo (strpos($sporocilo, 'Napaka') !== false) ? 'red' : 'green'; ?>; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($sporocilo); ?>
                </p>
            <?php endif; ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Koda</th>
                        <th>Ime predmeta</th>
                        <th>Status</th>
                        <th>Datum vpisa</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($moji_predmeti)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Ne obiskujete še nobenega predmeta.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($moji_predmeti as $predmet): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($predmet['koda']); ?></td>
                                <td><?php echo htmlspecialchars($predmet['ime']); ?></td>
                                <td>
                                    <span style="color: <?php echo $predmet['status'] === 'vpisano' ? 'green' : 'red'; ?>; font-weight: bold;">
                                        <?php echo ucfirst($predmet['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($predmet['datum_vpisa']); ?></td>
                                <td>
                                    <?php if ($predmet['status'] === 'vpisano'): ?>
                                        <button class="btn-action btn-opusci" onclick="if(confirm('Ali ste prepričani, da želite opustiti ta predmet?')) window.location.href='?opusci=<?php echo $predmet['id']; ?>'">Opusti</button>
                                    <?php else: ?>
                                        <button class="btn-action btn-vpisi" onclick="window.location.href='?vpisi=<?php echo $predmet['id']; ?>'">Vpiši</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="form-add">
                <h4>Dodaj predmet:</h4>
                <form method="POST" action="moji_predmeti_ucenec.php">
                    <select name="id_predmeta" required>
                        <option value="">-- Izberi predmet --</option>
                        <?php foreach ($vsi_predmeti as $predmet): ?>
                            <?php 
                            $ze_dodan = false;
                            foreach ($moji_predmeti as $p) {
                                if ($p['id'] == $predmet['id'] && $p['status'] === 'vpisano') {
                                    $ze_dodan = true;
                                    break;
                                }
                            }
                            if (!$ze_dodan):
                            ?>
                                <option value="<?php echo $predmet['id']; ?>">
                                    <?php echo htmlspecialchars($predmet['koda'] . ' - ' . $predmet['ime']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="dodaj_predmet">Dodaj predmet</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

