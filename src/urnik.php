<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$uporabnik_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];
global $pdo;

$urnik_podatki = [];
$sporocilo = "";

try {
    // PRIDOBIVANJE PODATKOV ZA URNIK (Primer za učenca)
    if ($vloga === 'ucenec') {
        // Opomba: Za polno funkcionalnost bi morali najprej pridobiti razred učenca 
        // in nato urnik za ta razred. Za začetek bomo pridobili urnik neposredno.
        
        // Ta poizvedba zahteva dodatne tabele (npr. razredi, urnik_razredi) ali pa 
        // se bo uporabljala predpostavka, da vsak učenec sledi svojemu razredu.
        
        // Ker urnik ni definiran v vaši PB, je tukaj splošen primer,
        // ki zahteva, da sami definirate logiko pridobivanja.
        
        // OSNUTKOVNA POIZVEDBA:
        // $stmt_urnik = $pdo->prepare("SELECT ... FROM urnik WHERE id_razreda = ? ORDER BY dan, ura");
        // $stmt_urnik->execute([$uporabnik['id_razreda']]); // Predpostavimo, da je id_razreda v tabeli uporabniki
        // $urnik_podatki = $stmt_urnik->fetchAll(PDO::FETCH_ASSOC);
        
        $sporocilo = "Trenutno ni podatkov za urnik. Dodajte tabelo 'urnik' v bazo in določite relacijo med učencem in urnikom.";

    } elseif ($vloga === 'ucitelj') {
        // PRIDOBIVANJE PODATKOV ZA UČITELJA (njegove ure)
        // Podobno, potrebna tabela urnik z relacijo do predmetov in učiteljev.
        
        // $stmt_urnik = $pdo->prepare("SELECT ... FROM urnik JOIN predmeti p ON ... JOIN ucitelji_predmeti up ON ... WHERE up.id_ucitelja = ? ORDER BY dan, ura");
        // $stmt_urnik->execute([$uporabnik_id]);
        // $urnik_podatki = $stmt_urnik->fetchAll(PDO::FETCH_ASSOC);

        $sporocilo = "Trenutno ni podatkov za urnik. Dodajte tabelo 'urnik' v bazo.";
        
    } elseif ($vloga === 'administrator') {
        // PRIDOBIVANJE VSEH PODATKOV (če je to potrebno)
        // $stmt_urnik = $pdo->query("SELECT * FROM urnik ORDER BY dan, ura");
        // $urnik_podatki = $stmt_urnik->fetchAll(PDO::FETCH_ASSOC);
        
        $sporocilo = "Trenutno ni podatkov za urnik. Dodajte tabelo 'urnik' v bazo.";
    }

} catch (PDOException $e) {
    $sporocilo = "Napaka pri pridobivanju urnika: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Urnik</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Dodatni stili za tabelo urnika, če jih ni v styles.css */
        .schedule-box { min-height: 500px; padding: 30px; background-color: var(--color-light-beige); border-radius: 15px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); position: relative; }
        .schedule-box table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: var(--color-white); border-radius: 8px; overflow: hidden; }
        .schedule-box th, .schedule-box td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .schedule-box th { background-color: var(--color-dark-beige); color: var(--color-text); font-weight: bold; }
        .schedule-box tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <nav class="main-nav">
        <a href="ocene.php">Ocene</a>
        <a href="#" class="active">Urnik</a>
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
        <div class="schedule-box">
            <h3>Urnik</h3>
            
            <?php if (!empty($sporocilo)): ?>
                <p style="color: red; text-align: center; margin-top: 20px;"><?php echo htmlspecialchars($sporocilo); ?></p>
            <?php elseif (!empty($urnik_podatki)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Ura</th>
                            <th>Ponedeljek</th>
                            <th>Torek</th>
                            </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            <?php else: ?>
                 <p style="text-align: center; margin-top: 20px;">Za vašo vlogo trenutno ni podatkov za urnik.</p>
            <?php endif; ?>

            <?php if ($vloga === 'ucitelj' || $vloga === 'administrator'): ?>
                <button class="btn-edit" onclick="window.location.href='uredi_urnik.php'">Uredi</button>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>