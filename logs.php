<?php
// logs.php - Página de visualização de logs
require_once 'config.php';

// Verificar se o usuário está logado
checkLogin();

// Tentar obter dados da API
$logsData = null;
$apiError = null;

try {
    $logsData = $api->get('/api/logs?limit=50');
} catch (Exception $e) {
    $apiError = $e->getMessage();
}

$pageTitle = 'Logs';
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
            <h1 class="page-title">Logs de Acesso</h1>
            <p class="page-subtitle">Visualizar e analisar registos de entrada</p>
        </div>

        <?php if ($apiError): ?>
            <div class="alert alert-danger">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Erro de conexão com a API: <?= htmlspecialchars($apiError) ?>
            </div>
        <?php endif; ?>

        <!-- Estatísticas de Hoje -->
        <div class="stats-grid" style="margin-bottom: 30px;">
            <div class="stat-card">
                <div class="stat-number success" id="todaySuccess">--</div>
                <div class="stat-label">Sucessos Hoje</div>
            </div>
            <div class="stat-card">
                <div class="stat-number danger" id="todayFails">--</div>
                <div class="stat-label">Falhas Hoje</div>
            </div>
            <div class="stat-card">
                <div class="stat-number info" id="todayTotal">--</div>
                <div class="stat-label">Total Hoje</div>
            </div>
            <div class="stat-card">
                <div class="stat-number warning" id="successRate">--</div>
                <div class="stat-label">Taxa de Sucesso</div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3 class="card-title">Filtros</h3>
            </div>
            <div class="card-body">
                <form id="filtersForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; align-items: end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="dateFrom" class="form-label">Data Início</label>
                        <input type="date" id="dateFrom" name="date_from" class="form-control">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="dateTo" class="form-label">Data Fim</label>
                        <input type="date" id="dateTo" name="date_to" class="form-control">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="userFilter" class="form-label">Utilizador</label>
                        <input type="text" id="userFilter" name="user" class="form-control" placeholder="Nome do utilizador">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="methodFilter" class="form-label">Método</label>
                        <select id="methodFilter" name="method" class="form-control">
                            <option value="">Todos</option>
                            <option value="Cartão">Cartão RFID</option>
                            <option value="APP">Aplicação</option>
                            <option value="API">API</option>
                            <option value="API_Legacy">API Legacy</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="resultFilter" class="form-label">Resultado</label>
                        <select id="resultFilter" name="result" class="form-control">
                            <option value="">Todos</option>
                            <option value="success">Apenas Sucessos</option>
                            <option value="failed">Apenas Falhas</option>
                        </select>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                            Filtrar
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                            </svg>
                            Limpar
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportLogs()">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Exportar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Gráfico de Atividade -->
        <div class="chart-container" style="margin-bottom: 25px;">
            <h3 class="chart-title">Atividade por Hora (Últimas 24h)</h3>
            <canvas id="activityChart" width="400" height="100"></canvas>
        </div>

        <!-- Tabela de Logs -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="card-title">Registos de Acesso</h3>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <span id="logsCount" style="color: #666; font-size: 14px;">--</span>
                    <input type="text" placeholder="Pesquisar logs..." 
                           class="form-control" style="width: 250px;" id="searchInput">
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table" id="logsTable">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Utilizador</th>
                                <th>Método</th>
                                <th>Resultado</th>
                                <th>Token/Tag</th>
                            </tr>
                        </thead>
                        <tbody id="logsTableBody">
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">
                                    <div class="loading">
                                        <div class="spinner"></div>
                                        Carregando logs...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Paginação -->
        <div class="pagination" id="pagination" style="display: none;">
            <!-- Será preenchida pelo JavaScript -->
        </div>
    </div>

    <script>
        // Classe utilitária para logs
        class AppUtils {
            static async get(url) {
                const response = await fetch(url, {
                    headers: { 'Accept': 'application/json' }
                });
                return response.json();
            }

            static formatDateTime(dateString) {
                if (!dateString) return 'N/A';
                try {
                    const date = new Date(dateString);
                    return date.toLocaleString('pt-PT');
                } catch (error) {
                    return dateString;
                }
            }

            static timeAgo(dateString) {
                if (!dateString) return 'N/A';
                try {
                    const date = new Date(dateString);
                    const now = new Date();
                    const diffInSeconds = Math.floor((now - date) / 1000);

                    if (diffInSeconds < 60) return 'agora mesmo';
                    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} min atrás`;
                    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} h atrás`;
                    
                    return date.toLocaleDateString('pt-PT');
                } catch (error) {
                    return dateString;
                }
            }

            static createChart(canvasId, config) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || !window.Chart) return null;
                try {
                    return new Chart(canvas, config);
                } catch (error) {
                    console.error('Erro ao criar gráfico:', error);
                    return null;
                }
            }

            static showAlert(message, type = 'info', duration = 5000) {
                alert(message); // Implementação simples
            }

            static debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        }

        class LogsPage {
            constructor() {
                this.currentPage = 1;
                this.itemsPerPage = 50;
                this.totalLogs = 0;
                this.filters = {};
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.loadTodayStats();
                this.loadLogs();
                this.createActivityChart();
                this.setDefaultDates();
            }

            setupEventListeners() {
                // Formulário de filtros
                document.getElementById('filtersForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.applyFilters();
                });

                // Pesquisa em tempo real
                const searchInput = document.getElementById('searchInput');
                const debouncedSearch = AppUtils.debounce((query) => {
                    this.searchLogs(query);
                }, 300);
                
                searchInput.addEventListener('input', (e) => {
                    debouncedSearch(e.target.value);
                });
            }

            setDefaultDates() {
                const today = new Date();
                const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                
                document.getElementById('dateFrom').value = weekAgo.toISOString().split('T')[0];
                document.getElementById('dateTo').value = today.toISOString().split('T')[0];
            }

            async loadTodayStats() {
                try {
                    const response = await AppUtils.get('/api/logs/today');
                    if (response.success && response.statistics) {
                        const stats = response.statistics;
                        
                        document.getElementById('todaySuccess').textContent = stats.sucessos || 0;
                        document.getElementById('todayFails').textContent = stats.falhas || 0;
                        document.getElementById('todayTotal').textContent = stats.total || 0;
                        
                        const successRate = stats.total > 0 ? 
                            ((stats.sucessos / stats.total) * 100).toFixed(1) + '%' : '0%';
                        document.getElementById('successRate').textContent = successRate;
                    }
                } catch (error) {
                    console.error('Erro ao carregar estatísticas:', error);
                }
            }

            async loadLogs(page = 1) {
                try {
                    const params = new URLSearchParams({
                        limit: this.itemsPerPage,
                        offset: (page - 1) * this.itemsPerPage,
                        ...this.filters
                    });

                    const response = await AppUtils.get(`/api/logs?${params}`);
                    
                    if (response.success) {
                        this.displayLogs(response.logs);
                        this.totalLogs = response.total;
                        this.updatePagination(page);
                        this.updateLogsCount(response.count, response.total);
                    }
                } catch (error) {
                    console.error('Erro ao carregar logs:', error);
                    this.showErrorMessage('Erro ao carregar logs');
                }
            }

            displayLogs(logs) {
                const tbody = document.getElementById('logsTableBody');
                
                if (!logs || logs.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align: center; color: #666; padding: 40px;">
                                Nenhum log encontrado com os filtros aplicados
                            </td>
                        </tr>
                    `;
                    return;
                }

                tbody.innerHTML = logs.map(log => `
                    <tr data-searchable="logs">
                        <td>
                            <div style="font-weight: 500;">${AppUtils.formatDateTime(log.data_hora)}</div>
                            <div style="color: #666; font-size: 12px;">${AppUtils.timeAgo(log.data_hora)}</div>
                        </td>
                        <td>
                            <strong>${log.nome || 'Desconhecido'}</strong>
                        </td>
                        <td>
                            <span class="badge badge-info">${log.metodo}</span>
                        </td>
                        <td>
                            <span class="badge ${log.result === 'Sucesso' ? 'badge-success' : 'badge-danger'}">
                                ${log.result}
                            </span>
                        </td>
                        <td>
                            <div style="font-family: monospace; font-size: 12px;">
                                ${log.token ? `Token: ${log.token.substring(0, 8)}...` : ''}
                                ${log.tag ? `Tag: ${log.tag}` : ''}
                            </div>
                        </td>
                    </tr>
                `).join('');
            }

            searchLogs(query) {
                const rows = document.querySelectorAll('[data-searchable="logs"]');
                const lowercaseQuery = query.toLowerCase();
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const matches = text.includes(lowercaseQuery);
                    row.style.display = matches ? '' : 'none';
                });
            }

            applyFilters() {
                const form = document.getElementById('filtersForm');
                const formData = new FormData(form);
                
                this.filters = {};
                
                if (formData.get('date_from')) {
                    this.filters.date_from = formData.get('date_from');
                }
                if (formData.get('date_to')) {
                    this.filters.date_to = formData.get('date_to');
                }
                if (formData.get('user')) {
                    this.filters.user = formData.get('user');
                }
                if (formData.get('method')) {
                    this.filters.method = formData.get('method');
                }
                if (formData.get('result') === 'success') {
                    this.filters.success_only = 'true';
                } else if (formData.get('result') === 'failed') {
                    this.filters.failed_only = 'true';
                }
                
                this.currentPage = 1;
                this.loadLogs(1);
                AppUtils.showAlert('Filtros aplicados com sucesso!', 'success', 3000);
            }

            clearFilters() {
                document.getElementById('filtersForm').reset();
                this.filters = {};
                this.currentPage = 1;
                this.setDefaultDates();
                this.loadLogs(1);
                AppUtils.showAlert('Filtros removidos!', 'info', 3000);
            }

            updatePagination(currentPage) {
                const totalPages = Math.ceil(this.totalLogs / this.itemsPerPage);
                const pagination = document.getElementById('pagination');
                
                if (totalPages <= 1) {
                    pagination.style.display = 'none';
                    return;
                }
                
                pagination.style.display = 'flex';
                
                let paginationHTML = '';
                
                // Botão anterior
                if (currentPage > 1) {
                    paginationHTML += `<a href="#" class="pagination-item" onclick="logsPage.loadLogs(${currentPage - 1})">← Anterior</a>`;
                } else {
                    paginationHTML += `<span class="pagination-item disabled">← Anterior</span>`;
                }
                
                // Páginas
                const startPage = Math.max(1, currentPage - 2);
                const endPage = Math.min(totalPages, currentPage + 2);
                
                if (startPage > 1) {
                    paginationHTML += `<a href="#" class="pagination-item" onclick="logsPage.loadLogs(1)">1</a>`;
                    if (startPage > 2) {
                        paginationHTML += `<span class="pagination-item disabled">...</span>`;
                    }
                }
                
                for (let i = startPage; i <= endPage; i++) {
                    if (i === currentPage) {
                        paginationHTML += `<span class="pagination-item active">${i}</span>`;
                    } else {
                        paginationHTML += `<a href="#" class="pagination-item" onclick="logsPage.loadLogs(${i})">${i}</a>`;
                    }
                }
                
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        paginationHTML += `<span class="pagination-item disabled">...</span>`;
                    }
                    paginationHTML += `<a href="#" class="pagination-item" onclick="logsPage.loadLogs(${totalPages})">${totalPages}</a>`;
                }
                
                // Botão próximo
                if (currentPage < totalPages) {
                    paginationHTML += `<a href="#" class="pagination-item" onclick="logsPage.loadLogs(${currentPage + 1})">Próximo →</a>`;
                } else {
                    paginationHTML += `<span class="pagination-item disabled">Próximo →</span>`;
                }
                
                pagination.innerHTML = paginationHTML;
                this.currentPage = currentPage;
            }

            updateLogsCount(count, total) {
                const logsCount = document.getElementById('logsCount');
                logsCount.textContent = `Mostrando ${count} de ${total} registos`;
            }

            async createActivityChart() {
                try {
                    const response = await AppUtils.get('/api/logs/today');
                    const logs = response.logs || [];
                    
                    // Agrupar por hora
                    const hourlyData = new Array(24).fill(0);
                    logs.forEach(log => {
                        const hour = new Date(log.data_hora).getHours();
                        hourlyData[hour]++;
                    });

                    const config = {
                        type: 'bar',
                        data: {
                            labels: Array.from({length: 24}, (_, i) => `${i}:00`),
                            datasets: [{
                                label: 'Acessos',
                                data: hourlyData,
                                backgroundColor: 'rgba(102, 126, 234, 0.6)',
                                borderColor: '#667eea',
                                borderWidth: 1
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

                    AppUtils.createChart('activityChart', config);
                } catch (error) {
                    console.error('Erro ao criar gráfico de atividade:', error);
                }
            }

            async exportLogs() {
                try {
                    AppUtils.showAlert('Preparando exportação...', 'info', 2000);
                    
                    const params = new URLSearchParams({
                        limit: 10000, // Limite alto para exportação
                        ...this.filters
                    });

                    const response = await AppUtils.get(`/api/logs?${params}`);
                    
                    if (response.success && response.logs) {
                        this.downloadCSV(response.logs);
                        AppUtils.showAlert('Logs exportados com sucesso!', 'success');
                    } else {
                        AppUtils.showAlert('Nenhum log para exportar', 'warning');
                    }
                } catch (error) {
                    console.error('Erro ao exportar logs:', error);
                    AppUtils.showAlert('Erro ao exportar logs', 'danger');
                }
            }

            downloadCSV(logs) {
                const headers = ['Data/Hora', 'Utilizador', 'Método', 'Resultado', 'Token', 'Tag'];
                
                const csvContent = [
                    headers.join(','),
                    ...logs.map(log => [
                        `"${AppUtils.formatDateTime(log.data_hora)}"`,
                        `"${log.nome || 'Desconhecido'}"`,
                        `"${log.metodo}"`,
                        `"${log.result}"`,
                        `"${log.token || ''}"`,
                        `"${log.tag || ''}"`
                    ].join(','))
                ].join('\n');

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                
                if (link.download !== undefined) {
                    const url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', `logs_accesspy_${new Date().toISOString().split('T')[0]}.csv`);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                }
            }

            showErrorMessage(message) {
                const tbody = document.getElementById('logsTableBody');
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; color: #ef4444; padding: 40px;">
                            ${message}
                        </td>
                    </tr>
                `;
            }
        }

        // Global functions for onclick handlers
        function clearFilters() {
            logsPage.clearFilters();
        }

        function exportLogs() {
            logsPage.exportLogs();
        }

        // Initialize page
        let logsPage;
        document.addEventListener('DOMContentLoaded', () => {
            logsPage = new LogsPage();
        });
    </script>
</body>
</html>