<?php
require_once 'Payment.php';

class Reservation {
    private $conn;
    private $table_name = "reservations";

    public $id;
    public $user_id;
    public $table_id;
    public $reservation_date;
    public $start_time;
    public $end_time;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, table_id=:table_id, reservation_date=:date, 
                      start_time=:start_time, end_time=:end_time, status='pending'";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":table_id", $this->table_id);
        $stmt->bindParam(":date", $this->reservation_date);
        $stmt->bindParam(":start_time", $this->start_time);
        $stmt->bindParam(":end_time", $this->end_time);

        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // PBO Concept: Integrity Automation
    public function confirm() {
        // Dependency Injection / Object Interaction
        $payment = new Payment($this->conn);
        $paymentStatus = $payment->checkDepositStatus($this->id);

        if ($paymentStatus === 'success') {
            $query = "UPDATE " . $this->table_name . " SET status = 'confirmed' WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id);
            return $stmt->execute();
        }
        return false;
    }

    public function getByUser($user_id) {
        $query = "SELECT r.*, t.table_number, p.status as payment_status 
                  FROM " . $this->table_name . " r 
                  JOIN tables t ON r.table_id = t.id 
                  LEFT JOIN payments p ON p.reservation_id = r.id
                  WHERE r.user_id = :user_id 
                  ORDER BY r.reservation_date DESC, r.start_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function getByIdAndUser($id, $user_id) {
        $query = "SELECT r.*, t.table_number, p.status as payment_status 
                  FROM " . $this->table_name . " r 
                  JOIN tables t ON r.table_id = t.id 
                  LEFT JOIN payments p ON p.reservation_id = r.id
                  WHERE r.id = :id AND r.user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Admin: Get all reservations with user & table info
    public function readAll() {
        $query = "SELECT r.*, u.name as user_name, u.email, t.table_number, p.status as payment_status
                  FROM " . $this->table_name . " r
                  JOIN users u ON r.user_id = u.id
                  JOIN tables t ON r.table_id = t.id
                  LEFT JOIN payments p ON p.reservation_id = r.id
                  ORDER BY r.reservation_date DESC, r.start_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Admin: Update reservation status manually
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Admin: Get summary statistics
    public function getStats() {
        $stmt = $this->conn->query("
            SELECT 
                COUNT(*) as total,
                SUM(status = 'confirmed') as confirmed,
                SUM(status = 'pending') as pending,
                SUM(status = 'cancelled') as cancelled,
                SUM(status = 'completed') as completed
            FROM reservations
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
