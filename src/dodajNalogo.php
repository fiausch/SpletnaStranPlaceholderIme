<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naslov = $_POST['naslov'];
    $opis = $_POST['opis'];
    $datum_oddaje = $_POST['datum_oddaje'];
    $profesor_id = 1; // Za zdaj statično, kasneje uporabite prijavo za pridobitev ID-ja profesorja

    try {
        $stmt = $pdo->prepare("INSERT INTO naloge (naslov, opis, datum_oddaje, profesor_id) VALUES (:naslov, :opis, :datum_oddaje, :profesor_id)");
        $stmt->execute([
            ':naslov' => $naslov,
            ':opis' => $opis,
            ':datum_oddaje' => $datum_oddaje,
            ':profesor_id' => $profesor_id
        ]);
        $sporocilo = "Naloga uspešno dodana!";
    } catch (PDOException $e) {
        $sporocilo = "Napaka pri dodajanju naloge: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj nalogo</title>
</head>
<body>
    <h1>Dodaj nalogo</h1>
    <?php if (!empty($sporocilo)) echo "<p>$sporocilo</p>"; ?>
    <form action="dodajNalogo.php" method="POST">
        <label for="naslov">Naslov naloge:</label>
        <input type="text" id="naslov" name="naslov" required><br>

        <label for="opis">Opis naloge:</label>
        <textarea id="opis" name="opis" required></textarea><br>

        <label for="datum_oddaje">Datum oddaje:</label>
        <input type="date" id="datum_oddaje" name="datum_oddaje" required><br>

        <button type="submit">Dodaj nalogo</button>
    </form>
    <a href="index.php">Nazaj na glavno stran</a>
</body>
</html>