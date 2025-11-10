<?php
require_once 'auth.php';
zahtevaj_prijavo();

$uporabnik = get_trenutni_uporabnik();
$vloga = $uporabnik['vloga'];

if ($vloga !== 'administrator') {
    header('Location: index.php');
    exit;
}

global $pdo;
$sporocilo = '';

// Obdelava brisanja
if (isset($_GET['brisi']) && is_numeric($_GET['brisi'])) {
    $id_ucenca = (int)$_GET['brisi'];
    try {
        $stmt = $pdo->prepare("SELECT id, vloga FROM uporabniki WHERE id = ? AND vloga = 'ucenec'");
        $stmt->execute([$id_ucenca]);
        $ucenec = $stmt->fetch();
        
        if ($ucenec) {
            $stmt = $pdo->prepare("UPDATE uporabniki SET status = 'neaktiven' WHERE id = ?");
            $stmt->execute([$id_ucenca]);
            $sporocilo = 'Učenec uspešno odstranjen.';
        } else {
            $sporocilo = 'Učenec ni najden.';
        }
    } catch (PDOException $e) {
        $sporocilo = 'Napaka pri brisanju: ' . $e->getMessage();
    }
}

// Pridobivanje vseh učencev
try {
    $stmt = $pdo->prepare("SELECT id, ime, priimek, email, uporabnisko_ime, status FROM uporabniki WHERE vloga = 'ucenec' ORDER BY priimek, ime");
    $stmt->execute();
    $ucenci = $stmt->fetchAll();
} catch (PDOException $e) {
    $ucenci = [];
    $sporocilo = 'Napaka pri pridobivanju učencev: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upravljanje z učenci</title>
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
        <a href="#" class="active">Učenci</a>
        <a href="upravljanje_ucitelji.php">Profesorji</a>
        <a href="meni.php">Meni</a>
    </nav>
    
    <div class="content-container">
        <div class="table-box">
            <h3>Upravljanje z učenci</h3>
            
            <?php if (!empty($sporocilo)): ?>
                <p style="color: <?php echo (strpos($sporocilo, 'Napaka') !== false) ? 'red' : 'green'; ?>; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($sporocilo); ?>
                </p>
            <?php endif; ?>
            
            <button class="btn-add" onclick="window.location.href='dodaj_ucenca.php'">+ Dodaj učenca</button>
            
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
                    <?php if (empty($ucenci)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Ni učencev.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ucenci as $ucenec): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ucenec['ime']); ?></td>
                                <td><?php echo htmlspecialchars($ucenec['priimek']); ?></td>
                                <td><?php echo htmlspecialchars($ucenec['email']); ?></td>
                                <td><?php echo htmlspecialchars($ucenec['uporabnisko_ime']); ?></td>
                                <td>
                                    <span class="status-<?php echo $ucenec['status']; ?>">
                                        <?php echo ucfirst($ucenec['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-edit" onclick="window.location.href='uredi_ucenca.php?id=<?php echo $ucenec['id']; ?>'">Uredi</button>
                                    <button class="btn-delete" onclick="if(confirm('Ali ste prepričani, da želite odstraniti tega učenca?')) window.location.href='?brisi=<?php echo $ucenec['id']; ?>'">Briši</button>
                                    <button class="btn-edit" onclick="window.location.href='upravljanje_ucenec_predmeti.php?id_ucenca=<?php echo $ucenec['id']; ?>'">Predmeti</button>
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

