<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$ucitelj_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];

if (!je_ucitelj()) {
    die("Dostop zavrnjen. Samo učitelji lahko dodajajo ocene.");
}

global $pdo;
$sporocilo = '';
$oddaje_za_ocenjevanje = [];
$izbran_predmet_id = $_GET['predmet_id'] ?? $_POST['id_predmeta'] ?? null;

// 1. PRIDOBITEV PREDMETOV, KI JIH UČITELJ POUČUJE
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

// 2. PRIKAZ ODDANIH NALOG ZA OCENJEVANJE, ČE JE PREDMET IZBRAN
if ($izbran_predmet_id) {
    // Preverimo, ali je izbran predmet res njegov
    $je_njegov_predmet = false;
    foreach ($predmeti as $p) {
        if ((int)$p['id'] === (int)$izbran_predmet_id) {
            $je_njegov_predmet = true;
            break;
        }
    }

    if (!$je_njegov_predmet) {
        die("Dostop zavrnjen. Ta predmet ni v vaši pristojnosti.");
    }
    
    // Pridobitev oddanih nalog, ki še nimajo ocene (ocena IS NULL)
    try {
        $stmt_oddaje = $pdo->prepare("
            SELECT 
                o.id AS oddaja_id, n.naslov AS naloga_naslov, u.ime, u.priimek, o.datum_oddaje
            FROM oddaje o
            JOIN naloge n ON o.id_naloge = n.id
            JOIN uporabniki u ON o.id_ucenca = u.id
            WHERE n.id_predmeta = ? AND o.ocena IS NULL AND n.id_avtorja = ? 
            ORDER BY u.priimek, n.naslov
        ");
        $stmt_oddaje->execute([$izbran_predmet_id, $ucitelj_id]);
        $oddaje_za_ocenjevanje = $stmt_oddaje->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $sporocilo = "Napaka pri pridobivanju oddaj: " . $e->getMessage();
    }
}


// Preveri, če je prišel direktni id_oddaje (iz pregled_oddanih_nalog.php)
$id_oddaje_direktno = $_GET['id_oddaje'] ?? null;
if ($id_oddaje_direktno) {
    try {
        $stmt = $pdo->prepare("
            SELECT o.id, o.id_naloge, n.id_predmeta, n.naslov as naloga_naslov, 
                   u.ime, u.priimek, o.datum_oddaje, o.ocena, o.komentar
            FROM oddaje o
            JOIN naloge n ON o.id_naloge = n.id
            JOIN uporabniki u ON o.id_ucenca = u.id
            WHERE o.id = ?
        ");
        $stmt->execute([$id_oddaje_direktno]);
        $oddaja_direktno = $stmt->fetch();
        if ($oddaja_direktno) {
            $izbran_predmet_id = $oddaja_direktno['id_predmeta'];
            // Preveri, če učitelj poučuje ta predmet
            $je_njegov_predmet = false;
            foreach ($predmeti as $p) {
                if ((int)$p['id'] === (int)$izbran_predmet_id) {
                    $je_njegov_predmet = true;
                    break;
                }
            }
            if (!$je_njegov_predmet) {
                die("Dostop zavrnjen.");
            }
        }
    } catch (PDOException $e) {
        die("Napaka: " . $e->getMessage());
    }
}

// 3. OBDELAVA FORME ZA OCENJEVANJE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['oddaja_id'])) {
    $oddaja_id = $_POST['oddaja_id'];
    $ocena = $_POST['ocena'] ?? null;
    $komentar = $_POST['komentar'] ?? '';
    $id_predmeta_redirect = $_POST['id_predmeta'] ?? $izbran_predmet_id;

    if (empty($ocena) || $ocena < 1 || $ocena > 10) {
        $sporocilo = "Ocena mora biti med 1 in 10.";
    } else {
        try {
            // Posodobimo tabelo oddaje z oceno in komentarjem
            $stmt = $pdo->prepare("
                UPDATE oddaje 
                SET ocena = :ocena, komentar = :komentar, status = 'ocenjeno', datum_ocenjevanja = NOW()
                WHERE id = :oddaja_id
            ");
            $stmt->execute([
                ':ocena' => $ocena,
                ':komentar' => $komentar,
                ':oddaja_id' => $oddaja_id
            ]);
            $sporocilo = "Ocena uspešno dodana!";

            // Preusmeri nazaj na pregled oddanih nalog
            if (isset($_POST['id_predmeta'])) {
                header("Location: pregled_oddanih_nalog.php?id_predmeta=" . $id_predmeta_redirect . "&uspeh=" . urlencode($sporocilo));
            } else {
                header("Location: dodajOceno.php?predmet_id=" . $izbran_predmet_id . "&uspeh=" . urlencode($sporocilo));
            }
            exit;

        } catch (PDOException $e) {
            $sporocilo = "Napaka pri dodajanju ocene: " . $e->getMessage();
        }
    }
}

// Sporočilo o uspehu iz GET parametra
if (isset($_GET['uspeh'])) {
    $sporocilo = htmlspecialchars($_GET['uspeh']);
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocenjevanje oddaj</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-box { max-width: 800px; margin: 20px auto; padding: 30px; background-color: var(--color-light-beige); border-radius: 15px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
        .form-box h1 { text-align: center; color: var(--color-text); margin-bottom: 25px; }
        .form-box select, .form-box button { margin-top: 15px; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .oddaja-list { margin-top: 20px; }
        .oddaja-item { border: 1px solid var(--color-dark-beige); border-radius: 8px; padding: 15px; margin-bottom: 15px; background-color: var(--color-white); }
        .oddaja-item strong { display: block; margin-bottom: 5px; color: var(--color-text); }
        .oddaja-item .meta { font-size: 0.9em; color: #666; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="content-container">
        <div class="form-box">
            <h1>Ocenjevanje oddanih nalog</h1>
            <?php if (!empty($sporocilo)): ?>
                <p style="color: <?php echo (strpos($sporocilo, 'Napaka') !== false) ? 'red' : 'green'; ?>; text-align: center; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($sporocilo); ?>
                </p>
            <?php endif; ?>

            <form method="GET" action="dodajOceno.php">
                <label for="predmet_id">Izberi predmet za ocenjevanje:</label>
                <select id="predmet_id" name="predmet_id" onchange="this.form.submit()" required>
                    <option value="">-- Izberi predmet --</option>
                    <?php foreach ($predmeti as $predmet): ?>
                        <option value="<?php echo htmlspecialchars($predmet['id']); ?>" 
                                <?php if ((int)$izbran_predmet_id === (int)$predmet['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($predmet['koda'] . ' - ' . $predmet['ime']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ($id_oddaje_direktno && isset($oddaja_direktno)): ?>
                <div class="oddaja-item">
                    <strong>Naloga: <?php echo htmlspecialchars($oddaja_direktno['naloga_naslov']); ?></strong>
                    <div class="meta">Učenec: <?php echo htmlspecialchars($oddaja_direktno['ime'] . ' ' . $oddaja_direktno['priimek']); ?> (Oddano: <?php echo date('d.m.Y H:i', strtotime($oddaja_direktno['datum_oddaje'])); ?>)</div>
                    
                    <form method="POST" action="dodajOceno.php" style="margin-top: 10px;">
                        <input type="hidden" name="oddaja_id" value="<?php echo htmlspecialchars($id_oddaje_direktno); ?>">
                        <input type="hidden" name="id_predmeta" value="<?php echo htmlspecialchars($oddaja_direktno['id_predmeta']); ?>">

                        <label for="ocena_direktno">Ocena (1-10):</label>
                        <input type="number" id="ocena_direktno" name="ocena" min="1" max="10" value="<?php echo $oddaja_direktno['ocena'] ?? ''; ?>" required style="width: 80px; display: inline-block; margin-right: 15px;">

                        <label for="komentar_direktno">Komentar:</label>
                        <textarea id="komentar_direktno" name="komentar" rows="2" style="width: 100%;"><?php echo htmlspecialchars($oddaja_direktno['komentar'] ?? ''); ?></textarea>
                        
                        <button type="submit">Ocenite</button>
                    </form>
                </div>
            <?php elseif ($izbran_predmet_id && empty($oddaje_za_ocenjevanje)): ?>
                <p style="margin-top: 20px; text-align: center;">Za ta predmet ni neoddanih nalog za ocenjevanje.</p>
            <?php elseif (!empty($oddaje_za_ocenjevanje)): ?>
                <div class="oddaja-list">
                    <?php foreach ($oddaje_za_ocenjevanje as $oddaja): ?>
                        <div class="oddaja-item">
                            <strong>Naloga: <?php echo htmlspecialchars($oddaja['naloga_naslov']); ?></strong>
                            <div class="meta">Učenec: <?php echo htmlspecialchars($oddaja['ime'] . ' ' . $oddaja['priimek']); ?> (Oddano: <?php echo date('d.m.Y H:i', strtotime($oddaja['datum_oddaje'])); ?>)</div>
                            
                            <form method="POST" action="dodajOceno.php" style="margin-top: 10px;">
                                <input type="hidden" name="oddaja_id" value="<?php echo htmlspecialchars($oddaja['oddaja_id']); ?>">
                                <input type="hidden" name="id_predmeta" value="<?php echo htmlspecialchars($izbran_predmet_id); ?>">

                                <label for="ocena_<?php echo $oddaja['oddaja_id']; ?>">Ocena (1-10):</label>
                                <input type="number" id="ocena_<?php echo $oddaja['oddaja_id']; ?>" name="ocena" min="1" max="10" required style="width: 80px; display: inline-block; margin-right: 15px;">

                                <label for="komentar_<?php echo $oddaja['oddaja_id']; ?>">Komentar:</label>
                                <textarea id="komentar_<?php echo $oddaja['oddaja_id']; ?>" name="komentar" rows="2" style="width: 100%;"></textarea>
                                
                                <button type="submit">Ocenite</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>