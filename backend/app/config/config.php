<?php
// backend/app/config/config.php

// 1. SILENCIAR ERRORES VISUALES (CRÍTICO)
error_reporting(0); 
ini_set('display_errors', 0);

// 2. DATOS DE BASE DE DATOS
define('DB_HOST', 'localhost');
define('DB_NAME', 'daw_products_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// 3. PERMISOS DE CONEXIÓN (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>