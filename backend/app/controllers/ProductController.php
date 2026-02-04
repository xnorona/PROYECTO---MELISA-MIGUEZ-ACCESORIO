
<?php
// Controlador: Lógica de negocio y validaciones del backend
class ProductController {
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

 // Ubicación: backend/app/controllers/ProductController.php

private function validate($data, $isUpdate = false) {
    $errors = [];
    $name = trim($data['name'] ?? '');
    $price = $data['price'] ?? null;
    $stock = $data['stock'] ?? null;
    $category = trim($data['category'] ?? '');
    $image = $data['image'] ?? null; // Capturamos la imagen

    if ($name === '') { $errors['name'] = 'El nombre es obligatorio.'; }
    if ($category === '') { $errors['category'] = 'La categoría es obligatoria.'; }

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        exit;
    }

    // RETORNO CRÍTICO: Si no incluyes 'image' aquí, el modelo recibe NULL
    return [
        'name' => $name,
        'price' => (float)$price,
        'stock' => (int)$stock,
        'category' => $category,
        'image' => $image 
    ];
}
    // En ProductController.php
public function index() {
    // Limpiamos el valor de búsqueda para evitar espacios vacíos
    $search = isset($_GET['q']) ? trim($_GET['q']) : null;
    $products = $this->model->all($search);
    echo json_encode($products);
}

    public function show($id) {
        $product = $this->model->find($id);
        if (!$product) { http_response_code(404); echo json_encode(['error' => 'No encontrado']); return; }
        echo json_encode($product);
    }

    public function store() {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $valid = $this->validate($input);
        $created = $this->model->create($valid);
        http_response_code(201);
        echo json_encode($created);
    }

    public function update($id) {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $valid = $this->validate($input, true);
        if (!$this->model->find($id)) { http_response_code(404); echo json_encode(['error' => 'No encontrado']); return; }
        $updated = $this->model->update($id, $valid);
        echo json_encode($updated);
    }

    public function destroy($id) {
        if (!$this->model->find($id)) { http_response_code(404); echo json_encode(['error' => 'No encontrado']); return; }
        $this->model->delete($id);
        http_response_code(204);
    }
}
