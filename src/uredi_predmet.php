<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$uporabnik_id = $uporabnik['id'];
$vloga = $uporabnik['vloga'];

global $pdo;

function lahko_uredi_predmet_local($id_predmeta, $uporabnik_id, $vloga) {
	if ($vloga === 'administrator') {
		return true;
	}
	if ($vloga === 'ucitelj') {
		global $pdo;
		$stmt = $pdo->prepare("SELECT COUNT(*) FROM ucitelji_predmeti WHERE id_predmeta = ? AND id_ucitelja = ?");
		$stmt->execute([$id_predmeta, $uporabnik_id]);
		return $stmt->fetchColumn() > 0;
	}
	return false;
}

$id_predmeta = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_predmeta <= 0) {
	header('Location: predmeti.php');
	exit;
}

// Preveri dovoljenja
if (!lahko_uredi_predmet_local($id_predmeta, $uporabnik_id, $vloga)) {
	die("Dostop zavrnjen.");
}

$sporocilo = '';

// Obdelava posodobitve
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$ime = $_POST['ime'] ?? '';
	$koda = $_POST['koda'] ?? '';
	$opis = $_POST['opis'] ?? '';
	$status = $_POST['status'] ?? 'aktiven';

	if (empty($ime) || empty($koda)) {
		$sporocilo = 'Polji Ime in Koda sta obvezni.';
	} else {
		try {
			$stmt = $pdo->prepare("UPDATE predmeti SET ime = ?, koda = ?, opis = ?, status = ? WHERE id = ?");
			$stmt->execute([$ime, $koda, $opis, $status, $id_predmeta]);
			$sporocilo = 'Predmet uspešno posodobljen.';
		} catch (PDOException $e) {
			$sporocilo = 'Napaka pri posodabljanju: ' . $e->getMessage();
		}
	}
}

// Pridobi trenutne podatke o predmetu
$stmt = $pdo->prepare("SELECT id, ime, koda, opis, status FROM predmeti WHERE id = ?");
$stmt->execute([$id_predmeta]);
$predmet = $stmt->fetch();

if (!$predmet) {
	die('Predmet ni najden.');
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Uredi predmet</title>
	<link rel="stylesheet" href="styles.css">
	<style>
		.form-box { max-width: 700px; margin: 20px auto; padding: 30px; background-color: var(--color-light-beige); border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
		.form-box h1 { text-align: center; color: var(--color-text); margin-bottom: 25px; }
		.form-box label { display: block; margin-top: 15px; font-weight: bold; color: var(--color-text); }
		.form-box input[type="text"], .form-box textarea, .form-box select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
		.form-box button { display: block; width: 160px; padding: 10px; margin: 25px auto 0 auto; border: none; border-radius: 5px; background-color: var(--color-dark-beige); color: var(--color-text); font-weight: 600; cursor: pointer; transition: background-color 0.2s; }
		.form-box button:hover { background-color: #c9b48c; }
		.nav-back { text-align: center; margin-top: 15px; }
		.nav-back a { color: var(--color-text); text-decoration: none; }
		.nav-back a:hover { text-decoration: underline; }
	</style>
</head>
<body>
	<div class="content-container">
		<div class="form-box">
			<h1>Uredi predmet</h1>
			<?php if (!empty($sporocilo)): ?>
				<p style="color: <?php echo (strpos($sporocilo, 'Napaka') !== false) ? 'red' : 'green'; ?>; text-align: center; margin-bottom: 20px;">
					<?php echo htmlspecialchars($sporocilo); ?>
				</p>
			<?php endif; ?>

			<form method="POST" action="uredi_predmet.php?id=<?php echo (int)$predmet['id']; ?>">
				<label for="ime">Ime</label>
				<input type="text" id="ime" name="ime" value="<?php echo htmlspecialchars($predmet['ime']); ?>" required>

				<label for="koda">Koda</label>
				<input type="text" id="koda" name="koda" value="<?php echo htmlspecialchars($predmet['koda']); ?>" required>

				<label for="opis">Opis</label>
				<textarea id="opis" name="opis" rows="5"><?php echo htmlspecialchars($predmet['opis'] ?? ''); ?></textarea>

				<label for="status">Status</label>
				<select id="status" name="status">
					<option value="aktiven" <?php echo ($predmet['status'] === 'aktiven') ? 'selected' : ''; ?>>Aktiven</option>
					<option value="neaktiven" <?php echo ($predmet['status'] === 'neaktiven') ? 'selected' : ''; ?>>Neaktiven</option>
				</select>

				<button type="submit">Shrani spremembe</button>
			</form>
			<div class="nav-back">
				<a href="predmeti.php">⬅ Nazaj na Predmeti</a>
			</div>
		</div>
	</div>
</body>
</html>


