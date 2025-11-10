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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $koda = trim($_POST['koda'] ?? '');
    $opis = trim($_POST['opis'] ?? '');
    $status = $_POST['status'] ?? 'aktiven';
    
    if (empty($ime) || empty($koda)) {
        $sporocilo = 'Polji Ime in Koda sta obvezni.';
    } else {
        try {
            // Preveri, če koda že obstaja
            $stmt = $pdo->prepare("SELECT id FROM predmeti WHERE koda = ?");
            $stmt->execute([$koda]);
            if ($stmt->fetch()) {
                $sporocilo = 'Predmet s to kodo že obstaja.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO predmeti (ime, koda, opis, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$ime, $koda, $opis, $status]);
                $sporocilo = 'Predmet uspešno dodan.';
                $ime = $koda = $opis = '';
            }
        } catch (PDOException $e) {
            $sporocilo = 'Napaka pri dodajanju: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj predmet</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-box {
            max-width: 700px;
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
            width: 160px;
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
        .nav-back {
            text-align: center;
            margin-top: 15px;
        }
        .nav-back a {
            color: var(--color-text);
            text-decoration: none;
        }
        .nav-back a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="content-container">
        <div class="form-box">
            <h1>Dodaj predmet</h1>
            <?php if (!empty($sporocilo)): ?>
                <p style="color: <?php echo (strpos($sporocilo, 'Napaka') !== false) ? 'red' : 'green'; ?>; text-align: center; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($sporocilo); ?>
                </p>
            <?php endif; ?>
            
            <form method="POST" action="dodaj_predmet.php">
                <label for="ime">Ime *</label>
                <input type="text" id="ime" name="ime" value="<?php echo htmlspecialchars($ime ?? ''); ?>" required>
                
                <label for="koda">Koda *</label>
                <input type="text" id="koda" name="koda" value="<?php echo htmlspecialchars($koda ?? ''); ?>" required maxlength="10">
                
                <label for="opis">Opis</label>
                <textarea id="opis" name="opis" rows="5"><?php echo htmlspecialchars($opis ?? ''); ?></textarea>
                
                <label for="status">Status *</label>
                <select id="status" name="status" required>
                    <option value="aktiven" selected>Aktiven</option>
                    <option value="neaktiven">Neaktiven</option>
                </select>
                
                <button type="submit">Dodaj predmet</button>
            </form>
            <div class="nav-back">
                <a href="predmeti.php">⬅ Nazaj na Predmeti</a>
            </div>
        </div>
    </div>
</body>
</html>

