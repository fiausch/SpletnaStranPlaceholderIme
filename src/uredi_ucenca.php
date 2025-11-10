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

$id_ucenca = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_ucenca <= 0) {
    header('Location: upravljanje_ucenci.php');
    exit;
}

$stmt = $pdo->prepare("SELECT id, ime, priimek, uporabnisko_ime, email, datum_rojstva, status FROM uporabniki WHERE id = ? AND vloga = 'ucenec'");
$stmt->execute([$id_ucenca]);
$ucenec = $stmt->fetch();

if (!$ucenec) {
    header('Location: upravljanje_ucenci.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime'] ?? '');
    $priimek = trim($_POST['priimek'] ?? '');
    $uporabnisko_ime = trim($_POST['uporabnisko_ime'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $datum_rojstva = $_POST['datum_rojstva'] ?? null;
    $status = $_POST['status'] ?? 'aktiven';
    $geslo = $_POST['geslo'] ?? '';
    
    if (empty($ime) || empty($priimek) || empty($uporabnisko_ime) || empty($email)) {
        $sporocilo = 'Vsa obvezna polja morajo biti izpolnjena.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM uporabniki WHERE (email = ? OR uporabnisko_ime = ?) AND id != ?");
            $stmt->execute([$email, $uporabnisko_ime, $id_ucenca]);
            if ($stmt->fetch()) {
                $sporocilo = 'E-mail ali uporabniško ime že obstaja pri drugem uporabniku.';
            } else {
                if (!empty($geslo)) {
                    $geslo_hash = password_hash($geslo, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE uporabniki SET ime = ?, priimek = ?, uporabnisko_ime = ?, email = ?, datum_rojstva = ?, status = ?, geslo = ? WHERE id = ?");
                    $stmt->execute([$ime, $priimek, $uporabnisko_ime, $email, $datum_rojstva ?: null, $status, $geslo_hash, $id_ucenca]);
                } else {
                    $stmt = $pdo->prepare("UPDATE uporabniki SET ime = ?, priimek = ?, uporabnisko_ime = ?, email = ?, datum_rojstva = ?, status = ? WHERE id = ?");
                    $stmt->execute([$ime, $priimek, $uporabnisko_ime, $email, $datum_rojstva ?: null, $status, $id_ucenca]);
                }
                $sporocilo = 'Učenec uspešno posodobljen.';
                $stmt = $pdo->prepare("SELECT id, ime, priimek, uporabnisko_ime, email, datum_rojstva, status FROM uporabniki WHERE id = ?");
                $stmt->execute([$id_ucenca]);
                $ucenec = $stmt->fetch();
            }
        } catch (PDOException $e) {
            $sporocilo = 'Napaka pri posodabljanju: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uredi učenca</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
        .form-box input[type="email"],
        .form-box input[type="password"],
        .form-box input[type="date"],
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
    </style>
</head>
<body>
    <div class="content-container">
        <div class="form-box">
            <h1>Uredi učenca</h1>
            <?php if (!empty($sporocilo)): ?>
                <p style="color: <?php echo (strpos($sporocilo, 'Napaka') !== false) ? 'red' : 'green'; ?>; text-align: center; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($sporocilo); ?>
                </p>
            <?php endif; ?>
            
            <form method="POST" action="uredi_ucenca.php?id=<?php echo $id_ucenca; ?>">
                <label for="ime">Ime *</label>
                <input type="text" id="ime" name="ime" value="<?php echo htmlspecialchars($ucenec['ime']); ?>" required>
                
                <label for="priimek">Priimek *</label>
                <input type="text" id="priimek" name="priimek" value="<?php echo htmlspecialchars($ucenec['priimek']); ?>" required>
                
                <label for="uporabnisko_ime">Uporabniško ime *</label>
                <input type="text" id="uporabnisko_ime" name="uporabnisko_ime" value="<?php echo htmlspecialchars($ucenec['uporabnisko_ime']); ?>" required>
                
                <label for="email">E-mail *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($ucenec['email']); ?>" required>
                
                <label for="geslo">Novo geslo (pustite prazno, če ne želite spremeniti)</label>
                <input type="password" id="geslo" name="geslo">
                
                <label for="datum_rojstva">Datum rojstva</label>
                <input type="date" id="datum_rojstva" name="datum_rojstva" value="<?php echo $ucenec['datum_rojstva'] ? htmlspecialchars($ucenec['datum_rojstva']) : ''; ?>">
                
                <label for="status">Status *</label>
                <select id="status" name="status" required>
                    <option value="aktiven" <?php echo ($ucenec['status'] === 'aktiven') ? 'selected' : ''; ?>>Aktiven</option>
                    <option value="neaktiven" <?php echo ($ucenec['status'] === 'neaktiven') ? 'selected' : ''; ?>>Neaktiven</option>
                </select>
                
                <button type="submit">Shrani spremembe</button>
            </form>
            <div class="nav-back">
                <a href="upravljanje_ucenci.php">⬅ Nazaj na seznam učencev</a>
            </div>
        </div>
    </div>
</body>
</html>

