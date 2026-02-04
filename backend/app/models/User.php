<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    

    // Registrar nuevo usuario
    public function register($data) {
        $stmt = $this->db->prepare('INSERT INTO users (full_name, email, username, password) VALUES (:name, :email, :user, :pass)');
        return $stmt->execute([
            ':name'  => $data['full_name'],
            ':email' => $data['email'],
            ':user'  => $data['username'],
            ':pass'  => password_hash($data['password'], PASSWORD_BCRYPT)
        ]);
    }

    // Buscar por Usuario
    public function findByUsername($username) {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :user');
        $stmt->execute([':user' => $username]);
        return $stmt->fetch();
    }
    

    // --- NUEVO: Buscar por Email (Para Google) ---
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>