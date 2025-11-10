<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$uporabnik_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];
$ime = $uporabnik['ime'];
$priimek = $uporabnik['priimek'];

if ($vloga !== 'ucenec') {
    header('Location: index.php');
    exit;
}

global $pdo;
$sporocilo = '';

$id_predmeta = isset($_GET['id_predmeta']) ? (int)$_GET['id_predmeta'] : 0;

// Preveri, če učenec obiskuje ta predmet
if ($id_predmeta > 0) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ucenci_predmeti WHERE id_predmeta = ? AND id_ucenca = ? AND status = 'vpisano'");
    $stmt->execute([$id_predmeta, $uporabnik_id]);
    if ($stmt->fetchColumn() == 0) {
        die("Dostop zavrnjen. Ne obiskujete tega predmeta.");
    }
}

// Obdelava oddaje naloge
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['oddaj_nalogo'])) {
    $id_naloge = (int)($_POST['id_naloge'] ?? 0);
    
    if ($id_naloge <= 0) {
        $sporocilo = 'Napačna naloga.';
    } elseif (!isset($_FILES['datoteka']) || $_FILES['datoteka']['error'] !== UPLOAD_ERR_OK) {
        $upload_error = $_FILES['datoteka']['error'] ?? UPLOAD_ERR_NO_FILE;
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'Datoteka presega maksimalno dovoljeno velikost (php.ini).',
            UPLOAD_ERR_FORM_SIZE => 'Datoteka presega maksimalno dovoljeno velikost (form).',
            UPLOAD_ERR_PARTIAL => 'Datoteka je bila le delno naložena.',
            UPLOAD_ERR_NO_FILE => 'Nobena datoteka ni bila izbrana.',
            UPLOAD_ERR_NO_TMP_DIR => 'Manjka začasni direktorij.',
            UPLOAD_ERR_CANT_WRITE => 'Napaka pri pisanju datoteke na disk.',
            UPLOAD_ERR_EXTENSION => 'Nalaganje datoteke je ustavila razširitev PHP.'
        ];
        $sporocilo = $error_messages[$upload_error] ?? 'Napaka pri nalaganju datoteke.';
    } else {
        try {
            // Pridobi podatke o nalogi
            $stmt = $pdo->prepare("SELECT id, naslov, id_predmeta FROM naloge WHERE id = ? AND status = 'aktiven'");
            $stmt->execute([$id_naloge]);
            $naloga = $stmt->fetch();
            
            if (!$naloga) {
                $sporocilo = 'Naloga ni najdena.';
            } else {
                // Preveri, če učenec obiskuje ta predmet
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM ucenci_predmeti WHERE id_predmeta = ? AND id_ucenca = ? AND status = 'vpisano'");
                $stmt->execute([$naloga['id_predmeta'], $uporabnik_id]);
                if ($stmt->fetchColumn() == 0) {
                    $sporocilo = 'Ne obiskujete tega predmeta.';
                } else {
                    // Preveri, če že obstaja oddaja
                    $stmt = $pdo->prepare("SELECT id, pot_do_datoteke FROM oddaje WHERE id_naloge = ? AND id_ucenca = ?");
                    $stmt->execute([$id_naloge, $uporabnik_id]);
                    $obstojeca_oddaja = $stmt->fetch();
                    
                    // Pripravi ime datoteke: Priimek Ime – Naslov naloge
                    $originalno_ime = $_FILES['datoteka']['name'];
                    $koncnica = pathinfo($originalno_ime, PATHINFO_EXTENSION);
                    
                    // Sestavi ime: Priimek Ime – Naslov naloge
                    $novo_ime = $priimek . ' ' . $ime . ' – ' . $naloga['naslov'];
                    // Odstrani neveljavne znake za datoteke (ohrani presledke in –)
                    $novo_ime = preg_replace('/[<>:"|?*\\\\]/', '', $novo_ime); // Odstrani samo neveljavne znake za Windows/Linux
                    $novo_ime = trim($novo_ime); // Odstrani začetne/končne presledke
                    $novo_ime .= '.' . $koncnica;
                    
                    // Uporabi absolutno pot glede na lokacijo skripte
                    $upload_dir = __DIR__ . '/../uploads/oddaje/';
                    if (!is_dir($upload_dir)) {
                        if (!mkdir($upload_dir, 0755, true)) {
                            $sporocilo = 'Napaka pri ustvarjanju direktorija za datoteke.';
                        }
                    }
                    
                    // Preveri, če direktorij obstaja in je zapisljiv
                    if (!is_writable($upload_dir)) {
                        $sporocilo = 'Direktorij za datoteke ni zapisljiv.';
                    } else {
                        $pot_do_datoteke = $upload_dir . $novo_ime;
                        
                        // Če obstaja prejšnja oddaja, jo izbriši
                        if ($obstojeca_oddaja && !empty($obstojeca_oddaja['pot_do_datoteke'])) {
                            $stara_pot = ltrim($obstojeca_oddaja['pot_do_datoteke'], '/');
                            // Poskusi z različnimi potmi
                            if (file_exists($stara_pot)) {
                                unlink($stara_pot);
                            } elseif (file_exists(__DIR__ . '/../' . $stara_pot)) {
                                unlink(__DIR__ . '/../' . $stara_pot);
                            }
                        }
                        
                        if (move_uploaded_file($_FILES['datoteka']['tmp_name'], $pot_do_datoteke)) {
                            // Shrani relativno pot za bazo (glede na src/)
                            $pot_do_datoteke_db = '/uploads/oddaje/' . $novo_ime;
                        
                            if ($obstojeca_oddaja) {
                                // Posodobi obstoječo oddajo
                                $stmt = $pdo->prepare("UPDATE oddaje SET pot_do_datoteke = ?, originalno_ime_datoteke = ?, datum_oddaje = NOW(), status = 'oddano' WHERE id = ?");
                                $stmt->execute([$pot_do_datoteke_db, $originalno_ime, $obstojeca_oddaja['id']]);
                                $sporocilo = 'Naloga uspešno posodobljena.';
                            } else {
                                // Dodaj novo oddajo
                                $stmt = $pdo->prepare("INSERT INTO oddaje (id_naloge, id_ucenca, datum_oddaje, pot_do_datoteke, originalno_ime_datoteke, status) VALUES (?, ?, NOW(), ?, ?, 'oddano')");
                                $stmt->execute([$id_naloge, $uporabnik_id, $pot_do_datoteke_db, $originalno_ime]);
                                $sporocilo = 'Naloga uspešno oddana.';
                            }
                        } else {
                            $sporocilo = 'Napaka pri shranjevanju datoteke. Preverite pravice za pisanje v direktorij.';
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            $sporocilo = 'Napaka: ' . $e->getMessage();
        }
    }
}

// Pridobi predmete, ki jih učenec obiskuje
$stmt = $pdo->prepare("
    SELECT p.id, p.ime, p.koda 
    FROM predmeti p
    INNER JOIN ucenci_predmeti up ON p.id = up.id_predmeta
    WHERE up.id_ucenca = ? AND up.status = 'vpisano' AND p.status = 'aktiven'
    ORDER BY p.ime
");
$stmt->execute([$uporabnik_id]);
$predmeti = $stmt->fetchAll();

// Če je izbran predmet, pridobi naloge in oddaje
$naloge = [];
$oddaje = [];
if ($id_predmeta > 0) {
    // Pridobi naloge za ta predmet
    $stmt = $pdo->prepare("
        SELECT id, naslov, navodila, rok_addaje as rok_oddaje, maksimalna_ocena
        FROM naloge
        WHERE id_predmeta = ? AND status = 'aktiven'
        ORDER BY datum_objave DESC
    ");
    $stmt->execute([$id_predmeta]);
    $naloge = $stmt->fetchAll();
    
    // Pridobi oddaje za te naloge
    if (!empty($naloge)) {
        $naloge_ids = array_column($naloge, 'id');
        $placeholders = implode(',', array_fill(0, count($naloge_ids), '?'));
        $stmt = $pdo->prepare("
            SELECT id_naloge, datum_oddaje, pot_do_datoteke, originalno_ime_datoteke, status, ocena
            FROM oddaje
            WHERE id_naloge IN ($placeholders) AND id_ucenca = ?
        ");
        $stmt->execute(array_merge($naloge_ids, [$uporabnik_id]));
        $oddaje_temp = $stmt->fetchAll();
        
        // Organiziraj oddaje po id_naloge
        foreach ($oddaje_temp as $oddaja) {
            $oddaje[$oddaja['id_naloge']] = $oddaja;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naloge</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .content-box {
            max-width: 1000px;
            margin: 20px auto;
            padding: 30px;
            background-color: var(--color-light-beige);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .content-box h3 {
            margin-bottom: 20px;
            color: var(--color-text);
        }
        .predmet-selector {
            margin-bottom: 20px;
        }
        .predmet-selector select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .naloga-item {
            background-color: var(--color-white);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .naloga-naslov {
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--color-text);
        }
        .naloga-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .naloga-navodila {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .oddaja-form {
            margin-top: 15px;
            padding: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
        }
        .oddaja-form input[type="file"] {
            margin: 10px 0;
        }
        .oddaja-form button {
            background-color: var(--color-dark-beige);
            color: var(--color-text);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        .oddaja-form button:hover {
            background-color: #c9b48c;
        }
        .oddaja-status {
            margin-top: 10px;
            padding: 10px;
            background-color: #e8f5e9;
            border-radius: 5px;
        }
        .oddaja-status.ocenjeno {
            background-color: #c8e6c9;
        }
        .btn-view {
            padding: 5px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .btn-view:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <nav class="main-nav">
        <a href="ocene.php">Ocene</a>
        <a href="urnik.php">Urnik</a>
        <a href="predmeti.php">Spletna učilnica</a>
        <a href="meni.php">Meni</a>
    </nav>
    
    <div class="content-container">
        <div class="content-box">
            <h3>Naloge</h3>
            
            <?php if (!empty($sporocilo)): ?>
                <p style="color: <?php echo (strpos($sporocilo, 'Napaka') !== false) ? 'red' : 'green'; ?>; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($sporocilo); ?>
                </p>
            <?php endif; ?>
            
            <div class="predmet-selector">
                <label for="predmet">Izberi predmet:</label>
                <select id="predmet" name="predmet" onchange="window.location.href='naloge_ucenec.php?id_predmeta=' + this.value">
                    <option value="0">-- Izberi predmet --</option>
                    <?php foreach ($predmeti as $predmet): ?>
                        <option value="<?php echo $predmet['id']; ?>" <?php echo ($id_predmeta == $predmet['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($predmet['koda'] . ' - ' . $predmet['ime']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php if ($id_predmeta > 0): ?>
                <?php if (empty($naloge)): ?>
                    <p>Za ta predmet še ni nalog.</p>
                <?php else: ?>
                    <?php foreach ($naloge as $naloga): ?>
                        <div class="naloga-item">
                            <div class="naloga-naslov"><?php echo htmlspecialchars($naloga['naslov']); ?></div>
                            <div class="naloga-meta">
                                Rok oddaje: <?php echo date('d.m.Y H:i', strtotime($naloga['rok_oddaje'])); ?> | 
                                Maksimalna ocena: <?php echo $naloga['maksimalna_ocena']; ?>
                            </div>
                            <div class="naloga-navodila">
                                <strong>Navodila:</strong><br>
                                <?php echo nl2br(htmlspecialchars($naloga['navodila'])); ?>
                            </div>
                            
                            <?php if (isset($oddaje[$naloga['id']])): ?>
                                <div class="oddaja-status <?php echo $oddaje[$naloga['id']]['status']; ?>">
                                    <strong>Oddano:</strong> <?php echo date('d.m.Y H:i', strtotime($oddaje[$naloga['id']]['datum_oddaje'])); ?><br>
                                    <strong>Datoteka:</strong> <?php echo htmlspecialchars($oddaje[$naloga['id']]['originalno_ime_datoteke']); ?><br>
                                    <?php if ($oddaje[$naloga['id']]['ocena']): ?>
                                        <strong>Ocena:</strong> <?php echo $oddaje[$naloga['id']]['ocena']; ?>/<?php echo $naloga['maksimalna_ocena']; ?><br>
                                    <?php endif; ?>
                                    <?php if ($oddaje[$naloga['id']]['pot_do_datoteke']): ?>
                                        <a href="<?php echo htmlspecialchars($oddaje[$naloga['id']]['pot_do_datoteke']); ?>" target="_blank" class="btn-view">Prikaži datoteko</a>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="oddaja-form">
                                    <form method="POST" action="naloge_ucenec.php?id_predmeta=<?php echo $id_predmeta; ?>" enctype="multipart/form-data">
                                        <input type="hidden" name="id_naloge" value="<?php echo $naloga['id']; ?>">
                                        <p><strong>Ponovna oddaja (povozi prejšnjo datoteko):</strong></p>
                                        <input type="file" name="datoteka" required>
                                        <button type="submit" name="oddaj_nalogo" onclick="return confirm('Ali ste prepričani, da želite povoziti prejšnjo oddajo?')">Oddaj ponovno</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="oddaja-form">
                                    <form method="POST" action="naloge_ucenec.php?id_predmeta=<?php echo $id_predmeta; ?>" enctype="multipart/form-data">
                                        <input type="hidden" name="id_naloge" value="<?php echo $naloga['id']; ?>">
                                        <label for="datoteka_<?php echo $naloga['id']; ?>">Izberi datoteko za oddajo:</label>
                                        <input type="file" id="datoteka_<?php echo $naloga['id']; ?>" name="datoteka" required>
                                        <button type="submit" name="oddaj_nalogo">Oddaj nalogo</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>
                <p>Izberite predmet za prikaz nalog.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

