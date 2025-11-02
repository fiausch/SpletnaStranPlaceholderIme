<?php
session_start();
require_once 'config.php';

// Če je že prijavljen, preusmeri
if (isset($_SESSION['prijavljen']) && $_SESSION['prijavljen'] === true) {
    header('Location: meni.php');
    exit;
}

// Obdelava prijave
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $geslo = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($geslo)) {
        try {
            $stmt = $pdo->prepare("SELECT id, ime, priimek, email, geslo, vloga, status FROM uporabniki WHERE email = ? AND status = 'aktiven'");
            $stmt->execute([$email]);
            $uporabnik = $stmt->fetch();
            
            if ($uporabnik && password_verify($geslo, $uporabnik['geslo'])) {
                // Uspešna prijava
                $_SESSION['prijavljen'] = true;
                $_SESSION['uporabnik_id'] = $uporabnik['id'];
                $_SESSION['vloga'] = $uporabnik['vloga'];
                $_SESSION['ime'] = $uporabnik['ime'];
                $_SESSION['priimek'] = $uporabnik['priimek'];
                
                // Shrani v piškotke (30 dni)
                setcookie('prijavljen', 'true', time() + (30 * 24 * 60 * 60), '/');
                setcookie('uporabnik_id', $uporabnik['id'], time() + (30 * 24 * 60 * 60), '/');
                
                header('Location: meni.php');
                exit;
            } else {
                $napaka = "Napačen e-mail ali geslo.";
            }
        } catch(PDOException $e) {
            $napaka = "Napaka pri prijavi. Poskusite znova.";
        }
    } else {
        $napaka = "Prosim izpolnite vsa polja.";
    }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="title">Prijava</h1>
            
            <?php if (isset($napaka)): ?>
                <p style="color: red; margin-bottom: 15px;"><?php echo htmlspecialchars($napaka); ?></p>
            <?php endif; ?>

            <form method="POST" action="index_prijava.php">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Geslo:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit" class="btn-submit">Vpis</button>
            </form>

            <a href="index_registracija.php" class="create-account-link">Ustvari račun</a>
        </div>
    </div>
</body>
</html>