<?php
// users.php - Página de gestão de utilizadores
require_once 'config.php';

// Verificar se o usuário está logado
checkLogin();

// Tentar obter dados da API
$usersData = null;
$apiError = null;

try {
    $usersData = $api->get('/api/users');
} catch (Exception $e) {
    $apiError = $e->getMessage();
}

$pageTitle = 'Utilizadores';
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
            <h1 class="page-title">Gestão de Utilizadores</h1>
            <p class="page-subtitle">Gerir utilizadores do sistema de acesso</p>
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

        <div class="card">
            <div class="card-header"
                style="display: flex; justify-content: space-between; align-items: center; padding: 25px 30px; border-bottom: 1px solid #f0f0f0;">
                <h3 class="card-title">Lista de Utilizadores</h3>
                <div style="display: flex; gap: 15px; align-items: center;">
                    <input type="text" placeholder="Pesquisar utilizadores..." class="form-control"
                        style="width: 250px;" id="searchInput">
                    <button class="btn btn-primary" onclick="openAddUserModal()">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Adicionar Utilizador
                    </button>
                </div>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table" id="usersTable">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Username</th>
                                <th>Token</th>
                                <th>Tag RFID</th>
                                <th>Cargo</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <?php if ($usersData && !empty($usersData['users'])): ?>
                                <?php foreach ($usersData['users'] as $user): ?>
                                    <tr data-searchable="users">
                                        <td><strong><?= htmlspecialchars($user['nome']) ?></strong></td>
                                        <td><?= htmlspecialchars($user['username'] ?: '-') ?></td>
                                        <td>
                                            <code
                                                style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 12px;">
                                                        <?= htmlspecialchars($user['token'] ?: '-') ?>
                                                    </code>
                                        </td>
                                        <td>
                                            <code
                                                style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 12px;">
                                                        <?= htmlspecialchars($user['tag'] ?: '-') ?>
                                                    </code>
                                        </td>
                                        <td><?= htmlspecialchars($user['cargo'] ?: '-') ?></td>
                                        <td><?= $user['created_at'] ? formatDateTime($user['created_at']) : '-' ?></td>
                                        <td>
                                            <div style="display: flex; gap: 8px;">
                                                <button class="btn btn-sm btn-secondary" onclick="editUser(<?= $user['id'] ?>)"
                                                    title="Editar">
                                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nome']) ?>')"
                                                    title="Eliminar">
                                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #666; padding: 40px;">
                                        Nenhum utilizador encontrado
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar/Editar Utilizador -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Adicionar Utilizador</h3>
                <button class="modal-close" onclick="closeUserModal()">&times;</button>
            </div>
            <form id="userForm" class="validate-form">
                <input type="hidden" id="userId" name="userId">

                <div class="form-group">
                    <label for="nome" class="form-label">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control">
                    <small style="color: #666; font-size: 12px;">Deixar em branco para manter a password atual (apenas
                        edição)</small>
                </div>

                <div class="form-group">
                    <label for="token" class="form-label">Token</label>
                    <input type="text" id="token" name="token" class="form-control">
                    <div style="display: flex; gap: 10px; margin-top: 8px;">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="generateToken()">Gerar
                            Token</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tag" class="form-label">Tag RFID</label>
                    <input type="text" id="tag" name="tag" class="form-control" placeholder="Ex: A1B2C3D4">
                </div>

                <div class="form-group">
                    <label for="cargo" class="form-label">Cargo</label>
                    <input type="text" id="cargo" name="cargo" class="form-control"
                        placeholder="Ex: Administrador, Funcionário">
                </div>

                <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
                    <button type="button" class="btn btn-secondary" onclick="closeUserModal()">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="saveUserBtn">
                        <span class="btn-text">Guardar</span>
                        <div class="loading"
                            style="display: none; width: 16px; height: 16px; border-width: 2px; margin-left: 8px;">
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Classe utilitária simples para users
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

            static async put(url, data) {
                return this.request(url, {
                    method: 'PUT',
                    body: JSON.stringify(data)
                });
            }

            static async delete(url) {
                return this.request(url, { method: 'DELETE' });
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

            static showAlert(message, type = 'info') {
                // Implementação simples de alert
                alert(message);
            }

            static validateForm(form) {
                const requiredFields = form.querySelectorAll('[required]');
                for (let field of requiredFields) {
                    if (!field.value.trim()) {
                        field.focus();
                        return false;
                    }
                }
                return true;
            }

            static openModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'flex';
                    modal.style.opacity = '1';
                    modal.style.visibility = 'visible';
                    modal.classList.add('show');
                }
            }

            static closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.opacity = '0';
                    modal.style.visibility = 'hidden';
                    modal.classList.remove('show');
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                }
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

        class UsersPage {
            constructor() {
                this.currentEditId = null;
                this.init();
            }

            init() {
                this.setupSearch();
                this.setupFormHandlers();
            }

            setupSearch() {
                const searchInput = document.getElementById('searchInput');
                const debouncedSearch = AppUtils.debounce((query) => {
                    this.filterUsers(query);
                }, 300);

                searchInput.addEventListener('input', (e) => {
                    debouncedSearch(e.target.value);
                });
            }

            filterUsers(query) {
                const rows = document.querySelectorAll('[data-searchable="users"]');
                const lowercaseQuery = query.toLowerCase();

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const matches = text.includes(lowercaseQuery);
                    row.style.display = matches ? '' : 'none';
                });
            }

            setupFormHandlers() {
                document.getElementById('userForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    await this.saveUser();
                });
            }

            async saveUser() {
                const form = document.getElementById('userForm');
                const btn = document.getElementById('saveUserBtn');
                const btnText = btn.querySelector('.btn-text');
                const loading = btn.querySelector('.loading');

                if (!AppUtils.validateForm(form)) {
                    return;
                }

                // Preparar dados
                const formData = new FormData(form);
                const userData = {
                    nome: formData.get('nome'),
                    username: formData.get('username') || null,
                    password: formData.get('password') || null,
                    token: formData.get('token') || null,
                    tag: formData.get('tag') || null,
                    cargo: formData.get('cargo') || null
                };

                // Remover campos vazios (exceto para criação)
                Object.keys(userData).forEach(key => {
                    if (userData[key] === '' || userData[key] === null) {
                        if (this.currentEditId && key === 'password') {
                            delete userData[key]; // Não enviar password vazia na edição
                        } else if (!userData[key]) {
                            delete userData[key];
                        }
                    }
                });

                try {
                    // Loading state
                    btn.disabled = true;
                    btnText.style.display = 'none';
                    loading.style.display = 'inline-block';

                    let response;
                    if (this.currentEditId) {
                        response = await AppUtils.put(`/api/users/${this.currentEditId}`, userData);
                    } else {
                        response = await AppUtils.post('/api/users', userData);
                    }

                    if (response.success) {
                        AppUtils.showAlert(
                            this.currentEditId ? 'Utilizador atualizado com sucesso!' : 'Utilizador criado com sucesso!',
                            'success'
                        );
                        this.closeModal();
                        await this.refreshUsersList();
                    } else {
                        AppUtils.showAlert(response.error || 'Erro ao guardar utilizador', 'danger');
                    }

                } catch (error) {
                    console.error('Erro ao guardar utilizador:', error);
                    AppUtils.showAlert('Erro de conexão: ' + error.message, 'danger');
                } finally {
                    btn.disabled = false;
                    btnText.style.display = 'inline';
                    loading.style.display = 'none';
                }
            }

            async refreshUsersList() {
                try {
                    const response = await AppUtils.get('/api/users');
                    const tbody = document.getElementById('usersTableBody');

                    if (response.users && response.users.length > 0) {
                        tbody.innerHTML = response.users.map(user => `
                            <tr data-searchable="users">
                                <td><strong>${user.nome}</strong></td>
                                <td>${user.username || '-'}</td>
                                <td><code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 12px;">${user.token || '-'}</code></td>
                                <td><code style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 12px;">${user.tag || '-'}</code></td>
                                <td>${user.cargo || '-'}</td>
                                <td>${user.created_at ? AppUtils.formatDateTime(user.created_at) : '-'}</td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <button class="btn btn-sm btn-secondary" onclick="usersPage.editUser(${user.id})" title="Editar">
                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                            </svg>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="usersPage.deleteUser(${user.id}, '${user.nome}')" title="Eliminar">
                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="7" style="text-align: center; color: #666; padding: 40px;">
                                    Nenhum utilizador encontrado
                                </td>
                            </tr>
                        `;
                    }
                } catch (error) {
                    console.error('Erro ao carregar utilizadores:', error);
                    AppUtils.showAlert('Erro ao carregar lista de utilizadores', 'danger');
                }
            }

            openAddModal() {
                this.currentEditId = null;
                document.getElementById('modalTitle').textContent = 'Adicionar Utilizador';
                document.getElementById('userForm').reset();
                document.getElementById('userId').value = '';
                AppUtils.openModal('userModal');
            }

            async editUser(userId) {
                try {
                    const response = await AppUtils.get(`/api/users/${userId}`);
                    if (response.success && response.user) {
                        this.currentEditId = userId;
                        const user = response.user;

                        document.getElementById('modalTitle').textContent = 'Editar Utilizador';
                        document.getElementById('userId').value = user.id;
                        document.getElementById('nome').value = user.nome || '';
                        document.getElementById('username').value = user.username || '';
                        document.getElementById('password').value = ''; // Sempre vazio para edição
                        document.getElementById('token').value = user.token || '';
                        document.getElementById('tag').value = user.tag || '';
                        document.getElementById('cargo').value = user.cargo || '';

                        AppUtils.openModal('userModal');
                    }
                } catch (error) {
                    console.error('Erro ao carregar utilizador:', error);
                    AppUtils.showAlert('Erro ao carregar dados do utilizador', 'danger');
                }
            }

            async deleteUser(userId, userName) {
                if (!confirm(`Tem certeza que deseja eliminar o utilizador "${userName}"?\n\nEsta ação não pode ser desfeita.`)) {
                    return;
                }

                try {
                    const response = await AppUtils.delete(`/api/users/${userId}`);
                    if (response.success) {
                        AppUtils.showAlert('Utilizador eliminado com sucesso!', 'success');
                        await this.refreshUsersList();
                    } else {
                        AppUtils.showAlert(response.error || 'Erro ao eliminar utilizador', 'danger');
                    }
                } catch (error) {
                    console.error('Erro ao eliminar utilizador:', error);
                    AppUtils.showAlert('Erro de conexão: ' + error.message, 'danger');
                }
            }

            closeModal() {
                AppUtils.closeModal('userModal');
                this.currentEditId = null;
            }

            generateToken() {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                let token = '';
                for (let i = 0; i < 32; i++) {
                    token += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                document.getElementById('token').value = token;
            }
        }

        // Global functions for onclick handlers
        function openAddUserModal() {
            usersPage.openAddModal();
        }

        function editUser(userId) {
            usersPage.editUser(userId);
        }

        function deleteUser(userId, userName) {
            usersPage.deleteUser(userId, userName);
        }

        function closeUserModal() {
            usersPage.closeModal();
        }

        function generateToken() {
            usersPage.generateToken();
        }

        // Initialize page
        let usersPage;
        document.addEventListener('DOMContentLoaded', () => {
            usersPage = new UsersPage();
        });
    </script>
</body>

</html>