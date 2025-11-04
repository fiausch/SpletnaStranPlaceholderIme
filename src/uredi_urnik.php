<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$vloga = $uporabnik['vloga'];
$uporabnik_id = $uporabnik['id'];

if ($vloga !== 'ucitelj' && $vloga !== 'administrator') {
	die('Dostop zavrnjen.');
}

global $pdo;

function getPredmetiZaUporabnika(PDO $pdo, int $userId, string $vloga): array {
	if ($vloga === 'administrator') {
		$stmt = $pdo->query("SELECT id, koda, ime FROM predmeti WHERE status = 'aktiven' ORDER BY ime");
		return $stmt->fetchAll();
	}
	$stmt = $pdo->prepare("
		SELECT p.id, p.koda, p.ime
		FROM predmeti p
		JOIN ucitelji_predmeti up ON up.id_predmeta = p.id
		WHERE up.id_ucitelja = ? AND p.status = 'aktiven'
		ORDER BY p.ime
	");
	$stmt->execute([$userId]);
	return $stmt->fetchAll();
}

function getUcitelji(PDO $pdo): array {
	$stmt = $pdo->prepare("SELECT id, ime, priimek FROM uporabniki WHERE vloga='ucitelj' AND status='aktiven' ORDER BY priimek, ime");
	$stmt->execute();
	return $stmt->fetchAll();
}

$predmeti = getPredmetiZaUporabnika($pdo, $uporabnik_id, $vloga);
$ucitelji = ($vloga === 'administrator') ? getUcitelji($pdo) : [];

$sporocilo = '';
$napaka = '';

$days = ['pon'=>'Ponedeljek','tor'=>'Torek','sre'=>'Sreda','cet'=>'Četrtek','pet'=>'Petek'];

// CREATE/UPDATE/DELETE
$action = $_POST['action'] ?? null;

try {
	if ($action === 'create' || $action === 'update') {
		$id = (int)($_POST['id'] ?? 0);
		$dan = $_POST['dan'] ?? '';
		$ura = (int)($_POST['ura'] ?? 0);
		$id_predmeta = (int)($_POST['id_predmeta'] ?? 0);
		$id_ucitelja = ($vloga === 'administrator')
			? (int)($_POST['id_ucitelja'] ?? 0)
			: $uporabnik_id;
		$ucilnica = trim($_POST['ucilnica'] ?? '');
		$opomba = trim($_POST['opomba'] ?? '');
		$status = $_POST['status'] ?? 'aktiven';

		if (!isset($days[$dan]) || $ura <= 0 || $id_predmeta <= 0 || $id_ucitelja <= 0) {
			$napaka = 'Izpolni vsa obvezna polja.';
		} else {
			if ($vloga === 'ucitelj') {
				$stmtChk = $pdo->prepare("
					SELECT COUNT(*)
					FROM ucitelji_predmeti
					WHERE id_ucitelja = ? AND id_predmeta = ?
				");
				$stmtChk->execute([$id_ucitelja, $id_predmeta]);
				if ($stmtChk->fetchColumn() == 0) {
					throw new RuntimeException('Ne smete dodati termina za predmet, ki ga ne poučujete.');
				}
			}

			if ($action === 'create') {
				$stmt = $pdo->prepare("
					INSERT INTO urnik (dan, ura, id_predmeta, id_ucitelja, ucilnica, opomba, status)
					VALUES (?, ?, ?, ?, ?, ?, ?)
				");
				$stmt->execute([$dan, $ura, $id_predmeta, $id_ucitelja, $ucilnica, $opomba, $status]);
				$sporocilo = 'Termin dodan.';
			} else {
				$stmtAuth = $pdo->prepare("
					SELECT id_ucitelja FROM urnik WHERE id = ?
				");
				$stmtAuth->execute([$id]);
				$row = $stmtAuth->fetch();
				if (!$row) {
					throw new RuntimeException('Termin ne obstaja.');
				}
				if ($vloga === 'ucitelj' && (int)$row['id_ucitelja'] !== $uporabnik_id) {
					throw new RuntimeException('Dostop zavrnjen.');
				}

				$stmt = $pdo->prepare("
					UPDATE urnik
					SET dan = ?, ura = ?, id_predmeta = ?, id_ucitelja = ?, ucilnica = ?, opomba = ?, status = ?
					WHERE id = ?
				");
				$stmt->execute([$dan, $ura, $id_predmeta, $id_ucitelja, $ucilnica, $opomba, $status, $id]);
				$sporocilo = 'Termin posodobljen.';
			}
		}
	} elseif ($action === 'delete') {
		$id = (int)($_POST['id'] ?? 0);
		$stmtAuth = $pdo->prepare("SELECT id_ucitelja FROM urnik WHERE id = ?");
		$stmtAuth->execute([$id]);
		$row = $stmtAuth->fetch();
		if (!$row) {
			throw new RuntimeException('Termin ne obstaja.');
		}
		if ($vloga === 'ucitelj' && (int)$row['id_ucitelja'] !== $uporabnik_id) {
			throw new RuntimeException('Dostop zavrnjen.');
		}
		$stmt = $pdo->prepare("DELETE FROM urnik WHERE id = ?");
		$stmt->execute([$id]);
		$sporocilo = 'Termin izbrisan.';
	}
} catch (Throwable $e) {
	$napaka = $e->getMessage();
}

// LIST
if ($vloga === 'administrator') {
	$stmt = $pdo->query("
		SELECT u.id, u.dan, u.ura, u.ucilnica, u.opomba, u.status,
		       p.ime AS predmet_ime, p.koda AS predmet_koda,
		       t.ime AS uc_ime, t.priimek AS uc_priimek
		FROM urnik u
		JOIN predmeti p ON p.id = u.id_predmeta
		JOIN uporabniki t ON t.id = u.id_ucitelja
		ORDER BY FIELD(u.dan,'pon','tor','sre','cet','pet'), u.ura, p.ime
	");
	$termini = $stmt->fetchAll();
} else {
	$stmt = $pdo->prepare("
		SELECT u.id, u.dan, u.ura, u.ucilnica, u.opomba, u.status,
		       p.ime AS predmet_ime, p.koda AS predmet_koda
		FROM urnik u
		JOIN predmeti p ON p.id = u.id_predmeta
		WHERE u.id_ucitelja = ?
		ORDER BY FIELD(u.dan,'pon','tor','sre','cet','pet'), u.ura, p.ime
	");
	$stmt->execute([$uporabnik_id]);
	$termini = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Uredi urnik</title>
	<link rel="stylesheet" href="styles.css">
	<style>
		.editor-box { max-width: 1100px; margin: 20px auto; padding: 30px; background-color: var(--color-light-beige); border-radius: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
		.grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
		.table { background: #fff; border: 1px solid #e8dec7; border-radius: 10px; overflow: hidden; }
		.table-header, .table-row { display: grid; grid-template-columns: 100px 70px 1fr 1fr 120px 80px 80px; gap: 8px; padding: 10px 12px; align-items: center; }
		.table-header { background: #f6f0dd; font-weight: bold; }
		.row-alt { background: #fbf8ef; }
		.form { background: #fff; border: 1px solid #e8dec7; border-radius: 10px; padding: 16px; }
		.form h3 { margin-bottom: 10px; color: var(--color-text); text-align: center; }
		.form label { display: block; margin-top: 10px; font-weight: bold; }
		.form select, .form input[type="text"], .form input[type="number"] { width: 100%; padding: 8px; margin-top: 4px; border: 1px solid #ccc; border-radius: 6px; }
		.row-actions { display: flex; gap: 8px; }
		.btn { padding: 6px 12px; border: none; border-radius: 6px; background: var(--color-dark-beige); color: var(--color-text); cursor: pointer; }
		.btn.danger { background: #d08989; }
		.note { text-align: center; margin: 10px 0; }
		.back { text-align: center; margin-top: 16px; }
		.back a { color: var(--color-text); text-decoration: none; }
	</style>
</head>
<body>
	<div class="content-container">
		<div class="editor-box">
			<h1>Uredi urnik</h1>

			<?php if (!empty($napaka)): ?>
				<p class="note" style="color: red;">&lt;?php echo htmlspecialchars($napaka); ?&gt;</p>
			<?php elseif (!empty($sporocilo)): ?>
				<p class="note" style="color: green;">&lt;?php echo htmlspecialchars($sporocilo); ?&gt;</p>
			<?php endif; ?>

			<div class="grid">
				<div class="table">
					<div class="table-header">
						<div>Dan</div>
						<div>Ura</div>
						<div>Predmet</div>
						<?php if ($vloga === 'administrator'): ?>
						<div>Učitelj</div>
						<?php else: ?>
						<div>Učilnica</div>
						<?php endif; ?>
						<div>Opomba</div>
						<div>Status</div>
						<div>Akcije</div>
					</div>
					<?php foreach ($termini as $i => $t): ?>
						<div class="table-row <?php echo ($i % 2 === 0) ? 'row-alt' : ''; ?>">
							<div><?php echo htmlspecialchars($days[$t['dan']] ?? $t['dan']); ?></div>
							<div><?php echo (int)$t['ura']; ?></div>
							<div><?php echo htmlspecialchars(($t['predmet_koda'] ?? '').' '.($t['predmet_ime'] ?? '')); ?></div>
							<?php if ($vloga === 'administrator'): ?>
								<div><?php echo htmlspecialchars(($t['uc_ime'] ?? '').' '.($t['uc_priimek'] ?? '')); ?></div>
							<?php else: ?>
								<div><?php echo htmlspecialchars($t['ucilnica'] ?? ''); ?></div>
							<?php endif; ?>
							<div><?php echo htmlspecialchars($t['opomba'] ?? ''); ?></div>
							<div><?php echo htmlspecialchars($t['status']); ?></div>
							<div class="row-actions">
								<form method="post" action="uredi_urnik.php">
									<input type="hidden" name="action" value="delete">
									<input type="hidden" name="id" value="<?php echo (int)$t['id']; ?>">
									<button class="btn danger" type="submit">Izbriši</button>
								</form>
							</div>
						</div>
					<?php endforeach; ?>
					<?php if (empty($termini)): ?>
						<div class="table-row" style="grid-template-columns: 1fr;">
							<div style="padding: 14px;">Ni terminov.</div>
						</div>
					<?php endif; ?>
				</div>

				<div class="form">
					<h3>Dodaj / Posodobi termin</h3>
					<form method="post" action="uredi_urnik.php">
						<input type="hidden" name="action" value="create">
						<label for="dan">Dan</label>
						<select id="dan" name="dan" required>
							<?php foreach ($days as $k => $v): ?>
								<option value="<?php echo $k; ?>"><?php echo $v; ?></option>
							<?php endforeach; ?>
						</select>

						<label for="ura">Ura (1-10)</label>
						<input type="number" id="ura" name="ura" min="1" max="15" required>

						<label for="id_predmeta">Predmet</label>
						<select id="id_predmeta" name="id_predmeta" required>
							<option value="">— izberi —</option>
							<?php foreach ($predmeti as $p): ?>
								<option value="<?php echo (int)$p['id']; ?>">
									<?php echo htmlspecialchars($p['koda'].' - '.$p['ime']); ?>
								</option>
							<?php endforeach; ?>
						</select>

						<?php if ($vloga === 'administrator'): ?>
							<label for="id_ucitelja">Učitelj</label>
							<select id="id_ucitelja" name="id_ucitelja" required>
								<option value="">— izberi —</option>
								<?php foreach ($ucitelji as $u): ?>
									<option value="<?php echo (int)$u['id']; ?>">
										<?php echo htmlspecialchars($u['priimek'].' '.$u['ime']); ?>
									</option>
								<?php endforeach; ?>
							</select>
						<?php endif; ?>

						<label for="ucilnica">Učilnica</label>
						<input type="text" id="ucilnica" name="ucilnica" placeholder="npr. 3A">

						<label for="opomba">Opomba</label>
						<input type="text" id="opomba" name="opomba" placeholder="">

						<label for="status">Status</label>
						<select id="status" name="status">
							<option value="aktiven" selected>Aktiven</option>
							<option value="neaktiven">Neaktiven</option>
						</select>

						<div style="text-align:center; margin-top:14px;">
							<button class="btn" type="submit">Shrani</button>
						</div>
					</form>
				</div>
			</div>

			<div class="back">
				<a href="urnik.php">⬅ Nazaj na Urnik</a>
			</div>
		</div>
	</div>
</body>
</html>
