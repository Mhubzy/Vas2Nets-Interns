<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../Logs/error.log');

require_once __DIR__. "/Database.php";

class User {
    
    private PDO $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    //_____________AUTHENTICATION METHODS__________________

    // Check if an email already exists — used during registration
    public function emailExists(string $email): array|false {
        $stmt = $this->conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Find a user by their email — used during login
    public function findByEmail(string $email): array|false {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
     
    // Find a user by their ID — used on profile pages
    public function findById(int $id): array|false {
        $stmt= $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    //insert — called by register()
    public function create(string $first, string $last, string $email, string $password): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (first, last, email, password) VALUES (?,?,?,?)");
        return $stmt->execute([$first, $last, $email, $hash]);
    }

    // High-level register — validates then calls create()
    public function register(string $first, string $last, string $email, string $password): array {
        
        //Validation
        if(empty($first) || empty($last) || empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'All fields are required.'];
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email address'];
        }

        if(strlen($password) <6 ) {
            return ['success' => false, 'error' => 'Password must be at least 6 characters'];
        } 

        //Check if email is not taken
        if($this->emailExists($email)) {
            return ['success' => false, 'error' => 'An account with this email already exist.'];
        }

        //All good- Create user
        if($this->create($first, $last, $email, $password)) {
            return['success' => true];
        }

        return ['success' => false, 'error' => 'Registration failed. Please try again later'];
    }

    //Verify Plain Password stored in Hash (AUTHENTICATION METHOD)
    public function verifyPassword(string $plainPassword, string $hashPassword): bool {
        return password_verify($plainPassword, $hashPassword);
    }

    //_______________USER ROLE CHECK METHODS____________________

    //USER ROLE METHOD (Admin, Advisor and Student role)
    public function isAdmin(array $user): bool {
        return($user['role'] ?? '') === 'admin';
    }

    public function isAdvisor(array $user): bool {
        return($user['role'] ?? '') === 'advisor';
    }

    public function isStudent(array $user): bool {
        return($user['role'] ?? '') === 'student';
    }

    
    //___________________PROFILE MANAGEMENT METHODS____________________

    //Update Profile Image 
    public function updateProfileImage($id, $filename) {
        $stmt = $this->conn->prepare("UPDATE users SET profile_image= ? WHERE id = ?");
        return $stmt->execute([$filename, $id]);
    }

    //Update Name
    public function updateName($id, $first, $last) {
        $stmt = $this->conn->prepare("UPDATE users SET first = ?, last = ? WHERE id = ?");
        return $stmt->execute([$first, $last, $id]);
    }

    //Update Password
    public function updatePassword($id, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    //Verify Update Password
    public function updateVerifyPassword($id, $password) {
    $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    return password_verify($password, $user['password']);
    }

}

?>
