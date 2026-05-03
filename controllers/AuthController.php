<?php
require_once '../models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function login() {
        // Redirect if already logged in
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?action=home");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email    = trim($_POST['email']);
            $password = $_POST['password'];

            if ($this->user->login($email, $password)) {
                $_SESSION['user_id']   = $this->user->id;
                $_SESSION['user_name'] = $this->user->name;
                $_SESSION['user_role'] = $this->user->role;

                // Redirect admin to admin dashboard, customer to home
                if ($this->user->role === 'admin') {
                    header("Location: index.php?action=admin_dashboard");
                } else {
                    header("Location: index.php?action=home");
                }
                exit();
            } else {
                $error = "Email atau password salah. Silakan coba lagi.";
                require_once '../views/login.php';
            }
        } else {
            require_once '../views/login.php';
        }
    }

    public function register() {
        // Redirect if already logged in
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?action=home");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name            = trim($_POST['name']);
            $email           = trim($_POST['email']);
            $phone           = trim($_POST['phone']);
            $password        = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            // Validation
            $errors = [];

            if (empty($name) || strlen($name) < 3) {
                $errors[] = "Nama minimal 3 karakter.";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format email tidak valid.";
            } elseif ($this->user->emailExists($email)) {
                $errors[] = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
            }

            if (strlen($password) < 6) {
                $errors[] = "Password minimal 6 karakter.";
            }

            if ($password !== $password_confirm) {
                $errors[] = "Konfirmasi password tidak cocok.";
            }

            if (empty($errors)) {
                $this->user->name     = $name;
                $this->user->email    = $email;
                $this->user->phone    = $phone;
                $this->user->password = $password;
                $this->user->role     = 'customer'; // New registrations are always customer

                if ($this->user->create()) {
                    // Auto-login after register
                    $this->user->login($email, $password);
                    $_SESSION['user_id']        = $this->user->id;
                    $_SESSION['user_name']      = $this->user->name;
                    $_SESSION['user_role']      = 'customer';
                    $_SESSION['register_success'] = true;
                    header("Location: index.php?action=home");
                    exit();
                } else {
                    $errors[] = "Registrasi gagal. Silakan coba lagi.";
                }
            }
        }

        require_once '../views/register.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?action=home");
        exit();
    }
}
?>

