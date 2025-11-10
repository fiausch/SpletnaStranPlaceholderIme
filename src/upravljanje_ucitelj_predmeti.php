<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$vloga = $uporabnik['vloga'];

if ($vloga !== 'administrator') {
    header('Location: index.php');
    exit;
}

global $pdo;
$sporocilo = '';

$id_ucitelja = isset($_GET['id_ucitelja']) ? (int)$_GET['id_ucitelja'] : 0;
if ($id_ucitelja <= 0) {
    header('Location: upravljanje_ucitelji.php');
    exit;
}

// Pridobi podatke o učitelju
$stmt = $pdo->prepare("SELECT id, ime, priimek FROM uporabniki WHERE id = ? AND vloga = 'ucitelj'");
$stmt->execute([$id_ucitelja]);
$ucitelj = $stmt->fetch();

if (!$ucitelj) {
    header('Location: upravljanje_ucitelji.php');
    exit;
}

// Obdelava dodajanja predmeta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_predmet'])) {
    $id_predmeta = (int)($_POST['id_predmeta'] ?? 0);
    if ($id_predmeta > 0) {
        try {
            // Preveri, če povezava že obstaja
            $stmt = $pdo->prepare("SELECT id FROM ucitelji_predmeti WHERE id_ucitelja = ? AND id_predmeta = ?");
            $stmt->execute([$id_ucitelja, $id_predmeta]);
            if ($stmt->fetch()) {
                $sporocilo = 'Učitelj že poučuje ta predmet.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO ucitelji_predmeti (id_ucitelja, id_predmeta) VALUES (?, ?)");
                $stmt->execute([$id_ucitelja, $id_predmeta]);
                $sporocilo = 'Predmet uspešno dodan.';
            }
        } catch (PDOException $e) {
            $sporocilo = 'Napaka pri dodajanju: ' . $e->getMessage();
        }
    }
}

// Obdelava brisanja predmeta
if (isset($_GET['brisi_predmet']) && is_numeric($_GET['brisi_predmet'])) {
    $id_predmeta = (int)$_GET['brisi_predmet'];
    try {
        $stmt = $pdo->prepare("DELETE FROM ucitelji_predmeti WHERE id_ucitelja = ? AND id_predmeta = ?");
        $stmt->execute([$id_ucitelja, $id_predmeta]);
        $sporocilo = 'Predmet uspešno odstranjen.';
    } catch (PDOException $e) {
        $sporocilo = 'Napaka pri brisanju: ' . $e->getMessage();
    }
}

// Pridobi predmete, ki jih učitelj poučuje
$stmt = $pdo->prepare("
    SELECT p.id, p.ime, p.koda 
    FROM predmeti p
    INNER JOIN ucitelji_predmeti up ON p.id = up.id_predmeta
    WHERE up.id_ucitelja = ? AND p.status = 'aktiven'
    ORDER BY p.ime
");
$stmt->execute([$id_ucitelja]);
$predmeti_ucitelja = $stmt->fetchAll();

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
    <title>Predmeti učitelja</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .table-box {
            max-width: 900px;
            margin: 20px auto;
            padding: 30px;
            background-color: var(--color-light-beige);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .table-box h3 {
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
        .btn-delete {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-delete:hover {
            background-color: #da190b;
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
    <div class="content-container">
        <div class="table-box">
            <h3>Predmeti učitelja: <?php echo htmlspecialchars($ucitelj['ime'] . ' ' . $ucitelj['priimek']); ?></h3>
            
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
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($predmeti_ucitelja)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">Učitelj še ne poučuje nobenega predmeta.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($predmeti_ucitelja as $predmet): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($predmet['koda']); ?></td>
                                <td><?php echo htmlspecialchars($predmet['ime']); ?></td>
                                <td>
                                    <button class="btn-delete" onclick="if(confirm('Ali ste prepričani, da želite odstraniti ta predmet?')) window.location.href='?id_ucitelja=<?php echo $id_ucitelja; ?>&brisi_predmet=<?php echo $predmet['id']; ?>'">Odstrani</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="form-add">
                <h4>Dodaj predmet:</h4>
                <form method="POST" action="upravljanje_ucitelj_predmeti.php?id_ucitelja=<?php echo $id_ucitelja; ?>">
                    <select name="id_predmeta" required>
                        <option value="">-- Izberi predmet --</option>
                        <?php foreach ($vsi_predmeti as $predmet): ?>
                            <?php 
                            $ze_dodan = false;
                            foreach ($predmeti_ucitelja as $p) {
                                if ($p['id'] == $predmet['id']) {
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
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="upravljanje_ucitelji.php" style="color: var(--color-text); text-decoration: none;">⬅ Nazaj na seznam učiteljev</a>
            </div>
        </div>
    </div>
</body>
</html>

