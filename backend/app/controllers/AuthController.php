<?php
class AuthController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    // --- 1. LOGIN NORMAL ---
    public function login() {
        // Decodificamos como ARRAY (true)
        $input = json_decode(file_get_contents('php://input'), true);
        
        $user = $this->userModel->findByUsername($input['user'] ?? '');
        $pass = $input['pass'] ?? '';

        if ($user && password_verify($pass, $user['password'])) {
            echo json_encode([
                'success' => true, 
                'role' => ($user['username'] === 'admin') ? 'admin' : 'user'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
        }
    }

    // --- 2. REGISTRO ---
    public function signup() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Verifica que vengan los datos mínimos
        if (!isset($input['username']) || !isset($input['password'])) {
            echo json_encode(['success' => false, 'message' => 'Faltan datos']);
            return;
        }

        if ($this->userModel->register($input)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar (quizás el usuario ya existe)']);
        }
    }

    // --- 3. LOGIN CON GOOGLE ---
    public function googleLogin() {
        // Usamos ARRAY para ser consistentes
        $input = json_decode(file_get_contents("php://input"), true);
        $token = $input['token'] ?? null;

        if (!$token) {
            echo json_encode(['success' => false, 'message' => 'Token no recibido']);
            return;
        }
        
        // Verificar token con Google
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $token;
        $response = @file_get_contents($url); 
        
        if (!$response) {
            echo json_encode(['success' => false, 'message' => 'Token inválido o expirado']);
            return;
        }

        // Decodificar respuesta de Google como ARRAY
        $payload = json_decode($response, true);
        
        // Verificar si el email existe en la respuesta
        if (!isset($payload['email'])) {
            echo json_encode(['success' => false, 'message' => 'Google no retornó email']);
            return;
        }

        $email = $payload['email'];
        $name = $payload['name'] ?? 'Usuario Google';

        // Buscar si el usuario ya existe
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            // Si no existe, REGISTRARLO AUTOMÁTICAMENTE
            // Generamos una contraseña aleatoria porque entra por Google
            $randomPass = bin2hex(random_bytes(8));
            
            // Usamos la parte antes del @ como usuario (ej: juan@gmail -> juan)
            $username = explode('@', $email)[0]; 

            // Llamamos al registro del modelo
            $registerData = [
                'full_name' => $name,
                'email' => $email,
                'username' => $username, 
                'password' => $randomPass
            ];

            $this->userModel->register($registerData);
            
            // Volvemos a buscarlo para obtener su ID y datos recién creados
            $user = $this->userModel->findByEmail($email);
        }

        // Responder al Frontend
        if ($user) {
            echo json_encode([
                'success' => true,
                'role' => ($user['username'] === 'admin') ? 'admin' : 'user',
                'user' => $user['username']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al procesar usuario']);
        }
    }
} // <--- ESTA LLAVE FALTABA EN TU CÓDIGO ANTERIOR
?>