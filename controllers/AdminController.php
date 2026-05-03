<?php
require_once '../models/User.php';
require_once '../models/Table.php';
require_once '../models/Menu.php';
require_once '../models/Reservation.php';

class AdminController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->requireAdmin();
    }

    // Middleware: only admins can access
    private function requireAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }
    }

    // ─── Dashboard ────────────────────────────────────────────────────────
    public function dashboard() {
        $reservation = new Reservation($this->db);
        $userModel   = new User($this->db);
        $menuModel   = new Menu($this->db);
        $tableModel  = new Table($this->db);

        $stats        = $reservation->getStats();
        $totalUsers   = $userModel->readAll()->rowCount();
        $totalMenus   = $menuModel->readAllAdmin()->rowCount();
        $totalTables  = $tableModel->readAll()->rowCount();
        $recentRes    = $reservation->readAll();

        require_once '../models/PreOrder.php';
        $preOrderModel = new PreOrder($this->db);
        $topMenus = $preOrderModel->getTopSellingMenus(5);

        require_once '../views/admin/dashboard.php';
    }

    // ─── Users ────────────────────────────────────────────────────────────
    public function users() {
        $userModel = new User($this->db);
        $users     = $userModel->readAll();
        $msg       = $_GET['msg'] ?? null;
        require_once '../views/admin/users.php';
    }

    public function userCreate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel          = new User($this->db);
            $userModel->name    = $_POST['name'];
            $userModel->email   = $_POST['email'];
            $userModel->phone   = $_POST['phone'] ?? null;
            $userModel->password = $_POST['password'];
            $userModel->role    = $_POST['role'];

            if ($userModel->emailExists($userModel->email)) {
                $error = "Email already exists.";
            } elseif ($userModel->create()) {
                header("Location: index.php?action=admin_users&msg=created");
                exit();
            } else {
                $error = "Failed to create user.";
            }
        }
        require_once '../views/admin/user_form.php';
    }

    public function userEdit() {
        $userModel = new User($this->db);
        $id        = (int)$_GET['id'];
        $userData  = $userModel->readOne($id);

        if (!$userData) {
            header("Location: index.php?action=admin_users&msg=not_found");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel->id    = $id;
            $userModel->name  = $_POST['name'];
            $userModel->email = $_POST['email'];
            $userModel->phone = $_POST['phone'] ?? null;
            $userModel->role  = $_POST['role'];

            if ($userModel->emailExists($userModel->email, $id)) {
                $error = "Email already used by another account.";
            } elseif ($userModel->update()) {
                header("Location: index.php?action=admin_users&msg=updated");
                exit();
            } else {
                $error = "Failed to update user.";
            }
        }
        require_once '../views/admin/user_form.php';
    }

    public function userDelete() {
        $userModel = new User($this->db);
        $id = (int)$_GET['id'];

        // Prevent deleting yourself
        if ($id === (int)$_SESSION['user_id']) {
            header("Location: index.php?action=admin_users&msg=self_delete");
            exit();
        }

        $userModel->delete($id);
        header("Location: index.php?action=admin_users&msg=deleted");
        exit();
    }

    // ─── Tables ───────────────────────────────────────────────────────────
    public function tables() {
        $tableModel = new Table($this->db);
        $tables     = $tableModel->readAll();
        $msg        = $_GET['msg'] ?? null;
        require_once '../views/admin/tables.php';
    }

    public function tableCreate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tableModel               = new Table($this->db);
            $tableModel->table_number = $_POST['table_number'];
            $tableModel->capacity     = (int)$_POST['capacity'];
            $tableModel->status       = $_POST['status'];
            if ($tableModel->create()) {
                header("Location: index.php?action=admin_tables&msg=created");
                exit();
            }
            $error = "Failed to create table.";
        }
        require_once '../views/admin/table_form.php';
    }

    public function tableEdit() {
        $tableModel = new Table($this->db);
        $id         = (int)$_GET['id'];
        $tableData  = $tableModel->readOne($id);

        if (!$tableData) {
            header("Location: index.php?action=admin_tables&msg=not_found");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tableModel->id           = $id;
            $tableModel->table_number = $_POST['table_number'];
            $tableModel->capacity     = (int)$_POST['capacity'];
            $tableModel->status       = $_POST['status'];
            if ($tableModel->update()) {
                header("Location: index.php?action=admin_tables&msg=updated");
                exit();
            }
            $error = "Failed to update table.";
        }
        require_once '../views/admin/table_form.php';
    }

    public function tableDelete() {
        $tableModel = new Table($this->db);
        $tableModel->delete((int)$_GET['id']);
        header("Location: index.php?action=admin_tables&msg=deleted");
        exit();
    }

    // ─── Menus ────────────────────────────────────────────────────────────
    public function menus() {
        $menuModel = new Menu($this->db);
        $menus     = $menuModel->readAllAdmin();
        $msg       = $_GET['msg'] ?? null;
        require_once '../views/admin/menus.php';
    }

    public function menuCreate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $menuModel               = new Menu($this->db);
            $menuModel->name         = $_POST['name'];
            $menuModel->description  = $_POST['description'];
            $menuModel->price        = (float)$_POST['price'];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'img/uploads/';
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid('menu_', true) . '.' . $extension;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../public/' . $uploadDir . $newFileName)) {
                    $menuModel->image_url = $uploadDir . $newFileName;
                }
            } else {
                $menuModel->image_url = null;
            }

            $menuModel->category     = $_POST['category'];
            $menuModel->is_available = isset($_POST['is_available']) ? 1 : 0;
            if ($menuModel->create()) {
                header("Location: index.php?action=admin_menus&msg=created");
                exit();
            }
            $error = "Failed to create menu item.";
        }
        require_once '../views/admin/menu_form.php';
    }

    public function menuEdit() {
        $menuModel = new Menu($this->db);
        $id        = (int)$_GET['id'];
        $menuData  = $menuModel->readOne($id);

        if (!$menuData) {
            header("Location: index.php?action=admin_menus&msg=not_found");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $menuModel->id           = $id;
            $menuModel->name         = $_POST['name'];
            $menuModel->description  = $_POST['description'];
            $menuModel->price        = (float)$_POST['price'];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'img/uploads/';
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $newFileName = uniqid('menu_', true) . '.' . $extension;
                if (move_uploaded_file($_FILES['image']['tmp_name'], '../public/' . $uploadDir . $newFileName)) {
                    // Delete old image if it was an uploaded one
                    if (!empty($menuData['image_url']) && strpos($menuData['image_url'], 'img/uploads/') === 0) {
                        $oldFile = '../public/' . $menuData['image_url'];
                        if (file_exists($oldFile)) unlink($oldFile);
                    }
                    $menuModel->image_url = $uploadDir . $newFileName;
                }
            } else {
                $menuModel->image_url = $_POST['current_image'] ?? $menuData['image_url'];
            }

            $menuModel->category     = $_POST['category'];
            $menuModel->is_available = isset($_POST['is_available']) ? 1 : 0;
            if ($menuModel->update()) {
                header("Location: index.php?action=admin_menus&msg=updated");
                exit();
            }
            $error = "Failed to update menu item.";
        }
        require_once '../views/admin/menu_form.php';
    }

    public function menuDelete() {
        $menuModel = new Menu($this->db);
        $id = (int)$_GET['id'];
        $menuData = $menuModel->readOne($id);
        
        if ($menuData && !empty($menuData['image_url']) && strpos($menuData['image_url'], 'img/uploads/') === 0) {
            $file = '../public/' . $menuData['image_url'];
            if (file_exists($file)) unlink($file);
        }

        $menuModel->delete($id);
        header("Location: index.php?action=admin_menus&msg=deleted");
        exit();
    }

    // ─── Reservations ─────────────────────────────────────────────────────
    public function reservations() {
        $reservation  = new Reservation($this->db);
        $reservations = $reservation->readAll();
        $msg          = $_GET['msg'] ?? null;
        require_once '../views/admin/reservations.php';
    }

    public function reservationUpdateStatus() {
        $reservation = new Reservation($this->db);
        $id     = (int)$_POST['id'];
        $status = $_POST['status'];
        $reservation->updateStatus($id, $status);
        header("Location: index.php?action=admin_reservations&msg=updated");
        exit();
    }

    public function apiGetPreOrder() {
        header('Content-Type: application/json');
        
        if (!isset($_GET['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing reservation ID.']);
            return;
        }

        require_once '../models/PreOrder.php';
        $preOrderModel = new PreOrder($this->db);
        $stmt = $preOrderModel->getByReservation((int)$_GET['id']);
        
        $items = [];
        $total = 0;
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subtotal = $row['price_at_order'] * $row['quantity'];
            $total += $subtotal;
            $items[] = [
                'name' => $row['name'],
                'quantity' => $row['quantity'],
                'price' => $row['price_at_order'],
                'subtotal' => $subtotal
            ];
        }

        echo json_encode([
            'status' => 'success',
            'items' => $items,
            'total' => $total
        ]);
        exit();
    }

    public function processRefund() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $query = "UPDATE payments SET status = 'refunded' WHERE reservation_id = :res_id AND status = 'success'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':res_id', $id);
            $stmt->execute();
        }
        header("Location: index.php?action=admin_reservations&msg=refunded");
        exit();
    }

    public function settleReservation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $reservation = new Reservation($this->db);
            $reservation->updateStatus($id, 'completed');
            
            // Note: We could insert another payment record if total_tagihan > total_paid,
            // but simply updating the reservation status fulfills the requirement for now.
        }
        header("Location: index.php?action=admin_reservations&msg=settled");
        exit();
    }

    public function apiGetReservationDetails() {
        header('Content-Type: application/json');
        
        if (!isset($_GET['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing reservation ID.']);
            return;
        }

        $res_id = (int)$_GET['id'];

        // Get paid amount
        $query = "SELECT SUM(amount) as total_paid FROM payments WHERE reservation_id = :id AND status = 'success'";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $res_id);
        $stmt->execute();
        $paymentData = $stmt->fetch(PDO::FETCH_ASSOC);
        $total_paid = $paymentData['total_paid'] ? (float)$paymentData['total_paid'] : 0;

        require_once '../models/PreOrder.php';
        $preOrderModel = new PreOrder($this->db);
        $stmt2 = $preOrderModel->getByReservation($res_id);
        
        $items = [];
        $preOrderTotal = 0;
        
        while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            $subtotal = $row['price_at_order'] * $row['quantity'];
            $preOrderTotal += $subtotal;
            $items[] = [
                'name' => $row['name'],
                'quantity' => $row['quantity'],
                'price' => $row['price_at_order'],
                'subtotal' => $subtotal
            ];
        }

        $deposit = 100000;

        echo json_encode([
            'status' => 'success',
            'items' => $items,
            'deposit' => $deposit,
            'total_paid' => $total_paid
        ]);
        exit();
    }
}
?>
