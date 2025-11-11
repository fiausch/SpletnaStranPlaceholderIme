<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$vloga = $uporabnik['vloga'];
$ime = $uporabnik['ime'];
$priimek = $uporabnik['priimek'];
$email = $uporabnik['email'];
$datum_rojstva = $uporabnik['datum_rojstva'];
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="profile-container">
        
        <header class="profile-header">
            <div class="user-info">
                <span class="user-icon">&#128100;</span> 
                <?php echo htmlspecialchars($ime . ' ' . $priimek); ?>
            </div>
            <form method="POST" action="izpisi.php" style="display: inline;">
                <button type="submit" class="btn-logout">Izpis</button>
            </form>
        </header>

        <div class="left-column">
            
            <section class="box user-data-box">
                <h3>Uporabnikovi podatki</h3>
                
                <div class="data-item">
                    <div class="data-label">E-mail:</div>
                    <input type="text" class="data-input" value="<?php echo htmlspecialchars($email); ?>" disabled>
                </div>
                
                <div class="data-item">
                    <div class="data-label">Datum rojstva:</div>
                    <input type="text" class="data-input" value="<?php echo $datum_rojstva ? htmlspecialchars($datum_rojstva) : 'Ni podatka'; ?>" disabled>
                </div>
                
                <?php if ($vloga === 'ucenec'): ?>
                <div class="data-item">
                    <div class="data-label">Razred:</div>
                    <input type="text" class="data-input" value="<?php echo $razred ? htmlspecialchars($razred) : 'Ni podatka'; ?>" disabled>
                </div>
                <?php endif; ?>
            </section>
            
            <section class="box other-box">
                <h3>Drugo</h3>
                <ul>
                    <?php if ($vloga === 'ucenec'): ?>
                        <li><a href="predmeti.php">Spletna učilnica</a></li>
                        <li><a href="moji_predmeti_ucenec.php">Moji predmeti</a></li>
                        <li><a href="urnik.php">Urnik</a></li>
                        <li><a href="ocene.php">Ocene</a></li>
                    <?php elseif ($vloga === 'ucitelj'): ?>
                        <li><a href="urnik.php">Urnik</a></li>
                        <li><a href="list_ucencov.php">Učenci</a></li>
                        <li><a href="predmeti.php">Spletna učilnica</a></li>
                        <li><a href="ocene.php">Ocene</a></li>
                    <?php elseif ($vloga === 'administrator'): ?>
                        <li><a href="ocene.php">Ocene</a></li>
                        <li><a href="urnik.php">Urnik</a></li>
                        <li><a href="predmeti.php">Spletna učilnica</a></li>
                        <li><a href="upravljanje_ucenci.php">Učenci</a></li>
                        <li><a href="upravljanje_ucitelji.php">Profesorji</a></li>
                    <?php endif; ?>
                </ul>
            </section>
        </div>

        <section class="box subjects-box">
            <h3>Predmeti</h3>
            <ul class="subjects-list">
                <li>SMV- Stroka Moderne vsebine</li>
                <li>NRPA - Načrtovanje in razvoj programskih aplikacij</li>
                <li>NUPB - Napredna uporaba podatkovnih baz</li>
            </ul>
        </section>
    </div>
</body>
</html>
