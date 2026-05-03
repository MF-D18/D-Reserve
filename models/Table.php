<?php
class Table {
    private $conn;
    private $table_name = "tables";

    public $id;
    public $table_number;
    public $capacity;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY table_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function getAvailable($date, $start_time, $end_time, $capacity) {
        // Find tables that are not reserved in the given time slot
        $query = "SELECT t.* FROM " . $this->table_name . " t
                  WHERE t.capacity >= :capacity
                  AND t.id NOT IN (
                      SELECT table_id FROM reservations 
                      WHERE reservation_date = :date 
                      AND status IN ('pending', 'confirmed')
                      AND ((start_time < :end_time AND end_time > :start_time))
                  )";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":capacity", $capacity);
        $stmt->bindParam(":date", $date);
        $stmt->bindParam(":start_time", $start_time);
        $stmt->bindParam(":end_time", $end_time);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET table_number=:num, capacity=:cap, status=:status";
        $stmt = $this->conn->prepare($query);
        $this->table_number = htmlspecialchars(strip_tags($this->table_number));
        $stmt->bindParam(':num',    $this->table_number);
        $stmt->bindParam(':cap',    $this->capacity);
        $stmt->bindParam(':status', $this->status);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET table_number=:num, capacity=:cap, status=:status WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $this->table_number = htmlspecialchars(strip_tags($this->table_number));
        $stmt->bindParam(':num',    $this->table_number);
        $stmt->bindParam(':cap',    $this->capacity);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id',     $this->id);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
