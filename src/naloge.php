<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$ucitelj_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];

if (!je_ucitelj()) {
    die("Dostop zavrnjen. Samo učitelji lahko dodajajo naloge.");
}

global $pdo;
$sporocilo = '';

// 1. PRIDOBITEV PREDMETOV, KI JIH POUČUJE PRIJAVLJENI UČITELJ
try {
    $stmt_predmeti = $pdo->prepare("
        SELECT p.id, p.ime, p.koda 
        FROM predmeti p
        JOIN ucitelji_predmeti up ON p.id = up.id_predmeta
        WHERE up.id_ucitelja = ? AND p.status = 'aktiven'
        ORDER BY p.ime
    ");
    $stmt_predmeti->execute([$ucitelj_id]);
    $predmeti = $stmt_predmeti->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Napaka pri pridobivanju predmetov: " . $e->getMessage());
}


// 2. OBDELAVA FORME ZA DODAJANJE NALOGE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naslov = $_POST['naslov'] ?? '';
    $navodila = $_POST['navodila'] ?? '';
    $rok_oddaje = $_POST['rok_oddaje'] ?? '';
    $id_predmeta = $_POST['id_predmeta'] ?? '';

    // Preverimo, če je izbrani predmet res eden od predmetov, ki jih učitelj poučuje (dodatna varnost)
    $veljaven_predmet = false;
    foreach ($predmeti as $p) {
        if ((int)$p['id'] === (int)$id_predmeta) {
            $veljaven_predmet = true;
            break;
        }
    }

    if (empty($naslov) || empty($navodila) || empty($rok_oddaje) || empty($id_predmeta)) {
        $sporocilo = "Vsa polja so obvezna.";
    } elseif (!$veljaven_predmet) {
        $sporocilo = "Napaka: Ne smete dodajati nalog za ta predmet.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO naloge (naslov, navodila, rok_oddaje, id_predmeta, id_avtorja, datum_objave, status) 
                                   VALUES (?, ?, ?, ?, ?, NOW(), 'aktiven')");
            $stmt->execute([$naslov, $navodila, $rok_oddaje, $id_predmeta, $ucitelj_id]);
            $sporocilo = "Naloga uspešno dodana.";
            
            // Po uspešnem dodajanju izpraznimo polja
            $naslov = $navodila = $rok_oddaje = $id_predmeta = '';

        } catch (PDOException $e) {
            $sporocilo = "Napaka pri dodajanju naloge: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj nalogo</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Specifičen stil za to stran (če ni v styles.css) */
        .form-box {
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            background-color: var(--color-light-beige);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-box h1 {
            text-align: center;
            color: var(--color-text);
            margin-bottom: 25px;
        }
        .form-box label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: var(--color-text);
        }
        .form-box input[type="text"],
        .form-box input[type="date"],
        .form-box textarea,
        .form-box select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-box button {
            display: block;
            width: 150px;
            padding: 10px;
            margin: 25px auto 0 auto;
            border: none;
            border-radius: 5px;
            background-color: var(--color-dark-beige);
            color: var(--color-text);
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .form-box button:hover {
            background-color: #c9b48c;
        }
    </style>
</head>
<body>
    <div class="content-container">
        <div class="form-box">
            <h1>Dodaj novo nalogo</h1>
            <?php if (!empty($sporocilo)): ?>
                <p style="color: <?php echo (strpos($sporocilo, 'Napaka') !== false) ? 'red' : 'green'; ?>; text-align: center; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($sporocilo); ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="naloge.php">
                <label for="id_predmeta">Predmet:</label>
                <select id="id_predmeta" name="id_predmeta" required>
                    <option value="">-- Izberi predmet --</option>
                    <?php foreach ($predmeti as $predmet): ?>
                        <option value="<?php echo htmlspecialchars($predmet['id']); ?>" 
                                <?php if (isset($id_predmeta) && $id_predmeta == $predmet['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($predmet['koda'] . ' - ' . $predmet['ime']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="naslov">Naslov naloge:</label>
                <input type="text" id="naslov" name="naslov" value="<?php echo htmlspecialchars($naslov ?? ''); ?>" required>

                <label for="navodila">Navodila:</label>
                <textarea id="navodila" name="navodila" rows="5" required><?php echo htmlspecialchars($navodila ?? ''); ?></textarea>

                <label for="rok_oddaje">Rok oddaje:</label>
                <input type="date" id="rok_oddaje" name="rok_oddaje" value="<?php echo htmlspecialchars($rok_oddaje ?? ''); ?>" required>

                <button type="submit">Objavi nalogo</button>
            </form>
        </div>
    </div>
</body>
</html>