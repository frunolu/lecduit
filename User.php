<?php
require_once __DIR__ . '/Database.php';

class User
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    /**
     * Zjistí, zda je uživatel přihlášen
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Vrátí data přihlášeného uživatele
     */
    public function getCurrentUser()
    {
        if (!$this->isLoggedIn())
            return null;

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        return $stmt->fetch();
    }

    /**
     * Přihlášení/Registrace přes Google
     */
    public function loginWithGoogle($googleData)
    {
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
                    ':av' => $googleData['picture'],
                    ':id' => $user['id']
                ]);
            }
            else {
                // 3. Uživatel neexistuje -> Vytvoříme nového
                $sql = "INSERT INTO users (email, first_name, last_name, google_id, avatar) 
                        VALUES (:email, :fname, :lname, :gid, :av)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':email' => $googleData['email'],
                    ':fname' => $googleData['given_name'],
                    ':lname' => $googleData['family_name'],
                    ':gid' => $googleData['id'],
                    ':av' => $googleData['picture']
                ]);
                // Získáme ID nového uživatele
                $user = ['id' => $this->pdo->lastInsertId()];
            }
        }

        // 4. Uložíme do session
        $_SESSION['user_id'] = $user['id'];
        return true;
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
    }

    /**
     * Register new user with email/password
     */
    public function register($email, $password, $firstName, $lastName)
    {
        // Check if email already exists
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'email_exists'];
        }

        // Hash password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));

        // Insert new user
        $sql = "INSERT INTO users (email, password_hash, first_name, last_name, verification_token, email_verified) 
                VALUES (:email, :hash, :fname, :lname, :token, 0)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':hash' => $passwordHash,
            ':fname' => $firstName,
            ':lname' => $lastName,
            ':token' => $verificationToken
        ]);

        return ['success' => true, 'token' => $verificationToken, 'user_id' => $this->pdo->lastInsertId()];
    }

    /**
     * Login with email/password
     */
    public function login($email, $password)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !$user['password_hash']) {
            return ['success' => false, 'error' => 'invalid_credentials'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'invalid_credentials'];
        }

        // Regenerate session ID for security
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];

        return ['success' => true, 'user' => $user];
    }

    /**
     * Verify email with token
     */
    public function verifyEmail($token)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE verification_token = :token");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'error' => 'invalid_token'];
        }

        // Mark email as verified
        $stmt = $this->pdo->prepare("UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = :id");
        $stmt->execute([':id' => $user['id']]);

        return ['success' => true];
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset($email)
    {
        $stmt = $this->pdo->prepare("SELECT id, first_name FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            // Return success even if user doesn't exist (security best practice)
            return ['success' => true];
        }

        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->pdo->prepare("UPDATE users SET reset_token = :token, reset_token_expires = :expires WHERE id = :id");
        $stmt->execute([
            ':token' => $resetToken,
            ':expires' => $expires,
            ':id' => $user['id']
        ]);

        return ['success' => true, 'token' => $resetToken, 'name' => $user['first_name'], 'email' => $email];
    }

    /**
     * Reset password with token
     */
    public function resetPassword($token, $newPassword)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE reset_token = :token AND reset_token_expires > NOW()");
        $stmt->execute([':token' => $token]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'error' => 'invalid_token'];
        }

        // Hash new password
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update password and clear reset token
        $stmt = $this->pdo->prepare("UPDATE users SET password_hash = :hash, reset_token = NULL, reset_token_expires = NULL WHERE id = :id");
        $stmt->execute([
            ':hash' => $passwordHash,
            ':id' => $user['id']
        ]);

        return ['success' => true];
    }

    /**
     * Update password for logged-in user
     */
    public function updatePassword($userId, $newPassword)
    {
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $this->pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
        $stmt->execute([
            ':hash' => $passwordHash,
            ':id' => $userId
        ]);

        return ['success' => true];
    }
}