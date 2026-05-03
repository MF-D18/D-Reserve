<?php
class Payment {
    private $conn;
    private $table_name = "payments";

    public $id;
    public $reservation_id;
    public $amount;
    public $payment_method;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function processDeposit($reservation_id, $amount, $method) {
        // Simulate processing bank transfer or e-wallet
        $simulatedStatus = 'success'; // In real world, this depends on Payment Gateway API

        $query = "INSERT INTO " . $this->table_name . " 
                  SET reservation_id=:res_id, amount=:amount, payment_method=:method, status=:status";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":res_id", $reservation_id);
        $stmt->bindParam(":amount", $amount);
        $stmt->bindParam(":method", $method);
        $stmt->bindParam(":status", $simulatedStatus);

        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function checkDepositStatus($reservation_id) {
        $query = "SELECT status FROM " . $this->table_name . " 
                  WHERE reservation_id = :res_id 
                  ORDER BY id DESC LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":res_id", $reservation_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['status'];
        }
        return 'pending';
    }
}
?>
