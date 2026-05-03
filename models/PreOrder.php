<?php
class PreOrder {
    private $conn;
    private $table_name = "pre_orders";

    public $id;
    public $reservation_id;
    public $menu_id;
    public $quantity;
    public $price_at_order;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addItems($reservation_id, $items) {
        // $items is an array of ['menu_id' => x, 'quantity' => y, 'price' => z]
        $query = "INSERT INTO " . $this->table_name . " (reservation_id, menu_id, quantity, price_at_order) VALUES ";
        $values = [];
        foreach($items as $item) {
            $values[] = "({$reservation_id}, {$item['menu_id']}, {$item['quantity']}, {$item['price']})";
        }
        
        if (empty($values)) return true;

        $query .= implode(", ", $values);
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

    public function getByReservation($reservation_id) {
        $query = "SELECT p.*, m.name, m.image_url FROM " . $this->table_name . " p 
                  JOIN menus m ON p.menu_id = m.id 
                  WHERE p.reservation_id = :res_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":res_id", $reservation_id);
        $stmt->execute();
        return $stmt;
    }

    public function getTopSellingMenus($limit = 5) {
        $query = "SELECT m.name, SUM(p.quantity) as total_quantity 
                  FROM " . $this->table_name . " p 
                  JOIN menus m ON p.menu_id = m.id 
                  GROUP BY p.menu_id 
                  ORDER BY total_quantity DESC 
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
