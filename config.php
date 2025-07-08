<?php
// config.php - Configurações globais do sistema

// Configurações da API
define('API_BASE_URL', 'https://07cf-87-103-122-166.ngrok-free.app');
define('API_KEY', 'cruz');

// Configurações do sistema
define('SYSTEM_NAME', 'AccessPy');
define('SYSTEM_VERSION', '7.0.0');
define('TIMEZONE', 'Europe/Lisbon');

// Configurações de sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set(TIMEZONE);

// Classe principal para fazer requests à API
class ApiClient {
    private $baseUrl;
    private $apiKey;
    private $timeout;
    
    public function __construct() {
        $this->baseUrl = API_BASE_URL;
        $this->apiKey = API_KEY;
        $this->timeout = 15;
    }
    
    /**
     * Fazer requisição HTTP para a API
     */
    public function request($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;
        
        // Headers para a requisição
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: AccessPy-Web/7.0'
        ];
        
        // Configurar cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        // Adicionar API key baseado no método
        if ($method === 'GET') {
            $separator = strpos($url, '?') !== false ? '&' : '?';
            curl_setopt($ch, CURLOPT_URL, $url . $separator . 'api_key=' . urlencode($this->apiKey));
        } else {
            // Para POST/PUT/DELETE, incluir api_key no body
            if ($data === null) {
                $data = [];
            }
            if (is_array($data)) {
                $data['api_key'] = $this->apiKey;
            }
        }
        
        // Configurar método HTTP
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        
        // Executar requisição
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        // Verificar erros de conexão
        if ($error) {
            throw new Exception('Erro de conexão: ' . $error);
        }
        
        // Decodificar resposta
        $responseData = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $errorMsg = isset($responseData['error']) ? $responseData['error'] : 'Erro na API';
            throw new Exception($errorMsg, $httpCode);
        }
        
        return $responseData;
    }
    
    /**
     * Métodos HTTP simplificados
     */
    public function get($endpoint) {
        return $this->request($endpoint, 'GET');
    }
    
    public function post($endpoint, $data = null) {
        return $this->request($endpoint, 'POST', $data);
    }
    
    public function put($endpoint, $data = null) {
        return $this->request($endpoint, 'PUT', $data);
    }
    
    public function delete($endpoint) {
        return $this->request($endpoint, 'DELETE');
    }
    
    /**
     * Testar conexão com a API
     */
    public function testConnection() {
        try {
            $result = $this->get('/');
            return ['success' => true, 'data' => $result];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Obter temperatura do sistema
     */
    public function getSystemTemperature() {
        try {
            $result = $this->get('/api/temperature');
            
            // Se a API retornar dados válidos
            if (isset($result['temperature'])) {
                return [
                    'celsius' => round($result['temperature'], 1),
                    'fahrenheit' => round(($result['temperature'] * 9/5) + 32, 1),
                    'status' => 'Real',
                    'alert' => $result['temperature'] > 70,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
            
            // Se não conseguir obter dados reais, simular
            return $this->simulateTemperature();
            
        } catch (Exception $e) {
            return $this->simulateTemperature();
        }
    }
    
    /**
     * Simular temperatura quando API não está disponível
     */
    private function simulateTemperature() {
        $temp = round((mt_rand(35, 75) + mt_rand(0, 99) / 100), 1);
        return [
            'celsius' => $temp,
            'fahrenheit' => round(($temp * 9/5) + 32, 1),
            'status' => 'Simulada',
            'alert' => $temp > 70,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Obter estatísticas do sistema
     */
    public function getSystemStats() {
        try {
            $result = $this->get('/api/stats');
            
            if (isset($result['stats'])) {
                return $result['stats'];
            }
            
            // Simular estatísticas se não disponível
            return $this->simulateStats();
            
        } catch (Exception $e) {
            return $this->simulateStats();
        }
    }
    
    /**
     * Simular estatísticas do sistema
     */
    private function simulateStats() {
        return [
            'cpu_usage' => mt_rand(10, 80),
            'memory_usage' => mt_rand(30, 90),
            'disk_usage' => mt_rand(20, 70),
            'uptime' => mt_rand(1, 168) . ' horas',
            'network_status' => 'Conectado',
            'last_reboot' => date('Y-m-d H:i:s', strtotime('-' . mt_rand(1, 168) . ' hours'))
        ];
    }
    
    /**
     * Abrir porta (admin)
     */
    public function openDoorAdmin() {
        return $this->post('/abrir-admin');
    }
    
    /**
     * Abrir porta com token
     */
    public function openDoor($token) {
        return $this->post('/api/abrir', ['token' => $token]);
    }
    
    /**
     * Obter utilizadores
     */
    public function getUsers($limit = 100, $offset = 0) {
        $endpoint = '/api/users?limit=' . $limit . '&offset=' . $offset;
        return $this->get($endpoint);
    }
    
    /**
     * Criar utilizador
     */
    public function createUser($userData) {
        return $this->post('/api/user', $userData);
    }
    
    /**
     * Atualizar utilizador
     */
    public function updateUser($userId, $userData) {
        return $this->put('/api/user/' . $userId, $userData);
    }
    
    /**
     * Remover utilizador
     */
    public function deleteUser($userId) {
        return $this->delete('/api/user/' . $userId);
    }
    
    /**
     * Obter logs
     */
    public function getLogs($filters = []) {
        $endpoint = '/api/logs';
        if (!empty($filters)) {
            $endpoint .= '?' . http_build_query($filters);
        }
        return $this->get($endpoint);
    }
    
    /**
     * Obter status do sistema
     */
    public function getSystemStatus() {
        try {
            return $this->get('/api/status');
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Testar hardware
     */
    public function testHardware($component = 'all') {
        return $this->post('/api/test-hardware', ['component' => $component]);
    }
    
    /**
     * Login do utilizador
     */
    public function login($username, $password) {
        $endpoint = '/login?username=' . urlencode($username) . 
                   '&password=' . urlencode($password) . 
                   '&api_key=' . urlencode($this->apiKey);
        
        return $this->get($endpoint);
    }
    
    /**
     * Obter dados completos do dashboard
     */
    public function getDashboardData() {
        $today = date('Y-m-d');
        $week_start = date('Y-m-d', strtotime('-7 days'));
        
        return [
            'system_status' => $this->getSystemStatus(),
            'temperature' => $this->getSystemTemperature(),
            'stats' => $this->getSystemStats(),
            'users' => $this->getUsers(),
            'today_logs' => $this->getLogs(['date_from' => $today]),
            'week_logs' => $this->getLogs(['date_from' => $week_start]),
            'all_logs' => $this->getLogs(['limit' => 200])
        ];
    }
}

// Instância global da API
$api = new ApiClient();

// Funções auxiliares
function formatDateTime($datetime) {
    return date('d/m/Y H:i:s', strtotime($datetime));
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'agora mesmo';
    if ($time < 3600) return floor($time/60) . ' min atrás';
    if ($time < 86400) return floor($time/3600) . ' h atrás';
    if ($time < 2592000) return floor($time/86400) . ' dias atrás';
    
    return formatDate($datetime);
}

function getUserInitial($name) {
    $words = explode(' ', trim($name));
    if (count($words) >= 2) {
        return strtoupper($words[0][0] . $words[1][0]);
    }
    return strtoupper(substr($name, 0, 1));
}

function getStatusBadge($status) {
    switch (strtolower($status)) {
        case 'sucesso':
            return '<span class="badge badge-light-success">Sucesso</span>';
        case 'negado':
            return '<span class="badge badge-light-danger">Negado</span>';
        case 'erro':
            return '<span class="badge badge-light-warning">Erro</span>';
        default:
            return '<span class="badge badge-light-secondary">' . htmlspecialchars($status) . '</span>';
    }
}

function getMethodBadge($method) {
    $badges = [
        'Cartão' => 'badge-light-info',
        'API' => 'badge-light-primary',
        'TestApp' => 'badge-light-success',
        'API_Legacy' => 'badge-light-warning'
    ];
    
    $class = $badges[$method] ?? 'badge-light-secondary';
    return '<span class="badge ' . $class . '">' . htmlspecialchars($method) . '</span>';
}

// Verificar se o usuário está logado
function checkLogin() {
    if (!isset($_SESSION['user_logged']) || !$_SESSION['user_logged']) {
        header('Location: login.php');
        exit;
    }
}

// Verificar conexão com a API
function checkApiConnection() {
    global $api;
    $result = $api->testConnection();
    return $result['success'];
}

// Função para log de debug (opcional)
function debugLog($message, $data = null) {
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        $logMessage = '[' . date('Y-m-d H:i:s') . '] ' . $message;
        if ($data !== null) {
            $logMessage .= ' | Data: ' . json_encode($data);
        }
        error_log($logMessage);
    }
}

// Função para sanitizar entrada
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Função para validar token
function validateToken($token) {
    return !empty($token) && strlen($token) >= 3 && strlen($token) <= 50;
}

// Configurações de debug (ativar apenas em desenvolvimento)
// define('DEBUG_MODE', true);
?>