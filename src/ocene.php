<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$vloga = $uporabnik['vloga'];
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocene</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="main-nav">
        <a href="#" class="active">Ocene</a>
        <a href="urnik.php">Urnik</a>
        <a href="predmeti.php">Spletna učilnica</a>
        <?php if ($vloga === 'ucitelj' || $vloga === 'administrator'): ?>
            <a href="list_ucencov.php">Učenci</a>
        <?php endif; ?>
        <?php if ($vloga === 'administrator'): ?>
            <a href="#">Profesorji</a>
        <?php endif; ?>
        <a href="meni.php">Meni</a>
    </nav>
    
    <div class="content-container">
        <div class="scores-box">
            
            <div class="scores-header">
                <h3>Predmet</h3>
                <h3>Ocena</h3>
            </div>
            
            <div class="subject-row">
                <div class="subject-name">Ime predmeta 1</div>
                <div class="subject-score">x</div>
            </div>
            
            <div class="subject-row">
                <div class="subject-name">Ime predmeta n</div>
                <div class="subject-score">x</div>
            </div>
            
            <?php if ($vloga === 'ucitelj' || $vloga === 'administrator'): ?>
            <button class="btn-edit">Uredi</button>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>
