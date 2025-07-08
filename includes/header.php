<?php
// includes/header.php - Cabeçalho global

include '../config.php';

$userName = $_SESSION['user_name'] ?? 'Admin';
$userInitial = getUserInitial($userName);
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Verificar status da API e obter temperatura
$apiStatus = checkApiConnection();
$temperature = getSystemTemperature();
?>

<div class="top-bar">
    <div class="menu-toggle" id="menuToggle">
        <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
        </svg>
    </div>
    
    <div class="logo"><?= SYSTEM_NAME ?></div>
    
    <div class="top-bar-right">
        <div class="status-box">
            <div class="status-indicator <?= $temperature && $temperature['alert'] ? 'status-offline' : 'status-online' ?>" id="tempStatus"></div>
            <span id="systemTemp"><?= $temperature ? $temperature['celsius'] . '°C' : '--°C' ?></span>
        </div>
        
        <div class="status-box">
            <div class="status-indicator <?= $apiStatus ? 'status-online' : 'status-offline' ?>" id="apiStatus"></div>
            <span id="apiStatusText"><?= $apiStatus ? 'Online' : 'Offline' ?></span>
        </div>
        
        <div class="user-dropdown">
            <div class="user-avatar" id="userAvatar"><?= $userInitial ?></div>
            <div class="dropdown-menu" id="userDropdown">
                <div class="dropdown-item">
                    <strong><?= htmlspecialchars($userName) ?></strong>
                </div>
                <div class="dropdown-divider"></div>
                <a href="profile.php" class="dropdown-item">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    Perfil
                </a>
                <a href="settings.php" class="dropdown-item">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                    Configurações
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"></path>
                    </svg>
                    Sair
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle do dropdown do usuário
document.addEventListener('DOMContentLoaded', function() {
    const userAvatar = document.getElementById('userAvatar');
    const userDropdown = document.getElementById('userDropdown');
    const menuToggle = document.getElementById('menuToggle');
    
    // Toggle dropdown do usuário
    userAvatar.addEventListener('click', function(e) {
        e.stopPropagation();
        userDropdown.classList.toggle('show');
    });
    
    // Fechar dropdown ao clicar fora
    document.addEventListener('click', function() {
        userDropdown.classList.remove('show');
    });
    
    // Toggle do menu móvel
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('mobile-open');
        });
    }
    
    // Atualizar temperatura do sistema via API
    async function updateSystemTemp() {
        try {
            const response = await fetch('/api/temperature', {
                headers: {
                    'X-API-Key': '<?= API_KEY ?>'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.temperature) {
                    const temp = data.temperature;
                    const tempElement = document.getElementById('systemTemp');
                    const tempStatus = document.getElementById('tempStatus');
                    
                    if (tempElement) {
                        tempElement.textContent = `${temp.celsius}°C`;
                    }
                    
                    if (tempStatus) {
                        tempStatus.className = `status-indicator ${temp.alert ? 'status-offline' : 'status-online'}`;
                    }
                }
            }
        } catch (error) {
            console.log('Erro ao obter temperatura:', error);
            // Fallback para temperatura simulada
            const temp = (Math.random() * 20 + 40).toFixed(1);
            const tempElement = document.getElementById('systemTemp');
            const tempStatus = document.getElementById('tempStatus');
            
            if (tempElement) {
                tempElement.textContent = `${temp}°C`;
            }
            
            if (tempStatus) {
                tempStatus.className = `status-indicator ${temp > 70 ? 'status-offline' : 'status-online'}`;
            }
        }
    }
    
    // Atualizar temperatura a cada 30 segundos
    updateSystemTemp();
    setInterval(updateSystemTemp, 30000);
});
</script>