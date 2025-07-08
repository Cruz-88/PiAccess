// assets/js/app.js - JavaScript global

class AccessPyApp {
    constructor() {
        this.apiBase = window.location.origin;
        this.apiKey = 'cruz';
        this.charts = {};
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.startPeriodicUpdates();
    }

    // Event Listeners
    setupEventListeners() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeComponents();
        });

        // Modal handlers
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.closeModal(e.target);
            }
        });

        // Form validation
        document.addEventListener('submit', (e) => {
            if (e.target.classList.contains('validate-form')) {
                if (!this.validateForm(e.target)) {
                    e.preventDefault();
                }
            }
        });
    }

    initializeComponents() {
        // Initialize tooltips
        this.initTooltips();
        
        // Initialize any existing charts
        this.initCharts();
        
        // Initialize search functionality
        this.initSearch();
    }

    // API Methods
    async apiRequest(endpoint, options = {}) {
        const url = `${this.apiBase}${endpoint}`;
        
        const defaultOptions = {
            headers: {
                'X-API-Key': this.apiKey,
                'Content-Type': 'application/json'
            }
        };

        const mergedOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers
            }
        };

        try {
            const response = await fetch(url, mergedOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            this.updateApiStatus(true);
            return data;
        } catch (error) {
            console.error('API Error:', error);
            this.updateApiStatus(false);
            throw error;
        }
    }

    async get(endpoint) {
        return this.apiRequest(endpoint, { method: 'GET' });
    }

    async post(endpoint, data = null) {
        return this.apiRequest(endpoint, {
            method: 'POST',
            body: data ? JSON.stringify(data) : null
        });
    }

    async put(endpoint, data = null) {
        return this.apiRequest(endpoint, {
            method: 'PUT',
            body: data ? JSON.stringify(data) : null
        });
    }

    async delete(endpoint) {
        return this.apiRequest(endpoint, { method: 'DELETE' });
    }

    // UI Methods
    updateApiStatus(isOnline) {
        const indicators = document.querySelectorAll('.status-indicator');
        const statusTexts = document.querySelectorAll('#apiStatusText, [data-status="api"]');
        
        indicators.forEach(indicator => {
            if (indicator.id === 'apiStatus' || indicator.hasAttribute('data-api-status')) {
                indicator.className = `status-indicator ${isOnline ? 'status-online' : 'status-offline'}`;
            }
        });
        
        statusTexts.forEach(text => {
            text.textContent = isOnline ? 'Online' : 'Offline';
        });
    }

    showAlert(message, type = 'info', duration = 5000) {
        const alertContainer = document.getElementById('alert-container') || this.createAlertContainer();
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.innerHTML = `
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                ${this.getAlertIcon(type)}
            </svg>
            <span>${message}</span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        `;
        
        alertContainer.appendChild(alert);
        
        // Auto remove
        if (duration > 0) {
            setTimeout(() => {
                if (alert.parentElement) {
                    alert.remove();
                }
            }, duration);
        }
        
        return alert;
    }

    createAlertContainer() {
        const container = document.createElement('div');
        container.id = 'alert-container';
        container.style.cssText = `
            position: fixed;
            top: 90px;
            right: 30px;
            z-index: 1050;
            max-width: 400px;
        `;
        document.body.appendChild(container);
        return container;
    }

    getAlertIcon(type) {
        const icons = {
            success: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>',
            danger: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>',
            warning: '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>',
            info: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>'
        };
        return icons[type] || icons.info;
    }

    // Modal Methods
    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    closeModal(modal) {
        if (typeof modal === 'string') {
            modal = document.getElementById(modal);
        }
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    // Form Methods
    validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            this.clearFieldError(field);
            
            if (!field.value.trim()) {
                this.showFieldError(field, 'Este campo é obrigatório');
                isValid = false;
            } else if (field.type === 'email' && !this.isValidEmail(field.value)) {
                this.showFieldError(field, 'Email inválido');
                isValid = false;
            }
        });
        
        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add('error');
        
        let errorElement = field.parentElement.querySelector('.form-error');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'form-error';
            field.parentElement.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    clearFieldError(field) {
        field.classList.remove('error');
        const errorElement = field.parentElement.querySelector('.form-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Chart Methods
    createChart(canvasId, config) {
        const canvas = document.getElementById(canvasId);
        if (!canvas || !window.Chart) {
            console.warn(`Canvas ${canvasId} not found or Chart.js not loaded`);
            return null;
        }

        // Destroy existing chart
        if (this.charts[canvasId]) {
            this.charts[canvasId].destroy();
        }

        const ctx = canvas.getContext('2d');
        this.charts[canvasId] = new Chart(ctx, config);
        return this.charts[canvasId];
    }

    updateChart(canvasId, newData) {
        const chart = this.charts[canvasId];
        if (chart) {
            chart.data = newData;
            chart.update();
        }
    }

    initCharts() {
        // This will be called by individual pages to initialize their specific charts
    }

    // Utility Methods
    formatDateTime(dateTime) {
        return new Date(dateTime).toLocaleString('pt-PT');
    }

    formatDate(date) {
        return new Date(date).toLocaleDateString('pt-PT');
    }

    timeAgo(dateTime) {
        const now = new Date();
        const time = new Date(dateTime);
        const diffInSeconds = Math.floor((now - time) / 1000);
        
        if (diffInSeconds < 60) return 'agora mesmo';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} min atrás`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} h atrás`;
        if (diffInSeconds < 2592000) return `${Math.floor(diffInSeconds / 86400)} dias atrás`;
        
        return this.formatDate(dateTime);
    }

    debounce(func, wait) {
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

    // Search functionality
    initSearch() {
        const searchInputs = document.querySelectorAll('[data-search]');
        searchInputs.forEach(input => {
            const target = input.getAttribute('data-search');
            const debouncedSearch = this.debounce(() => {
                this.performSearch(input.value, target);
            }, 300);
            
            input.addEventListener('input', debouncedSearch);
        });
    }

    performSearch(query, target) {
        const targetElements = document.querySelectorAll(`[data-searchable="${target}"]`);
        
        targetElements.forEach(element => {
            const text = element.textContent.toLowerCase();
            const matches = text.includes(query.toLowerCase());
            element.style.display = matches ? '' : 'none';
        });
    }

    // Tooltip functionality
    initTooltips() {
        const tooltipElements = document.querySelectorAll('[title], [data-tooltip]');
        tooltipElements.forEach(element => {
            this.addTooltip(element);
        });
    }

    addTooltip(element) {
        const tooltipText = element.getAttribute('data-tooltip') || element.getAttribute('title');
        if (!tooltipText) return;

        element.removeAttribute('title'); // Remove default browser tooltip

        let tooltip;

        element.addEventListener('mouseenter', () => {
            tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            tooltip.textContent = tooltipText;
            tooltip.style.cssText = `
                position: absolute;
                background: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 12px;
                z-index: 1000;
                pointer-events: none;
                opacity: 0;
                transition: opacity 0.2s;
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = element.getBoundingClientRect();
            tooltip.style.left = `${rect.left + rect.width / 2 - tooltip.offsetWidth / 2}px`;
            tooltip.style.top = `${rect.top - tooltip.offsetHeight - 8}px`;
            
            setTimeout(() => tooltip.style.opacity = '1', 50);
        });

        element.addEventListener('mouseleave', () => {
            if (tooltip) {
                tooltip.remove();
                tooltip = null;
            }
        });
    }

    // Periodic updates
    startPeriodicUpdates() {
        // Update system status every 30 seconds
        setInterval(() => {
            this.updateSystemStatus();
        }, 30000);

        // Update API status every 10 seconds
        setInterval(() => {
            this.checkApiStatus();
        }, 10000);
    }

    async updateSystemStatus() {
        try {
            const status = await this.get('/api/status');
            // Update UI with status data
            this.updateSystemTemp();
        } catch (error) {
            console.error('Failed to update system status:', error);
        }
    }

    async checkApiStatus() {
        try {
            await this.get('/');
            this.updateApiStatus(true);
        } catch (error) {
            this.updateApiStatus(false);
        }
    }

    updateSystemTemp() {
        const temp = (Math.random() * 20 + 40).toFixed(1);
        const tempElements = document.querySelectorAll('#systemTemp, [data-temp]');
        const tempStatus = document.querySelectorAll('#tempStatus, [data-temp-status]');
        
        tempElements.forEach(element => {
            element.textContent = `${temp}°C`;
        });
        
        tempStatus.forEach(status => {
            if (temp > 70) {
                status.className = 'status-indicator status-offline';
            } else {
                status.className = 'status-indicator status-online';
            }
        });
    }

    // Loading states
    showLoading(element) {
        if (typeof element === 'string') {
            element = document.getElementById(element);
        }
        
        if (element) {
            element.innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                    Carregando...
                </div>
            `;
        }
    }

    hideLoading(element) {
        if (typeof element === 'string') {
            element = document.getElementById(element);
        }
        
        if (element) {
            const loading = element.querySelector('.loading');
            if (loading) {
                loading.remove();
            }
        }
    }

    // Data formatting for tables
    formatTableData(data, columns) {
        return data.map(row => {
            const formattedRow = {};
            columns.forEach(col => {
                const value = row[col.key];
                
                if (col.type === 'datetime') {
                    formattedRow[col.key] = this.formatDateTime(value);
                } else if (col.type === 'date') {
                    formattedRow[col.key] = this.formatDate(value);
                } else if (col.type === 'timeago') {
                    formattedRow[col.key] = this.timeAgo(value);
                } else if (col.type === 'badge') {
                    formattedRow[col.key] = `<span class="badge badge-${col.badgeClass(value)}">${value}</span>`;
                } else {
                    formattedRow[col.key] = value;
                }
            });
            return formattedRow;
        });
    }
}

// Initialize the app
const app = new AccessPyApp();

// Export for use in other scripts
window.AccessPyApp = app;