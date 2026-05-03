<?php
require_once '../models/Reservation.php';
require_once '../models/Table.php';
require_once '../models/Menu.php';
require_once '../models/PreOrder.php';

class ReservationController {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function apiCheckAvailability() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        // We decode the JSON input since we will send JSON from fetch()
        $data = json_decode(file_get_contents('php://input'), true);
        
        $date = $data['date'] ?? '';
        $start_time = $data['start_time'] ?? '';
        $duration = isset($data['duration']) ? (int)$data['duration'] : 0;
        $capacity = isset($data['capacity']) ? (int)$data['capacity'] : 0;
        
        if (empty($date) || empty($start_time) || $duration <= 0 || $capacity <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Lengkapi semua field.']);
            return;
        }

        $end_time = date('H:i', strtotime($start_time) + ($duration * 3600));
        
        $tableModel = new Table($this->db);
        $stmt = $tableModel->getAvailable($date, $start_time, $end_time, $capacity);
        
        $availableTables = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $availableTables[] = $row;
        }

        // Store current booking criteria in session for the confirmation step
        $_SESSION['booking_data'] = [
            'date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time
        ];

        echo json_encode([
            'status' => 'success', 
            'tables' => $availableTables,
            'session_data' => $_SESSION['booking_data'] // For debugging/verification
        ]);
        exit();
    }

    public function book() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $tableModel = new Table($this->db);
        $menuModel = new Menu($this->db);
        
        $availableTables = null;
        $menus = $menuModel->readAll();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_booking'])) {

            $reservation = new Reservation($this->db);
            $reservation->user_id = $_SESSION['user_id'];
            $reservation->table_id = $_POST['table_id'];
            $reservation->reservation_date = $_SESSION['booking_data']['date'];
            $reservation->start_time = $_SESSION['booking_data']['start_time'];
            $reservation->end_time = $_SESSION['booking_data']['end_time'];

            if ($reservation->create()) {
                // Handle Pre-Orders
                if (isset($_POST['menus']) && !empty($_POST['menus'])) {
                    $preOrder = new PreOrder($this->db);
                    $items = [];
                    foreach ($_POST['menus'] as $menu_id => $qty) {
                        if ($qty > 0) {
                            $items[] = [
                                'menu_id' => $menu_id,
                                'quantity' => $qty,
                                'price' => $_POST['prices'][$menu_id]
                            ];
                        }
                    }
                    $preOrder->addItems($reservation->id, $items);
                }
                
                header("Location: index.php?action=payment&res_id=" . $reservation->id);
                exit();
            }
        }

        require_once '../views/reservation.php';
    }

    public function payment() {
        if (!isset($_SESSION['user_id']) || !isset($_GET['res_id'])) {
            header("Location: index.php?action=home");
            exit();
        }

        $res_id = $_GET['res_id'];
        
        // Calculate dynamic total
        require_once '../models/PreOrder.php';
        $preOrderModel = new PreOrder($this->db);
        $stmt = $preOrderModel->getByReservation($res_id);
        
        $preOrderItems = [];
        $preOrderTotal = 0;
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $subtotal = $row['price_at_order'] * $row['quantity'];
            $preOrderTotal += $subtotal;
            $preOrderItems[] = [
                'name' => $row['name'],
                'quantity' => $row['quantity'],
                'price' => $row['price_at_order'],
                'subtotal' => $subtotal
            ];
        }
        
        $deposit = 100000;
        $totalAmount = $deposit + $preOrderTotal;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $method = $_POST['payment_method']; // bank_transfer or e_wallet
            // Verify total amount passed from form or use server-calculated
            $amountToPay = $totalAmount; 

            $reservation = new Reservation($this->db);
            $reservation->id = $res_id;
            
            $payment = new Payment($this->db);
            if ($payment->processDeposit($res_id, $amountToPay, $method)) {
                // INTEGRITY AUTOMATION: Confirm reservation ONLY if payment is success
                if ($reservation->confirm()) {
                    header("Location: index.php?action=my_reservations&success=1");
                    exit();
                }
            }
            $error = "Payment failed. Reservation not confirmed.";
        }

        require_once '../views/payment.php';
    }

    public function myReservations() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }

        $reservation = new Reservation($this->db);
        $reservations = $reservation->getByUser($_SESSION['user_id']);
        
        require_once '../views/my_reservations.php';
    }

    public function apiGetMyReservation() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized or missing ID.']);
            return;
        }

        $res_id = (int)$_GET['id'];
        $user_id = $_SESSION['user_id'];

        $reservationModel = new Reservation($this->db);
        $reservation = $reservationModel->getByIdAndUser($res_id, $user_id);

        if (!$reservation) {
            echo json_encode(['status' => 'error', 'message' => 'Reservation not found or access denied.']);
            return;
        }

        require_once '../models/PreOrder.php';
        $preOrderModel = new PreOrder($this->db);
        $stmt = $preOrderModel->getByReservation($res_id);
        
        $items = [];
        $preOrderTotal = 0;
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
        $totalAmount = $deposit + $preOrderTotal;

        echo json_encode([
            'status' => 'success',
            'reservation' => $reservation,
            'items' => $items,
            'deposit' => $deposit,
            'total' => $totalAmount
        ]);
        exit();
    }
    public function cancelReservation() {
        if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=login");
            exit();
        }

        $res_id = (int)$_POST['id'];
        $user_id = $_SESSION['user_id'];

        $reservationModel = new Reservation($this->db);
        $reservation = $reservationModel->getByIdAndUser($res_id, $user_id);

        if ($reservation && in_array($reservation['status'], ['pending', 'confirmed'])) {
            $reservationModel->updateStatus($res_id, 'cancelled');
        }

        header("Location: index.php?action=my_reservations&msg=cancelled");
        exit();
    }
}
?>
