<?php
// includes/sidebar.php - Menu lateral
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

$menuItems = [
    [
        'id' => 'dashboard',
        'title' => 'Dashboard',
        'url' => 'dashboard.php',
        'icon' => '<path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>'
    ],
    [
        'id' => 'users',
        'title' => 'Utilizadores',
        'url' => 'users.php',
        'icon' => '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>'
    ],
    [
        'id' => 'logs',
        'title' => 'Logs',
        'url' => 'logs.php',
        'icon' => '<path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"></path>'
    ],
    [
        'id' => 'settings',
        'title' => 'Configurações',
        'url' => 'settings.php',
        'icon' => '<path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>'
    ],
    [
        'id' => 'help',
        'title' => 'Ajuda',
        'url' => 'help.php',
        'icon' => '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>'
    ]
];
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-content">
        <?php foreach ($menuItems as $item): ?>
            <a href="<?= $item['url'] ?>" class="sidebar-item <?= $currentPage === $item['id'] ? 'active' : '' ?>"
                title="<?= $item['title'] ?>">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <?= $item['icon'] ?>
                </svg>
                <span class="sidebar-text"><?= $item['title'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-item" title="Versão <?= SYSTEM_VERSION ?>">
            <svg fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            <span class="sidebar-text">v<?= SYSTEM_VERSION ?></span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Highlight do menu ativo
        const currentPage = '<?= $currentPage ?>';
        const menuItems = document.querySelectorAll('.sidebar-item');

        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && href.includes(currentPage)) {
                item.classList.add('active');
            }
        });

        // Fechar menu mobile ao clicar em item
        menuItems.forEach(item => {
            item.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    document.querySelector('.sidebar').classList.remove('mobile-open');
                }
            });
        });

        // Fechar menu ao clicar fora (mobile)
        document.addEventListener('click', function (e) {
            const sidebar = document.querySelector('.sidebar');
            const menuToggle = document.getElementById('menuToggle');

            if (window.innerWidth <= 768 &&
                !sidebar.contains(e.target) &&
                !menuToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    });
</script>