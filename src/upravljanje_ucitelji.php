<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$vloga = $uporabnik['vloga'];

// Samo administratorji lahko dostopajo
if ($vloga !== 'administrator') {
    header('Location: index.php');
    exit;
}

global $pdo;
$sporocilo = '';

// Obdelava brisanja
if (isset($_GET['brisi']) && is_numeric($_GET['brisi'])) {
    $id_ucitelja = (int)$_GET['brisi'];
    try {
        // Preveri, če učitelj obstaja in je res učitelj
        $stmt = $pdo->prepare("SELECT id, vloga FROM uporabniki WHERE id = ? AND vloga = 'ucitelj'");
        $stmt->execute([$id_ucitelja]);
        $ucitelj = $stmt->fetch();
        
        if ($ucitelj) {
            // Nastavi status na neaktiven namesto brisanja (zaradi referenčne integritete)
            $stmt = $pdo->prepare("UPDATE uporabniki SET status = 'neaktiven' WHERE id = ?");
            $stmt->execute([$id_ucitelja]);
            $sporocilo = 'Učitelj uspešno odstranjen.';
        } else {
            $sporocilo = 'Učitelj ni najden.';
        }
    } catch (PDOException $e) {
        $sporocilo = 'Napaka pri brisanju: ' . $e->getMessage();
    }
}

// Pridobivanje vseh učiteljev
try {
    $stmt = $pdo->prepare("SELECT id, ime, priimek, email, uporabnisko_ime, status FROM uporabniki WHERE vloga = 'ucitelj' ORDER BY priimek, ime");
    $stmt->execute();
    $ucitelji = $stmt->fetchAll();
} catch (PDOException $e) {
    $ucitelji = [];
    $sporocilo = 'Napaka pri pridobivanju učiteljev: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravljanje z učitelji</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .table-box {
            max-width: 1200px;
            margin: 20px auto;
            padding: 30px;
            background-color: var(--color-light-beige);
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .table-box h3 {
            margin-bottom: 20px;
            color: var(--color-text);
        }
        .btn-add {
            background-color: var(--color-dark-beige);
            color: var(--color-text);
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .btn-add:hover {
            background-color: #c9b48c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--color-white);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: var(--color-dark-beige);
            color: var(--color-text);
            font-weight: bold;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn-edit, .btn-delete {
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-edit {
            background-color: #4CAF50;
            color: white;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        .btn-edit:hover {
            background-color: #45a049;
        }
        .btn-delete:hover {
            background-color: #da190b;
        }
        .status-aktiven {
            color: green;
            font-weight: bold;
        }
        .status-neaktiven {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="main-nav">
        <a href="ocene.php">Ocene</a>
        <a href="urnik.php">Urnik</a>
        <a href="predmeti.php">Spletna učilnica</a>
        <a href="list_ucencov.php">Učenci</a>
        <a href="#" class="active">Profesorji</a>
        <a href="meni.php">Meni</a>
    </nav>
    
    <div class="content-container">
        <div class="table-box">
            <h3>Upravljanje z učitelji</h3>
            
            <?php if (!empty($sporocilo)): ?>
                <p style="color: <?php echo (strpos($sporocilo, 'Napaka') !== false) ? 'red' : 'green'; ?>; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($sporocilo); ?>
                </p>
            <?php endif; ?>
            
            <button class="btn-add" onclick="window.location.href='dodaj_ucitelja.php'">+ Dodaj učitelja</button>
            
            <table>
                <thead>
                    <tr>
                        <th>Ime</th>
                        <th>Priimek</th>
                        <th>E-mail</th>
                        <th>Uporabniško ime</th>
                        <th>Status</th>
                        <th>Akcije</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ucitelji)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Ni učiteljev.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ucitelji as $ucitelj): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ucitelj['ime']); ?></td>
                                <td><?php echo htmlspecialchars($ucitelj['priimek']); ?></td>
                                <td><?php echo htmlspecialchars($ucitelj['email']); ?></td>
                                <td><?php echo htmlspecialchars($ucitelj['uporabnisko_ime']); ?></td>
                                <td>
                                    <span class="status-<?php echo $ucitelj['status']; ?>">
                                        <?php echo ucfirst($ucitelj['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-edit" onclick="window.location.href='uredi_ucitelja.php?id=<?php echo $ucitelj['id']; ?>'">Uredi</button>
                                    <button class="btn-delete" onclick="if(confirm('Ali ste prepričani, da želite odstraniti tega učitelja?')) window.location.href='?brisi=<?php echo $ucitelj['id']; ?>'">Briši</button>
                                    <button class="btn-edit" onclick="window.location.href='upravljanje_ucitelj_predmeti.php?id_ucitelja=<?php echo $ucitelj['id']; ?>'">Predmeti</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

