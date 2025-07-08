<?php
// settings.php - Página de configurações do sistema
require_once 'config.php';

// Verificar se o usuário está logado
checkLogin();

// Tentar obter dados da API
$systemStatus = null;
$apiError = null;

try {
    $systemStatus = $api->get('/api/status');
} catch (Exception $e) {
    $apiError = $e->getMessage();
}

$pageTitle = 'Configurações';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= SYSTEM_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Configurações do Sistema</h1>
            <p class="page-subtitle">Gerir configurações e teste de hardware</p>
        </div>

        <?php if ($apiError): ?>
            <div class="alert alert-danger">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Erro de conexão com a API: <?= htmlspecialchars($apiError) ?>
            </div>
        <?php endif; ?>

        <!-- Status do Sistema -->
        <div class="card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3 class="card-title">Status do Sistema</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div>
                        <h4 style="color: #333; margin-bottom: 15px;">Informações Gerais</h4>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Sistema:</span>
                                <strong><?= $systemStatus['system'] ?? 'N/A' ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Versão:</span>
                                <strong><?= $systemStatus['version'] ?? 'N/A' ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Status:</span>
                                <span class="badge badge-success"><?= $systemStatus['status'] ?? 'N/A' ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Uptime:</span>
                                <strong><?= $systemStatus['uptime'] ?? 'N/A' ?></strong>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 style="color: #333; margin-bottom: 15px;">Hardware</h4>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <?php if ($systemStatus && isset($systemStatus['hardware'])): ?>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Relé:</span>
                                    <code>GPIO <?= $systemStatus['hardware']['relay_pin'] ?></code>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>LED:</span>
                                    <code>GPIO <?= $systemStatus['hardware']['led_pin'] ?></code>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Buzzer:</span>
                                    <code>GPIO <?= $systemStatus['hardware']['beep_pin'] ?></code>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>RFID:</span>
                                    <code>D0:<?= $systemStatus['hardware']['rfid_pins']['D0'] ?> D1:<?= $systemStatus['hardware']['rfid_pins']['D1'] ?></code>
                                </div>
                            <?php else: ?>
                                <span style="color: #666;">Informações não disponíveis</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div>
                        <h4 style="color: #333; margin-bottom: 15px;">Base de Dados</h4>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Ficheiro:</span>
                                <code><?= $systemStatus['database']['file'] ?? 'N/A' ?></code>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Conexão:</span>
                                <span class="badge <?= ($systemStatus['database']['connected'] ?? false) ? 'badge-success' : 'badge-danger' ?>">
                                    <?= ($systemStatus['database']['connected'] ?? false) ? 'Conectado' : 'Desconectado' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teste de Hardware -->
        <div class="card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3 class="card-title">Teste de Hardware</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <button class="btn btn-secondary" onclick="testHardware('led')">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                        </svg>
                        Testar LED
                    </button>
                    
                    <button class="btn btn-secondary" onclick="testHardware('beep')">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"></path>
                        </svg>
                        Testar Buzzer
                    </button>
                    
                    <button class="btn btn-secondary" onclick="testHardware('relay')">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Testar Relé
                    </button>
                    
                    <button class="btn btn-secondary" onclick="testHardware('rfid')">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path>
                        </svg>
                        Testar RFID
                    </button>
                </div>
                
                <button class="btn btn-primary" onclick="testHardware('all')" style="width: 100%;">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Testar Todos os Componentes
                </button>
                
                <div id="testResults" style="margin-top: 20px; display: none;">
                    <h4 style="color: #333; margin-bottom: 15px;">Resultados dos Testes</h4>
                    <div id="testResultsContent"></div>
                </div>
            </div>
        </div>

        <!-- Controle de Porta -->
        <div class="card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3 class="card-title">Controle de Porta</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <p style="color: #666; margin-bottom: 15px;">
                        Abrir a porta manualmente como administrador
                    </p>
                    
                    <button class="btn btn-success" onclick="openDoor()" style="max-width: 300px;">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"></path>
                        </svg>
                        Abrir Porta (Admin)
                    </button>
                </div>
            </div>
        </div>

        <!-- Informações do Sistema -->
        <div class="card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3 class="card-title">Temperatura do Sistema</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <div style="text-align: center;">
                        <div id="tempDisplay" style="font-size: 48px; font-weight: bold; color: #667eea;">--°C</div>
                        <div id="tempStatus" style="color: #666; margin-top: 5px;">Carregando...</div>
                    </div>
                    
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Normal</span>
                            <span>0-60°C</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Elevada</span>
                            <span>60-70°C</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span>Alta</span>
                            <span>70-80°C</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Crítica</span>
                            <span>>80°C</span>
                        </div>
                    </div>
                    
                    <button class="btn btn-secondary" onclick="refreshTemperature()">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                        </svg>
                        Atualizar
                    </button>
                </div>
            </div>
        </div>

        <!-- Configurações da API -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Configurações da API</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div>
                        <h4 style="color: #333; margin-bottom: 15px;">Configuração Atual</h4>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>URL Base:</span>
                                <code><?= API_BASE_URL ?></code>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>API Key:</span>
                                <code><?= substr(API_KEY, 0, 4) ?>****</code>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Timeout:</span>
                                <code>10s</code>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 style="color: #333; margin-bottom: 15px;">Teste de Conectividade</h4>
                        <button class="btn btn-secondary" onclick="testApiConnection()" style="width: 100%;">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Testar Conexão
                        </button>
                        <div id="apiTestResult" style="margin-top: 15px; display: none;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Classe utilitária para settings
        class AppUtils {
            static async request(url, options = {}) {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        ...options.headers
                    },
                    ...options
                });
                return response.json();
            }

            static async get(url) {
                return this.request(url, { method: 'GET' });
            }

            static async post(url, data) {
                return this.request(url, {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
            }

            static showAlert(message, type = 'info') {
                alert(message);
            }
        }

        class SettingsPage {
            constructor() {
                this.init();
            }

            init() {
                this.loadTemperature();
                // Atualizar temperatura a cada 30 segundos
                setInterval(() => {
                    this.loadTemperature();
                }, 30000);
            }

            async loadTemperature() {
                try {
                    const response = await AppUtils.get('/api/temperature');
                    if (response.success && response.temperature) {
                        this.displayTemperature(response.temperature);
                    }
                } catch (error) {
                    console.log('Usando temperatura simulada');
                    // Fallback para temperatura simulada
                    const simTemp = {
                        celsius: (Math.random() * 30 + 40).toFixed(1),
                        status: 'Simulada',
                        alert: false
                    };
                    this.displayTemperature(simTemp);
                }
            }

            displayTemperature(temp) {
                const tempDisplay = document.getElementById('tempDisplay');
                const tempStatus = document.getElementById('tempStatus');
                
                if (tempDisplay) {
                    tempDisplay.textContent = `${temp.celsius}°C`;
                    
                    // Colorir baseado na temperatura
                    if (temp.alert) {
                        tempDisplay.style.color = '#ef4444';
                    } else if (temp.celsius > 60) {
                        tempDisplay.style.color = '#f59e0b';
                    } else {
                        tempDisplay.style.color = '#10b981';
                    }
                }
                
                if (tempStatus) {
                    tempStatus.textContent = `Status: ${temp.status}`;
                }
            }

            async testHardware(component) {
                try {
                    const button = event.target;
                    const originalText = button.innerHTML;
                    
                    button.disabled = true;
                    button.innerHTML = `
                        <div class="loading" style="width: 16px; height: 16px; border-width: 2px; margin-right: 8px;"></div>
                        Testando...
                    `;
                    
                    const response = await AppUtils.post('/api/test-hardware', { component });
                    
                    if (response.success) {
                        this.displayTestResults(response.results, component);
                        AppUtils.showAlert(`Teste de ${component} concluído!`, 'success');
                    } else {
                        AppUtils.showAlert(`Erro no teste: ${response.error}`, 'danger');
                    }
                } catch (error) {
                    console.error('Erro ao testar hardware:', error);
                    AppUtils.showAlert('Erro de conexão ao testar hardware', 'danger');
                } finally {
                    const button = event.target;
                    button.disabled = false;
                    button.innerHTML = event.target.getAttribute('data-original-text') || 'Testar';
                }
            }

            displayTestResults(results, component) {
                const resultsDiv = document.getElementById('testResults');
                const contentDiv = document.getElementById('testResultsContent');
                
                let html = '';
                
                Object.entries(results).forEach(([key, result]) => {
                    const isSuccess = result.includes('OK');
                    const badgeClass = isSuccess ? 'badge-success' : 'badge-danger';
                    
                    html += `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                            <strong>${key.toUpperCase()}</strong>
                            <span class="badge ${badgeClass}">${result}</span>
                        </div>
                    `;
                });
                
                contentDiv.innerHTML = html;
                resultsDiv.style.display = 'block';
            }

            async openDoor() {
                try {
                    const button = event.target;
                    const originalText = button.innerHTML;
                    
                    button.disabled = true;
                    button.innerHTML = `
                        <div class="loading" style="width: 16px; height: 16px; border-width: 2px; margin-right: 8px;"></div>
                        Abrindo...
                    `;
                    
                    const response = await AppUtils.post('/abrir-admin');
                    
                    if (response.success) {
                        AppUtils.showAlert('Porta aberta com sucesso!', 'success');
                    } else {
                        AppUtils.showAlert(`Erro ao abrir porta: ${response.message}`, 'danger');
                    }
                } catch (error) {
                    console.error('Erro ao abrir porta:', error);
                    AppUtils.showAlert('Erro de conexão ao abrir porta', 'danger');
                } finally {
                    const button = event.target;
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }

            async testApiConnection() {
                try {
                    const button = event.target;
                    const originalText = button.innerHTML;
                    const resultDiv = document.getElementById('apiTestResult');
                    
                    button.disabled = true;
                    button.innerHTML = `
                        <div class="loading" style="width: 16px; height: 16px; border-width: 2px; margin-right: 8px;"></div>
                        Testando...
                    `;
                    
                    const startTime = Date.now();
                    const response = await AppUtils.get('/');
                    const endTime = Date.now();
                    const responseTime = endTime - startTime;
                    
                    resultDiv.innerHTML = `
                        <div class="alert alert-success">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <strong>Conexão bem-sucedida!</strong><br>
                                Tempo de resposta: ${responseTime}ms<br>
                                Status: ${response.status}<br>
                                Versão: ${response.version}
                            </div>
                        </div>
                    `;
                    resultDiv.style.display = 'block';
                    
                } catch (error) {
                    const resultDiv = document.getElementById('apiTestResult');
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <strong>Falha na conexão!</strong><br>
                                Erro: ${error.message}
                            </div>
                        </div>
                    `;
                    resultDiv.style.display = 'block';
                } finally {
                    const button = event.target;
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            }

            async refreshTemperature() {
                await this.loadTemperature();
                AppUtils.showAlert('Temperatura atualizada!', 'success', 2000);
            }
        }

        // Global functions for onclick handlers
        function testHardware(component) {
            settingsPage.testHardware(component);
        }

        function openDoor() {
            settingsPage.openDoor();
        }

        function testApiConnection() {
            settingsPage.testApiConnection();
        }

        function refreshTemperature() {
            settingsPage.refreshTemperature();
        }

        // Initialize page
        let settingsPage;
        document.addEventListener('DOMContentLoaded', () => {
            settingsPage = new SettingsPage();
        });
    </script>
</body>
</html>