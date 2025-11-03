<?php
session_start();
require_once 'config.php';

// Če je že prijavljen, preusmeri
if (isset($_SESSION['prijavljen']) && $_SESSION['prijavljen'] === true) {
    header('Location: meni.php');
    exit;
}

// Obdelava registracije
$napaka = '';
$uspeh = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = $_POST['ime'] ?? '';
    $priimek = $_POST['priimek'] ?? '';
    $uporabnisko_ime = $_POST['uporabnisko_ime'] ?? '';
    $datum_rojstva = $_POST['datum_rojstva'] ?? '';
    $email = $_POST['email'] ?? '';
    $geslo = $_POST['password'] ?? '';
    
    // Preverjanje ali so vsa polja izpolnjena
    if (empty($ime) || empty($priimek) || empty($uporabnisko_ime) || empty($email) || empty($geslo) || empty($datum_rojstva)) {
        $napaka = "Prosim izpolnite vsa polja.";
    } else {
        // Preverjanje ali email že obstaja
        $stmt = $pdo->prepare("SELECT id FROM uporabniki WHERE email = ? OR uporabnisko_ime = ?");
        $stmt->execute([$email, $uporabnisko_ime]);
        
        if ($stmt->fetch()) {
            $napaka = "E-mail ali uporabniško ime že obstaja.";
        } else {
            // Hash gesla
            $geslo_hash = password_hash($geslo, PASSWORD_DEFAULT);
            
            // Privzeta vloga je 'ucenec'
            $vloga = 'ucenec';
            
            try {
                $stmt = $pdo->prepare("INSERT INTO uporabniki (ime, priimek, uporabnisko_ime, email, geslo, vloga, datum_registracije, datum_rojstva, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, 'aktiven')");
                $stmt->execute([$ime, $priimek, $uporabnisko_ime, $email, $geslo_hash, $vloga, $datum_rojstva]);
                
                $uspeh = "Registracija uspešna! Sedaj se lahko prijavite.";
            } catch(PDOException $e) {
                $napaka = "Napaka pri registraciji. Poskusite znova.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ustvari račun</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <h1 class="title">Ustvari račun</h1>
            
            <?php if ($napaka): ?>
                <p style="color: red; margin-bottom: 15px;"><?php echo htmlspecialchars($napaka); ?></p>
            <?php endif; ?>
            
            <?php if ($uspeh): ?>
                <p style="color: green; margin-bottom: 15px;"><?php echo htmlspecialchars($uspeh); ?></p>
            <?php endif; ?>

            <form method="POST" action="index_registracija.php">
                <label for="ime">Ime:</label>
                <input type="text" id="ime" name="ime" required>

                <label for="priimek">Priimek:</label>
                <input type="text" id="priimek" name="priimek" required>

                <label for="uporabnisko_ime">Uporabniško ime:</label>
                <input type="text" id="uporabnisko_ime" name="uporabnisko_ime" required>
                
                <label for="datum_rojstva">Datum rojstva:</label>
                <input type="date" id="datum_rojstva" name="datum_rojstva" required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Geslo:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn-submit">Vpis</button>
            </form>

            <a href="index.php" class="login-link">Prijava</a>
        </div>
    </div>
</body>
</html>