<?php
// help.php - Página de ajuda e documentação
require_once 'config.php';

// Verificar se o usuário está logado
checkLogin();

$pageTitle = 'Ajuda';
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= SYSTEM_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .help-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .help-nav ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 0;
            padding: 0;
        }
        
        .help-nav a {
            text-decoration: none;
            color: #667eea;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .help-nav a:hover, .help-nav a.active {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }
        
        .help-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 25px;
        }
        
        .help-section h2 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .help-section h3 {
            color: #667eea;
            margin: 25px 0 15px 0;
        }
        
        .help-section h4 {
            color: #555;
            margin: 20px 0 10px 0;
        }
        
        .help-section code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #d63384;
        }
        
        .help-section pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            border-left: 4px solid #667eea;
        }
        
        .faq-item {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        
        .faq-question {
            background: #f8f9fa;
            padding: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease;
        }
        
        .faq-question:hover {
            background: #e9ecef;
        }
        
        .faq-answer {
            padding: 20px;
            display: none;
            border-top: 1px solid #e5e7eb;
        }
        
        .faq-icon {
            transition: transform 0.3s ease;
        }
        
        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }
        
        .faq-item.active .faq-answer {
            display: block;
        }
        
        .contact-card {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .feature-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
        }
        
        .feature-card h4 {
            color: #667eea;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Ajuda & Documentação</h1>
            <p class="page-subtitle">Guias e informações sobre o sistema AccessPy</p>
        </div>

        <!-- Navegação da Ajuda -->
        <nav class="help-nav">
            <ul>
                <li><a href="#overview" class="help-link active">Visão Geral</a></li>
                <li><a href="#getting-started" class="help-link">Primeiros Passos</a></li>
                <li><a href="#features" class="help-link">Funcionalidades</a></li>
                <li><a href="#api" class="help-link">API</a></li>
                <li><a href="#faq" class="help-link">FAQ</a></li>
                <li><a href="#troubleshooting" class="help-link">Resolução de Problemas</a></li>
                <li><a href="#contact" class="help-link">Contacto</a></li>
            </ul>
        </nav>

        <!-- Visão Geral -->
        <section id="overview" class="help-section">
            <h2>Visão Geral do Sistema</h2>
            
            <p>O <strong>AccessPy</strong> é um sistema completo de controle de acesso baseado em Raspberry Pi, que oferece múltiplas formas de autenticação e uma interface web moderna para gestão.</p>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <h4>🔐 Controle de Acesso</h4>
                    <p>Autenticação via cartões RFID, tokens de API e interface web com gestão completa de utilizadores.</p>
                </div>
                
                <div class="feature-card">
                    <h4>📊 Dashboard Inteligente</h4>
                    <p>Visualização em tempo real de estatísticas, gráficos de atividade e logs de acesso detalhados.</p>
                </div>
                
                <div class="feature-card">
                    <h4>🛠️ Gestão de Hardware</h4>
                    <p>Controle e teste de componentes como relé, LED, buzzer e leitor RFID diretamente da interface.</p>
                </div>
                
                <div class="feature-card">
                    <h4>📱 Interface Responsiva</h4>
                    <p>Design moderno e adaptável para desktop, tablet e dispositivos móveis.</p>
                </div>
            </div>
        </section>

        <!-- Primeiros Passos -->
        <section id="getting-started" class="help-section" style="display: none;">
            <h2>Primeiros Passos</h2>
            
            <h3>1. Acesso ao Sistema</h3>
            <p>Para aceder ao sistema, navegue até a página de login e introduza as suas credenciais:</p>
            <ul>
                <li><strong>Username:</strong> O seu nome de utilizador</li>
                <li><strong>Password:</strong> A sua palavra-passe</li>
            </ul>
            
            <h3>2. Navegação Principal</h3>
            <p>O sistema está organizado em 5 secções principais:</p>
            <ul>
                <li><strong>Dashboard:</strong> Visão geral e estatísticas do sistema</li>
                <li><strong>Utilizadores:</strong> Gestão de utilizadores e permissões</li>
                <li><strong>Logs:</strong> Visualização de registos de acesso</li>
                <li><strong>Configurações:</strong> Configuração do sistema e teste de hardware</li>
                <li><strong>Ajuda:</strong> Esta secção de documentação</li>
            </ul>
            
            <h3>3. Gestão de Utilizadores</h3>
            <p>Para adicionar um novo utilizador:</p>
            <ol>
                <li>Vá para a secção "Utilizadores"</li>
                <li>Clique em "Adicionar Utilizador"</li>
                <li>Preencha os dados necessários</li>
                <li>Guarde as alterações</li>
            </ol>
            
            <h3>4. Monitorização</h3>
            <p>O dashboard atualiza automaticamente a cada 30 segundos, mostrando:</p>
            <ul>
                <li>Acessos e falhas do dia</li>
                <li>Total de utilizadores</li>
                <li>Gráficos de atividade</li>
                <li>Logs recentes</li>
            </ul>
        </section>

        <!-- Funcionalidades -->
        <section id="features" class="help-section" style="display: none;">
            <h2>Funcionalidades Detalhadas</h2>
            
            <h3>Dashboard</h3>
            <ul>
                <li>Estatísticas em tempo real</li>
                <li>Gráficos de acessos por hora</li>
                <li>Distribuição de métodos de acesso</li>
                <li>Lista de atividade recente</li>
                <li>Indicadores de sistema (temperatura, status da API)</li>
            </ul>
            
            <h3>Gestão de Utilizadores</h3>
            <ul>
                <li>Criar, editar e eliminar utilizadores</li>
                <li>Gestão de tokens de acesso</li>
                <li>Configuração de tags RFID</li>
                <li>Pesquisa e filtros</li>
                <li>Geração automática de tokens</li>
            </ul>
            
            <h3>Sistema de Logs</h3>
            <ul>
                <li>Visualização de todos os acessos</li>
                <li>Filtros avançados (data, utilizador, método)</li>
                <li>Exportação para CSV</li>
                <li>Gráficos de atividade</li>
                <li>Pesquisa em tempo real</li>
            </ul>
            
            <h3>Configurações</h3>
            <ul>
                <li>Teste individual de componentes</li>
                <li>Monitorização de temperatura</li>
                <li>Controle manual da porta</li>
                <li>Teste de conectividade da API</li>
                <li>Informações do sistema</li>
            </ul>
        </section>

        <!-- API -->
        <section id="api" class="help-section" style="display: none;">
            <h2>Documentação da API</h2>
            
            <h3>Autenticação</h3>
            <p>Todos os endpoints protegidos requerem o header <code>X-API-Key</code> com a chave de API válida.</p>
            
            <h3>Endpoints Principais</h3>
            
            <h4>Informações do Sistema</h4>
            <pre><code>GET /
Retorna informações básicas do sistema e lista de endpoints disponíveis.</code></pre>
            
            <h4>Autenticação</h4>
            <pre><code>POST /login
Body: { "username": "user", "password": "pass" }
Autentica um utilizador e retorna os dados do perfil.</code></pre>
            
            <h4>Controle de Porta</h4>
            <pre><code>POST /api/abrir
Body: { "token": "user_token" }
Abre a porta usando o token do utilizador.</code></pre>
            
            <h4>Utilizadores</h4>
            <pre><code>GET /api/users - Lista todos os utilizadores
POST /api/users - Cria um novo utilizador
GET /api/users/{id} - Obtém dados de um utilizador
PUT /api/users/{id} - Atualiza um utilizador
DELETE /api/users/{id} - Remove um utilizador</code></pre>
            
            <h4>Logs</h4>
            <pre><code>GET /api/logs - Lista logs com filtros opcionais
GET /api/logs/today - Logs apenas do dia atual
GET /api/logs/user/{id} - Logs de um utilizador específico</code></pre>
            
            <h4>Sistema</h4>
            <pre><code>GET /api/status - Status detalhado do sistema
GET /api/temperature - Temperatura do Raspberry Pi
POST /api/test-hardware - Teste de componentes de hardware</code></pre>
        </section>

        <!-- FAQ -->
        <section id="faq" class="help-section" style="display: none;">
            <h2>Perguntas Frequentes (FAQ)</h2>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span><strong>Como adicionar um novo utilizador?</strong></span>
                    <svg class="faq-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="faq-answer">
                    <p>Para adicionar um novo utilizador:</p>
                    <ol>
                        <li>Aceda à secção "Utilizadores" no menu lateral</li>
                        <li>Clique no botão "Adicionar Utilizador"</li>
                        <li>Preencha pelo menos o campo "Nome" (obrigatório)</li>
                        <li>Opcionalmente, adicione username, password, token e tag RFID</li>
                        <li>Clique em "Guardar" para criar o utilizador</li>
                    </ol>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span><strong>Como funciona a autenticação por RFID?</strong></span>
                    <svg class="faq-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="faq-answer">
                    <p>O sistema lê automaticamente as tags RFID apresentadas ao leitor. Quando uma tag é detectada:</p>
                    <ul>
                        <li>O sistema consulta a base de dados para verificar se a tag está registada</li>
                        <li>Se a tag for válida, a porta abre e o LED acende durante 4 segundos</li>
                        <li>Se a tag for inválida, o buzzer toca 3 vezes</li>
                        <li>Todos os acessos são registados nos logs</li>
                    </ul>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span><strong>O que fazer se o sistema não responde?</strong></span>
                    <svg class="faq-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="faq-answer">
                    <p>Se o sistema não responder:</p>
                    <ol>
                        <li>Verifique se o indicador "API" no topo da página está "Online"</li>
                        <li>Teste a conectividade na secção "Configurações"</li>
                        <li>Verifique se o Raspberry Pi está ligado e conectado à rede</li>
                        <li>Reinicie o serviço da API no Raspberry Pi</li>
                        <li>Se o problema persistir, consulte os logs de erro</li>
                    </ol>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span><strong>Como exportar os logs de acesso?</strong></span>
                    <svg class="faq-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="faq-answer">
                    <p>Para exportar os logs:</p>
                    <ol>
                        <li>Vá para a secção "Logs"</li>
                        <li>Aplique os filtros desejados (data, utilizador, método)</li>
                        <li>Clique no botão "Exportar"</li>
                        <li>O ficheiro CSV será descarregado automaticamente</li>
                    </ol>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span><strong>Como alterar a minha password?</strong></span>
                    <svg class="faq-icon" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="faq-answer">
                    <p>Para alterar a sua password:</p>
                    <ol>
                        <li>Clique no seu avatar no canto superior direito</li>
                        <li>Selecione "Perfil" no menu dropdown</li>
                        <li>Na secção de segurança, introduza a nova password</li>
                        <li>Confirme a nova password</li>
                        <li>Clique em "Guardar alterações"</li>
                    </ol>
                </div>
            </div>
        </section>

        <!-- Resolução de Problemas -->
        <section id="troubleshooting" class="help-section" style="display: none;">
            <h2>Resolução de Problemas</h2>
            
            <h3>Problemas Comuns</h3>
            
            <h4>🔴 API Offline</h4>
            <p><strong>Sintomas:</strong> Indicador "Offline" no topo da página, dados não carregam</p>
            <p><strong>Soluções:</strong></p>
            <ul>
                <li>Verificar se o Raspberry Pi está ligado e conectado à rede</li>
                <li>Confirmar se o serviço da API está a executar</li>
                <li>Verificar configurações de firewall</li>
                <li>Testar conectividade na secção "Configurações"</li>
            </ul>
            
            <h4>🟡 Temperatura Alta</h4>
            <p><strong>Sintomas:</strong> Indicador de temperatura vermelho, sistema lento</p>
            <p><strong>Soluções:</strong></p>
            <ul>
                <li>Verificar ventilação do Raspberry Pi</li>
                <li>Limpar poeira dos componentes</li>
                <li>Adicionar dissipadores de calor</li>
                <li>Verificar se não há sobrecarga de processos</li>
            </ul>
            
            <h4>🔵 RFID Não Funciona</h4>
            <p><strong>Sintomas:</strong> Cartões não são detectados</p>
            <p><strong>Soluções:</strong></p>
            <ul>
                <li>Verificar conexões do leitor RFID</li>
                <li>Testar hardware na secção "Configurações"</li>
                <li>Confirmar se a tag está registada no sistema</li>
                <li>Verificar alimentação do leitor</li>
            </ul>
            
            <h4>🟢 Porta Não Abre</h4>
            <p><strong>Sintomas:</strong> Acesso autorizado mas porta não abre</p>
            <p><strong>Soluções:</strong></p>
            <ul>
                <li>Testar relé na secção "Configurações"</li>
                <li>Verificar conexões elétricas</li>
                <li>Confirmar alimentação da fechadura</li>
                <li>Usar controle manual para teste</li>
            </ul>
            
            <h3>Códigos de Erro</h3>
            
            <h4>Erro 401 - Não Autorizado</h4>
            <p>API key inválida ou em falta. Verificar configurações de autenticação.</p>
            
            <h4>Erro 403 - Acesso Negado</h4>
            <p>Token de utilizador inválido ou utilizador sem permissões.</p>
            
            <h4>Erro 404 - Não Encontrado</h4>
            <p>Endpoint da API não existe ou utilizador não encontrado.</p>
            
            <h4>Erro 500 - Erro Interno</h4>
            <p>Problema no servidor. Verificar logs de erro do sistema.</p>
            
            <h3>Logs de Debug</h3>
            <p>Para análise avançada de problemas:</p>
            <ol>
                <li>Aceder aos logs de erro na secção "Configurações"</li>
                <li>Verificar ficheiros de log no Raspberry Pi</li>
                <li>Analisar logs do navegador (F12 → Console)</li>
                <li>Contactar suporte técnico se necessário</li>
            </ol>
        </section>

        <!-- Contacto -->
        <section id="contact" class="help-section" style="display: none;">
            <h2>Informações de Contacto</h2>
            
            <div class="contact-card">
                <h3 style="margin-bottom: 20px;">🆘 Precisa de Ajuda?</h3>
                <p style="margin-bottom: 25px;">A nossa equipa está disponível para ajudar com qualquer questão sobre o sistema AccessPy.</p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; text-align: left; background: rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 20px;">
                    <div>
                        <h4>📧 Email</h4>
                        <p>suporte@accesspy.com</p>
                    </div>
                    
                    <div>
                        <h4>📞 Telefone</h4>
                        <p>+351 XXX XXX XXX</p>
                    </div>
                    
                    <div>
                        <h4>🕒 Horário</h4>
                        <p>Seg-Sex: 9h-18h</p>
                    </div>
                    
                    <div>
                        <h4>🌐 Website</h4>
                        <p>www.accesspy.com</p>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                <div class="help-section" style="margin: 0;">
                    <h3>📚 Recursos Adicionais</h3>
                    <ul>
                        <li><a href="#" style="color: #667eea;">Manual do Utilizador (PDF)</a></li>
                        <li><a href="#" style="color: #667eea;">Vídeos Tutoriais</a></li>
                        <li><a href="#" style="color: #667eea;">Fórum da Comunidade</a></li>
                        <li><a href="#" style="color: #667eea;">Atualizações do Sistema</a></li>
                    </ul>
                </div>
                
                <div class="help-section" style="margin: 0;">
                    <h3>🔧 Informações Técnicas</h3>
                    <p><strong>Versão do Sistema:</strong> <?= SYSTEM_VERSION ?></p>
                    <p><strong>Última Atualização:</strong> <?= date('d/m/Y') ?></p>
                    <p><strong>Compatibilidade:</strong> Raspberry Pi 3B+, 4B</p>
                    <p><strong>Requisitos:</strong> Python 3.7+, Flask, SQLite</p>
                </div>
            </div>
        </section>
    </div>

    <script src="assets/js/app.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Navegação entre secções
            const helpLinks = document.querySelectorAll('.help-link');
            const helpSections = document.querySelectorAll('.help-section');
            
            helpLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    
                    // Remove active class from all links and hide all sections
                    helpLinks.forEach(l => l.classList.remove('active'));
                    helpSections.forEach(s => s.style.display = 'none');
                    
                    // Add active class to clicked link and show target section
                    this.classList.add('active');
                    document.getElementById(targetId).style.display = 'block';
                    
                    // Scroll to top of content
                    document.querySelector('.main-content').scrollTop = 0;
                });
            });
        });
        
        function toggleFaq(element) {
            const faqItem = element.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Close all FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Toggle current item
            if (!isActive) {
                faqItem.classList.add('active');
            }
        }
    </script>
</body>
</html>