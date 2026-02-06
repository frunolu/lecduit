<?php
require_once __DIR__ . '/Database.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    /**
     * Zjistí, zda je uživatel přihlášen
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Vrátí data přihlášeného uživatele
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) return null;

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        return $stmt->fetch();
    }

    /**
     * Přihlášení/Registrace přes Google
     */
    public function loginWithGoogle($googleData) {
        // 1. Zkusíme najít uživatele podle Google ID
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE google_id = :gid");
        $stmt->execute([':gid' => $googleData['id']]);
        $user = $stmt->fetch();

        if (!$user) {
            // 2. Pokud nemá Google ID, zkusíme podle e-mailu (mohla to být dřívější registrace)
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $googleData['email']]);
            $user = $stmt->fetch();

            if ($user) {
                // Spárujeme existující účet s Googlem
                $stmt = $this->pdo->prepare("UPDATE users SET google_id = :gid, avatar = :av WHERE id = :id");
                $stmt->execute([
                    ':gid' => $googleData['id'],
                    ':av'  => $googleData['picture'],
                    ':id'  => $user['id']
                ]);
            } else {
                // 3. Uživatel neexistuje -> Vytvoříme nového
                $sql = "INSERT INTO users (email, first_name, last_name, google_id, avatar) 
                        VALUES (:email, :fname, :lname, :gid, :av)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':email' => $googleData['email'],
                    ':fname' => $googleData['given_name'],
                    ':lname' => $googleData['family_name'],
                    ':gid'   => $googleData['id'],
                    ':av'    => $googleData['picture']
                ]);
                // Získáme ID nového uživatele
                $user = ['id' => $this->pdo->lastInsertId()];
            }
        }

        // 4. Uložíme do session
        $_SESSION['user_id'] = $user['id'];
        return true;
    }

    public function logout() {
        unset($_SESSION['user_id']);
    }
}
