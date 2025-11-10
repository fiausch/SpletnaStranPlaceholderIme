<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$uporabnik_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];

if ($vloga !== 'ucitelj' && $vloga !== 'administrator') {
    header('Location: index.php');
    exit;
}

global $pdo;
$sporocilo = '';

$id_predmeta = isset($_GET['id_predmeta']) ? (int)$_GET['id_predmeta'] : 0;
if ($id_predmeta <= 0) {
    header('Location: gradiva.php');
    exit;
}

// Preveri, če učitelj poučuje ta predmet (razen če je administrator)
if ($vloga === 'ucitelj') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ucitelji_predmeti WHERE id_predmeta = ? AND id_ucitelja = ?");
    $stmt->execute([$id_predmeta, $uporabnik_id]);
    if ($stmt->fetchColumn() == 0) {
        die("Dostop zavrnjen. Ne poučujete tega predmeta.");
    }
}

// Pridobi podatke o predmetu
$stmt = $pdo->prepare("SELECT id, ime, koda FROM predmeti WHERE id = ?");
$stmt->execute([$id_predmeta]);
$predmet = $stmt->fetch();

if (!$predmet) {
    header('Location: gradiva.php');
    exit;
}

// Obdelava forme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naslov = trim($_POST['naslov'] ?? '');
    $vsebina = trim($_POST['vsebina'] ?? '');
    $tip = $_POST['tip'] ?? 'dokument';
    $povezava = trim($_POST['povezava'] ?? '');
    
    if (empty($naslov)) {
        $sporocilo = 'Naslov je obvezen.';
    } else {
        try {
            $pot_do_datoteke = null;
            
            // Obdelava naložene datoteke
            if (isset($_FILES['datoteka']) && $_FILES['datoteka']['error'] === UPLOAD_ERR_OK) {
                // Uporabi absolutno pot glede na lokacijo skripte
                $upload_dir = __DIR__ . '/../uploads/gradiva/';
                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true)) {
                        $sporocilo = 'Napaka pri ustvarjanju direktorija za datoteke.';
                    }
                }
                
                if (is_writable($upload_dir)) {
                    $originalno_ime = $_FILES['datoteka']['name'];
                    $koncnica = pathinfo($originalno_ime, PATHINFO_EXTENSION);
                    $novo_ime = uniqid() . '_' . basename($originalno_ime);
                    $pot_do_datoteke = $upload_dir . $novo_ime;
                    
                    if (move_uploaded_file($_FILES['datoteka']['tmp_name'], $pot_do_datoteke)) {
                        $pot_do_datoteke = '/uploads/gradiva/' . $novo_ime;
                    } else {
                        $sporocilo = 'Napaka pri shranjevanju datoteke. Preverite pravice za pisanje.';
                        $pot_do_datoteke = null;
                    }
                } else {
                    $sporocilo = 'Direktorij za datoteke ni zapisljiv.';
                    $pot_do_datoteke = null;
                }
            } elseif (!empty($povezava)) {
                $pot_do_datoteke = $povezava;
            }
            
            if (empty($sporocilo)) {
                $stmt = $pdo->prepare("INSERT INTO gradiva (naslov, vsebina, tip, pot_do_datoteke, id_predmeta, id_avtorja, datum_objave, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'aktiven')");
                $stmt->execute([$naslov, $vsebina, $tip, $pot_do_datoteke, $id_predmeta, $uporabnik_id]);
                $sporocilo = 'Gradivo uspešno dodano.';
                header('Location: gradiva.php?id_predmeta=' . $id_predmeta);
                exit;
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
    <title>Dodaj gradivo</title>
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
        .form-box input[type="url"],
        .form-box input[type="file"],
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
            <h1>Dodaj gradivo - <?php echo htmlspecialchars($predmet['koda'] . ' - ' . $predmet['ime']); ?></h1>
            <?php if (!empty($sporocilo)): ?>
                <p style="color: red; text-align: center; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($sporocilo); ?>
                </p>
            <?php endif; ?>
            
            <form method="POST" action="dodaj_gradivo.php?id_predmeta=<?php echo $id_predmeta; ?>" enctype="multipart/form-data">
                <label for="naslov">Naslov *</label>
                <input type="text" id="naslov" name="naslov" required>
                
                <label for="tip">Tip gradiva *</label>
                <select id="tip" name="tip" required>
                    <option value="dokument">Dokument</option>
                    <option value="video">Video</option>
                    <option value="povezava">Povezava</option>
                    <option value="drugi">Drugi</option>
                </select>
                
                <label for="vsebina">Opis/Vsebina</label>
                <textarea id="vsebina" name="vsebina" rows="5"></textarea>
                
                <label for="datoteka">Datoteka (ali povezava spodaj)</label>
                <input type="file" id="datoteka" name="datoteka">
                
                <label for="povezava">Povezava (če ni datoteka)</label>
                <input type="url" id="povezava" name="povezava" placeholder="https://...">
                
                <button type="submit">Dodaj gradivo</button>
            </form>
            <div class="nav-back">
                <a href="gradiva.php?id_predmeta=<?php echo $id_predmeta; ?>">⬅ Nazaj na gradiva</a>
            </div>
        </div>
    </div>
</body>
</html>

