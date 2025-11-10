<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$vloga = $uporabnik['vloga'];

// Ta stran je na voljo samo za profesorje in administratorje
if ($vloga !== 'ucitelj' && $vloga !== 'administrator') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Učenci</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav class="main-nav">
        <a href="ocene.php">Ocene</a>
        <a href="urnik.php">Urnik</a>
        <a href="predmeti.php">Spletna učilnica</a>
        <a href="#" class="active">Učenci</a>
        <?php if ($vloga === 'administrator'): ?>
            <a href="upravljanje_ucitelji.php">Profesorji</a>
        <?php endif; ?>
        <a href="meni.php">Meni</a>
    </nav>
    
    <div class="content-container">
        <div class="table-box">
            
            <div class="student-table">
                <div class="table-header">Ime</div>
                <div class="table-header">Priimek</div>
                <div class="table-header">Predmet/Ocena</div>
                <div class="table-header">Opravičene ure</div>
                <div class="table-header">Neopravičene ure</div>

                <div class="table-data-highlighted">
                    <div class="highlight-cell">Ime 1</div>
                    <div class="highlight-cell">Priimek 1</div>
                    <div class="highlight-cell"></div>
                    <div class="highlight-cell"></div>
                    <div class="highlight-cell"></div>
                </div>

                <div class="table-data-highlighted">
                    <div class="highlight-cell">Ime 2</div>
                    <div class="highlight-cell">Priimek 2</div>
                    <div class="highlight-cell"></div>
                    <div class="highlight-cell"></div>
                    <div class="highlight-cell"></div>
                </div>
                
            </div>
            
            <button class="btn-edit">Uredi</button>

        </div>
    </div>
</body>
</html>
