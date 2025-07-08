<?php
// login.php - Página de login
require_once 'config.php';

$error = '';
$success = '';

// Se já estiver logado, redirecionar para dashboard
if (isset($_SESSION['user_logged']) && $_SESSION['user_logged']) {
    header('Location: dashboard.php');
    exit;
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username e password são obrigatórios';
    } else {
        try {
            $response = $api->post('/login', [
                'username' => $username,
                'password' => $password
            ]);

            if ($response['success']) {
                $_SESSION['user_logged'] = true;
                $_SESSION['user_id'] = $response['user']['id'];
                $_SESSION['user_name'] = $response['user']['nome'];
                $_SESSION['user_username'] = $response['user']['username'];
                $_SESSION['user_token'] = $response['user']['token'];
                $_SESSION['user_cargo'] = $response['user']['cargo'] ?? '';

                header('Location: dashboard.php');
                exit;
            } else {
                $error = $response['error'] ?? 'Erro no login';
            }
        } catch (Exception $e) {
            $error = 'Erro de conexão: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= SYSTEM_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 50px;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo {
            font-size: 36px;
            font-weight: bold;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 40px;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .version {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .loading {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px;
                margin: 20px;
            }

            .logo {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo"><?= SYSTEM_NAME ?></div>
        <div class="subtitle">Sistema de Controle de Acesso</div>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required
                    autocomplete="current-password">
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <span class="btn-text">Entrar</span>
                <div class="loading" id="loginLoading"></div>
            </button>
        </form>
    </div>

    <div class="version">
        <?= SYSTEM_NAME ?> v<?= SYSTEM_VERSION ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('loginForm');
            const btn = document.getElementById('loginBtn');
            const btnText = btn.querySelector('.btn-text');
            const loading = document.getElementById('loginLoading');

            form.addEventListener('submit', function () {
                btn.disabled = true;
                btnText.style.display = 'none';
                loading.style.display = 'block';
            });

            // Auto-focus no primeiro campo
            document.getElementById('username').focus();

            // Enter key navigation
            document.getElementById('username').addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    document.getElementById('password').focus();
                }
            });
        });
    </script>
</body>

</html>