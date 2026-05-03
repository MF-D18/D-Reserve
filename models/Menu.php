<?php
class Menu {
    private $conn;
    private $table_name = "menus";

    public $id;
    public $name;
    public $description;
    public $price;
    public $image_url;
    public $is_available;
    public $category;

    public function __construct($db) {
        $this->conn = $db;
    }

    // For public menu page (available only)
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_available = 1 ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // For admin (all menus)
    public function readAllAdmin() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
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
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, description=:desc, price=:price, image_url=:img, is_available=:avail, category=:category";
        $stmt = $this->conn->prepare($query);
        $this->name        = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category    = htmlspecialchars(strip_tags($this->category));
        $stmt->bindParam(':name',  $this->name);
        $stmt->bindParam(':desc',  $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':img',   $this->image_url);
        $stmt->bindParam(':avail', $this->is_available);
        $stmt->bindParam(':category', $this->category);
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET name=:name, description=:desc, price=:price, image_url=:img, is_available=:avail, category=:category WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $this->name        = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->category    = htmlspecialchars(strip_tags($this->category));
        $stmt->bindParam(':name',  $this->name);
        $stmt->bindParam(':desc',  $this->description);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':img',   $this->image_url);
        $stmt->bindParam(':avail', $this->is_available);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':id',    $this->id);
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
