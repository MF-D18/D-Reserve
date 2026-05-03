<?php
require_once '../models/User.php';

class ProfileController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();

        // Only logged-in customers can access
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }
    }

    public function show() {
        $userModel = new User($this->db);
        $userData  = $userModel->readOne($_SESSION['user_id']);
        $success   = $_GET['success'] ?? null;
        $errors    = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name        = trim($_POST['name'] ?? '');
            $email       = trim($_POST['email'] ?? '');
            $phone       = trim($_POST['phone'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPass = $_POST['confirm_password'] ?? '';

            // Validation
            if (strlen($name) < 3) {
                $errors[] = "Nama minimal 3 karakter.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format email tidak valid.";
            } elseif ($userModel->emailExists($email, $_SESSION['user_id'])) {
                $errors[] = "Email sudah digunakan oleh akun lain.";
            }
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    $errors[] = "Password baru minimal 6 karakter.";
                } elseif ($newPassword !== $confirmPass) {
                    $errors[] = "Konfirmasi password baru tidak cocok.";
                }
            }

            if (empty($errors)) {
                $passToUpdate = !empty($newPassword) ? $newPassword : null;
                if ($userModel->updateProfile($_SESSION['user_id'], $name, $email, $phone, $passToUpdate)) {
                    // Update session name if changed
                    $_SESSION['user_name'] = $name;
                    header("Location: index.php?action=profile&success=1");
                    exit();
                } else {
                    $errors[] = "Gagal memperbarui profil. Silakan coba lagi.";
                }
            }

            // Repopulate form on error
            $userData['name']  = $name;
            $userData['email'] = $email;
            $userData['phone'] = $phone;
        }

        require_once '../views/profile.php';
    }
}
?>
