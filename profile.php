<?php
// profile.php - Página de perfil do utilizador
require_once 'config.php';

// Verificar se o usuário está logado
checkLogin();

// Obter dados do utilizador atual
$userId = $_SESSION['user_id'] ?? null;
$userData = null;
$userLogs = null;
$apiError = null;

if ($userId) {
    try {
        $userData = $api->get("/api/users/{$userId}");
        $userLogs = $api->get("/api/logs/user/{$userId}?limit=20");
    } catch (Exception $e) {
        $apiError = $e->getMessage();
    }
}

$pageTitle = 'Perfil';
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
            <h1 class="page-title">Meu Perfil</h1>
            <p class="page-subtitle">Gerir informações pessoais e configurações da conta</p>
        </div>

        <?php if ($apiError): ?>
            <div class="alert alert-danger">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                Erro de conexão com a API: <?= htmlspecialchars($apiError) ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
            <!-- Informações Pessoais -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informações Pessoais</h3>
                </div>
                <div class="card-body">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(45deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: bold; margin: 0 auto 15px;">
                            <?= getUserInitial($_SESSION['user_name'] ?? 'User') ?>
                        </div>
                        <h4 style="margin: 0; color: #333;"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Utilizador') ?></h4>
                        <p style="color: #666; margin: 5px 0 0 0;"><?= htmlspecialchars($_SESSION['user_cargo'] ?? 'Utilizador') ?></p>
                    </div>
                    
                    <?php if ($userData && $userData['success']): ?>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                <span style="color: #666;">Username:</span>
                                <strong><?= htmlspecialchars($userData['user']['username'] ?: 'Não definido') ?></strong>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                <span style="color: #666;">Token de Acesso:</span>
                                <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                    <?= $userData['user']['token'] ? substr($userData['user']['token'], 0, 8) . '...' : 'Não definido' ?>
                                </code>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                                <span style="color: #666;">Tag RFID:</span>
                                <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                    <?= htmlspecialchars($userData['user']['tag'] ?: 'Não definido') ?>
                                </code>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
                                <span style="color: #666;">Conta criada:</span>
                                <strong><?= $userData['user']['created_at'] ? formatDateTime($userData['user']['created_at']) : 'N/A' ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <button class="btn btn-primary" onclick="openEditProfileModal()" style="width: 100%; margin-top: 20px;">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                        </svg>
                        Editar Perfil
                    </button>
                </div>
            </div>

            <!-- Estatísticas Pessoais -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Estatísticas de Acesso</h3>
                </div>
                <div class="card-body">
                    <?php if ($userLogs && $userLogs['success']): ?>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                            <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 12px;">
                                <div style="font-size: 32px; font-weight: bold; color: #10b981;"><?= $userLogs['statistics']['sucessos'] ?? 0 ?></div>
                                <div style="color: #666; font-size: 14px;">Total de Acessos</div>
                            </div>
                            
                            <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 12px;">
                                <div style="font-size: 32px; font-weight: bold; color: #667eea;"><?= number_format($userLogs['statistics']['taxa_sucesso'] ?? 0, 1) ?>%</div>
                                <div style="color: #666; font-size: 14px;">Taxa de Sucesso</div>
                            </div>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <span style="color: #666;">Último acesso:</span>
                            <strong>
                                <?php 
                                if ($userData && $userData['success'] && $userData['user']['statistics']['ultimo_acesso']) {
                                    echo timeAgo($userData['user']['statistics']['ultimo_acesso']);
                                } else {
                                    echo 'Nunca';
                                }
                                ?>
                            </strong>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #666;">Tentativas falhadas:</span>
                            <strong style="color: #ef4444;"><?= $userLogs['statistics']['falhas'] ?? 0 ?></strong>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; color: #666; padding: 40px;">
                            Estatísticas não disponíveis
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Alterar Password -->
        <div class="card" style="margin-bottom: 25px;">
            <div class="card-header">
                <h3 class="card-title">Segurança da Conta</h3>
            </div>
            <div class="card-body">
                <form id="passwordForm" class="validate-form" style="max-width: 500px;">
                    <div class="form-group">
                        <label for="currentPassword" class="form-label">Password Atual</label>
                        <input type="password" id="currentPassword" name="currentPassword" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="newPassword" class="form-label">Nova Password</label>
                        <input type="password" id="newPassword" name="newPassword" class="form-control" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Confirmar Nova Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        Alterar Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Histórico de Acessos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Histórico de Acessos Recentes</h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Método</th>
                                <th>Resultado</th>
                                <th>Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($userLogs && $userLogs['success'] && !empty($userLogs['logs'])): ?>
                                <?php foreach (array_slice($userLogs['logs'], 0, 10) as $log): ?>
                                    <tr>
                                        <td>
                                            <div style="font-weight: 500;"><?= formatDateTime($log['data_hora']) ?></div>
                                            <div style="color: #666; font-size: 12px;"><?= timeAgo($log['data_hora']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?= htmlspecialchars($log['metodo']) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $log['result'] === 'Sucesso' ? 'badge-success' : 'badge-danger' ?>">
                                                <?= htmlspecialchars($log['result']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-family: monospace; font-size: 12px; color: #666;">
                                                <?php if ($log['token']): ?>
                                                    Token: <?= substr($log['token'], 0, 8) ?>...
                                                <?php endif; ?>
                                                <?php if ($log['tag']): ?>
                                                    Tag: <?= htmlspecialchars($log['tag']) ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #666; padding: 40px;">
                                        Nenhum acesso registado
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Perfil -->
    <div class="modal" id="editProfileModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Editar Perfil</h3>
                <button class="modal-close" onclick="closeEditProfileModal()">&times;</button>
            </div>
            <form id="editProfileForm" class="validate-form">
                <div class="form-group">
                    <label for="editNome" class="form-label">Nome *</label>
                    <input type="text" id="editNome" name="nome" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="editUsername" class="form-label">Username</label>
                    <input type="text" id="editUsername" name="username" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="editCargo" class="form-label">Cargo</label>
                    <input type="text" id="editCargo" name="cargo" class="form-control">
                </div>
                
                <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditProfileModal()">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="saveProfileBtn">
                        <span class="btn-text">Guardar Alterações</span>
                        <div class="loading" style="display: none; width: 16px; height: 16px; border-width: 2px; margin-left: 8px;"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
    <script>
        class ProfilePage {
            constructor() {
                this.userId = <?= $userId ?? 'null' ?>;
                this.init();
            }

            init() {
                this.setupEventListeners();
            }

            setupEventListeners() {
                // Formulário de alteração de password
                document.getElementById('passwordForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    await this.changePassword();
                });

                // Formulário de edição de perfil
                document.getElementById('editProfileForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    await this.updateProfile();
                });
            }

            async changePassword() {
                const form = document.getElementById('passwordForm');
                const formData = new FormData(form);
                
                const currentPassword = formData.get('currentPassword');
                const newPassword = formData.get('newPassword');
                const confirmPassword = formData.get('confirmPassword');
                
                if (newPassword !== confirmPassword) {
                    app.showAlert('As passwords não coincidem', 'danger');
                    return;
                }
                
                if (newPassword.length < 6) {
                    app.showAlert('A password deve ter pelo menos 6 caracteres', 'danger');
                    return;
                }
                
                try {
                    const response = await app.put(`/api/users/${this.userId}`, {
                        password: newPassword
                    });
                    
                    if (response.success) {
                        app.showAlert('Password alterada com sucesso!', 'success');
                        form.reset();
                    } else {
                        app.showAlert(response.error || 'Erro ao alterar password', 'danger');
                    }
                } catch (error) {
                    console.error('Erro ao alterar password:', error);
                    app.showAlert('Erro de conexão', 'danger');
                }
            }

            openEditProfileModal() {
                const userData = <?= json_encode($userData['user'] ?? []) ?>;
                
                if (userData) {
                    document.getElementById('editNome').value = userData.nome || '';
                    document.getElementById('editUsername').value = userData.username || '';
                    document.getElementById('editCargo').value = userData.cargo || '';
                }
                
                app.openModal('editProfileModal');
            }

            async updateProfile() {
                const form = document.getElementById('editProfileForm');
                const btn = document.getElementById('saveProfileBtn');
                const btnText = btn.querySelector('.btn-text');
                const loading = btn.querySelector('.loading');
                
                if (!app.validateForm(form)) {
                    return;
                }
                
                const formData = new FormData(form);
                const profileData = {
                    nome: formData.get('nome'),
                    username: formData.get('username') || null,
                    cargo: formData.get('cargo') || null
                };
                
                try {
                    btn.disabled = true;
                    btnText.style.display = 'none';
                    loading.style.display = 'inline-block';
                    
                    const response = await app.put(`/api/users/${this.userId}`, profileData);
                    
                    if (response.success) {
                        app.showAlert('Perfil atualizado com sucesso!', 'success');
                        this.closeEditProfileModal();
                        
                        // Atualizar dados da sessão
                        if (profileData.nome) {
                            // Recarregar página para atualizar header
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } else {
                        app.showAlert(response.error || 'Erro ao atualizar perfil', 'danger');
                    }
                } catch (error) {
                    console.error('Erro ao atualizar perfil:', error);
                    app.showAlert('Erro de conexão', 'danger');
                } finally {
                    btn.disabled = false;
                    btnText.style.display = 'inline';
                    loading.style.display = 'none';
                }
            }

            closeEditProfileModal() {
                app.closeModal('editProfileModal');
            }
        }

        // Global functions for onclick handlers
        function openEditProfileModal() {
            profilePage.openEditProfileModal();
        }

        function closeEditProfileModal() {
            profilePage.closeEditProfileModal();
        }

        // Initialize page
        let profilePage;
        document.addEventListener('DOMContentLoaded', () => {
            profilePage = new ProfilePage();
        });
    </script>
</body>
</html>