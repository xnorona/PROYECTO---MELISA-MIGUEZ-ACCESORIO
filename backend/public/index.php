<?php
// backend/public/index.php

// Carga de dependencias
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';

// Conexión segura
try {
    $db = Database::getConnection();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error BD']);
    exit;
}

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// --- RUTAS ---

// 1. LOGIN NORMAL
if (strpos($uri, 'api/login') !== false) {
    $auth = new AuthController(new User($db));
    if ($method === 'POST') $auth->login();
    exit;
    
// RUTA GOOGLE
if (strpos($uri, 'google-login') !== false) {
    $auth = new AuthController(new User($db));
    if ($method === 'POST') {
        $auth->googleLogin();
    }
    exit;
}
}

// 2. REGISTRO
if (strpos($uri, 'api/signup') !== false) {
    $auth = new AuthController(new User($db));
    if ($method === 'POST') $auth->signup();
    exit;
}

// 3. LOGIN CON GOOGLE
if (strpos($uri, 'api/google-login') !== false) {
    $auth = new AuthController(new User($db));
    if ($method === 'POST') $auth->googleLogin();
    exit;
}

// 4. PRODUCTOS (INVENTARIO)
if (strpos($uri, 'api/products') !== false) {
    $controller = new ProductController(new Product($db));
    
    // Detectar ID al final de la URL
    $parts = explode('/', parse_url($uri, PHP_URL_PATH));
    $lastPart = end($parts);
    $id = is_numeric($lastPart) ? (int)$lastPart : null;

    switch ($method) {
        case 'GET': $id ? $controller->show($id) : $controller->index(); break;
        case 'POST': $controller->store(); break;
        case 'PUT': $id ? $controller->update($id) : err('Falta ID'); break;
        case 'DELETE': $id ? $controller->destroy($id) : err('Falta ID'); break;
    }
    exit;
}

// Ruta no encontrada
http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Ruta no encontrada']);

function err($msg) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $msg]);
}
?>