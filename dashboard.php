<?php
// dashboard.php - Página principal do dashboard
require_once 'config.php';

// Verificar se o usuário está logado
checkLogin();

// Tentar obter dados da API
$dashboardData = null;
$logsData = null;
$apiError = null;

try {
    $dashboardData = $api->get('/api/stats/dashboard');
    $logsData = $api->get('/api/logs?limit=10');
} catch (Exception $e) {
    $apiError = $e->getMessage();
}

$pageTitle = 'Dashboard';
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= SYSTEM_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Visão geral do sistema de controle de acesso</p>
        </div>

        <?php if ($apiError): ?>
            <div class="alert alert-danger">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                Erro de conexão com a API: <?= htmlspecialchars($apiError) ?>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number success" id="accessToday">
                    <?= $dashboardData['statistics']['acessos_hoje'] ?? '--' ?>
                </div>
                <div class="stat-label">Acessos Hoje</div>
            </div>
            <div class="stat-card">
                <div class="stat-number danger" id="failsToday">
                    <?= $dashboardData['statistics']['falhas_hoje'] ?? '--' ?>
                </div>
                <div class="stat-label">Tentativas Negadas</div>
            </div>
            <div class="stat-card">
                <div class="stat-number info" id="totalUsers">
                    <?= $dashboardData['statistics']['total_users'] ?? '--' ?>
                </div>
                <div class="stat-label">Total de Utilizadores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number warning" id="accessWeek">
                    <?= $dashboardData['statistics']['acessos_semana'] ?? '--' ?>
                </div>
                <div class="stat-label">Acessos esta Semana</div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px; margin-bottom: 40px;">
            <div class="chart-container">
                <h3 class="chart-title">Acessos por Hora (Hoje)</h3>
                <canvas id="hourlyChart" width="400" height="200"></canvas>
            </div>
            <div class="chart-container">
                <h3 class="chart-title">Métodos de Acesso</h3>
                <canvas id="methodsChart" width="300" height="300"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Atividade Recente</h3>
            </div>
            <div class="card-body">
                <div id="recentActivity">
                    <?php if ($logsData && !empty($logsData['logs'])): ?>
                        <?php foreach ($logsData['logs'] as $log): ?>
                            <div class="activity-item"
                                style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
                                <div>
                                    <div class="activity-user" style="font-weight: 600; color: #333;">
                                        <?= htmlspecialchars($log['nome'] ?: 'Desconhecido') ?>
                                    </div>
                                    <div class="activity-time" style="color: #666; font-size: 14px;">
                                        <?= formatDateTime($log['data_hora']) ?>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-info"><?= htmlspecialchars($log['metodo']) ?></span>
                                    <span class="badge <?= $log['result'] === 'Sucesso' ? 'badge-success' : 'badge-danger' ?>"
                                        style="margin-left: 10px;">
                                        <?= htmlspecialchars($log['result']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: #666; padding: 40px;">
                            Nenhuma atividade recente
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script>
        class DashboardPage {
            constructor() {
                this.init();
            }

            async init() {
                await this.createHourlyChart();
                await this.createMethodsChart();
                this.startAutoRefresh();
            }

            async createHourlyChart() {
                try {
                    const response = await app.get('/api/logs/today');
                    const logs = response.logs || [];

                    // Agrupar por hora
                    const hourlyData = new Array(24).fill(0);
                    logs.forEach(log => {
                        if (log.result === 'Sucesso') {
                            const hour = new Date(log.data_hora).getHours();
                            hourlyData[hour]++;
                        }
                    });

                    const config = {
                        type: 'line',
                        data: {
                            labels: Array.from({ length: 24 }, (_, i) => `${i}:00`),
                            datasets: [{
                                label: 'Acessos',
                                data: hourlyData,
                                borderColor: '#667eea',
                                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    };

                    app.createChart('hourlyChart', config);
                } catch (error) {
                    console.error('Erro ao criar gráfico de horas:', error);
                }
            }

            async createMethodsChart() {
                try {
                    const stats = await app.get('/api/stats/dashboard');
                    const methods = stats.methods_distribution || {};

                    const config = {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(methods),
                            datasets: [{
                                data: Object.values(methods),
                                backgroundColor: [
                                    '#667eea',
                                    '#764ba2',
                                    '#10b981',
                                    '#f59e0b',
                                    '#ef4444'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    };

                    app.createChart('methodsChart', config);
                } catch (error) {
                    console.error('Erro ao criar gráfico de métodos:', error);
                }
            }

            async updateStats() {
                try {
                    const stats = await app.get('/api/stats/dashboard');

                    document.getElementById('accessToday').textContent = stats.statistics.acessos_hoje || 0;
                    document.getElementById('failsToday').textContent = stats.statistics.falhas_hoje || 0;
                    document.getElementById('totalUsers').textContent = stats.statistics.total_users || 0;
                    document.getElementById('accessWeek').textContent = stats.statistics.acessos_semana || 0;
                } catch (error) {
                    console.error('Erro ao atualizar estatísticas:', error);
                }
            }

            async updateRecentActivity() {
                try {
                    const response = await app.get('/api/logs?limit=10');
                    const container = document.getElementById('recentActivity');

                    if (response.logs && response.logs.length > 0) {
                        container.innerHTML = response.logs.map(log => `
                            <div class="activity-item" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #f0f0f0;">
                                <div>
                                    <div class="activity-user" style="font-weight: 600; color: #333;">
                                        ${log.nome || 'Desconhecido'}
                                    </div>
                                    <div class="activity-time" style="color: #666; font-size: 14px;">
                                        ${app.formatDateTime(log.data_hora)}
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-info">${log.metodo}</span>
                                    <span class="badge ${log.result === 'Sucesso' ? 'badge-success' : 'badge-danger'}" style="margin-left: 10px;">
                                        ${log.result}
                                    </span>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = '<div style="text-align: center; color: #666; padding: 40px;">Nenhuma atividade recente</div>';
                    }
                } catch (error) {
                    console.error('Erro ao atualizar atividade recente:', error);
                }
            }

            startAutoRefresh() {
                // Atualizar dados a cada 30 segundos
                setInterval(async () => {
                    await this.updateStats();
                    await this.updateRecentActivity();
                }, 30000);

                // Atualizar gráficos a cada 5 minutos
                setInterval(async () => {
                    await this.createHourlyChart();
                    await this.createMethodsChart();
                }, 300000);
            }
        }

        // Inicializar dashboard quando a página carregar
        document.addEventListener('DOMContentLoaded', () => {
            new DashboardPage();
        });
    </script>
</body>

</html>