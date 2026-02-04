
<?php
// Modelo: Acceso y gestión de datos de productos
class Product {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

 public function all($search = null) {
    if ($search) {
        // Corregido: Usamos s1 y s2 para evitar el error de parámetros de antes
        $stmt = $this->db->prepare('SELECT * FROM products WHERE name LIKE :s1 OR category LIKE :s2 ORDER BY created_at DESC');
        $stmt->execute([':s1' => "%$search%", ':s2' => "%$search%"]);
    } else {
        $stmt = $this->db->query('SELECT * FROM products ORDER BY created_at DESC');
    }
    return $stmt->fetchAll();
}

    public function find($id) {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    

// Ubicación: backend/app/models/Product.php

public function create($data) {
    $stmt = $this->db->prepare('INSERT INTO products (name, price, stock, category, image) VALUES (:name, :price, :stock, :category, :image)');
    $stmt->execute([
        ':name'     => $data['name'],
        ':price'    => $data['price'],
        ':stock'    => $data['stock'],
        ':category' => $data['category'],
        ':image'    => $data['image'] // Vinculación del dato
    ]);
    return $this->find($this->db->lastInsertId());
}

    public function update($id, $data) {
    $stmt = $this->db->prepare('UPDATE products SET name = :name, price = :price, stock = :stock, category = :category, image = :image WHERE id = :id');
    $stmt->execute([
        ':name' => $data['name'],
        ':price' => $data['price'],
        ':stock' => $data['stock'],
        ':category' => $data['category'],
        ':image' => $data['image'], // <--- LÍNEA IMPORTANTE
        ':id' => $id
    ]);
    return $this->find($id);
}

    public function delete($id) {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
