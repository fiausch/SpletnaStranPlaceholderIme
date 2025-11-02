<?php
// Avtentikacija in zaščita strani

session_start();
require_once 'config.php';

// Funkcija za preverjanje ali je uporabnik prijavljen
function je_prijavljen() {
    // Preveri session
    if (isset($_SESSION['prijavljen']) && $_SESSION['prijavljen'] === true) {
        return true;
    }
    
    // Preveri piškotke
    if (isset($_COOKIE['prijavljen']) && isset($_COOKIE['uporabnik_id'])) {
        // Preveri ali piškotek vsebuje veljavne podatke
        $uporabnik_id = $_COOKIE['uporabnik_id'];
        
        global $pdo;
        $stmt = $pdo->prepare("SELECT id, vloga, status FROM uporabniki WHERE id = ? AND status = 'aktiven'");
        $stmt->execute([$uporabnik_id]);
        $uporabnik = $stmt->fetch();
        
        if ($uporabnik) {
            // Obnovi session iz piškotka
            $_SESSION['prijavljen'] = true;
            $_SESSION['uporabnik_id'] = $uporabnik['id'];
            $_SESSION['vloga'] = $uporabnik['vloga'];
            return true;
        }
    }
    
    return false;
}

// Funkcija za zahtevanje prijave
function zahtevaj_prijavo() {
    if (!je_prijavljen()) {
        header('Location: index_prijava.php');
        exit;
    }
}

// Funkcija za pridobitev informacij o trenutnem uporabniku
function get_trenutni_uporabnik() {
    if (!je_prijavljen()) {
        return null;
    }
    
    global $pdo;
    $uporabnik_id = $_SESSION['uporabnik_id'] ?? $_COOKIE['uporabnik_id'] ?? null;
    
    if (!$uporabnik_id) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM uporabniki WHERE id = ? AND status = 'aktiven'");
    $stmt->execute([$uporabnik_id]);
    return $stmt->fetch();
}

// Funkcija za preverjanje ali je uporabnik profesor predmeta
function je_profesor_predmeta($id_predmeta, $id_ucitelja) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ucitelji_predmeti WHERE id_predmeta = ? AND id_ucitelja = ?");
    $stmt->execute([$id_predmeta, $id_ucitelja]);
    return $stmt->fetchColumn() > 0;
}

// Funkcija za preverjanje ali je uporabnik administrator
function je_administrator() {
    if (!je_prijavljen()) {
        return false;
    }
    
    $vloga = $_SESSION['vloga'] ?? null;
    return $vloga === 'administrator';
}

// Funkcija za preverjanje ali je uporabnik učenec
function je_ucenec() {
    if (!je_prijavljen()) {
        return false;
    }
    
    $vloga = $_SESSION['vloga'] ?? null;
    return $vloga === 'ucenec';
}

// Funkcija za preverjanje ali je uporabnik učitelj
function je_ucitelj() {
    if (!je_prijavljen()) {
        return false;
    }
    
    $vloga = $_SESSION['vloga'] ?? null;
    return $vloga === 'ucitelj';
}

// Funkcija za izpis
function izpisi() {
    session_destroy();
    setcookie('prijavljen', '', time() - 3600, '/');
    setcookie('uporabnik_id', '', time() - 3600, '/');
    header('Location: index_prijava.php');
    exit;
}
?>
