<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<nav class="custom-navbar">
    <div class="navbar-container">
        <!-- Logo/Brand -->
        <?= Html::a(
                Html::img($logoUrl, ['alt' => 'Logo']),
                ['/site/index'],
                ['class' => 'navbar-brand']
        ) ?>

        <!-- Mobile Toggle -->
        <button class="mobile-toggle" id="mobile-toggle-btn">☰</button>

        <!-- Menu Items -->
        <ul class="navbar-menu" id="navbar-menu">
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <?= Html::a($item['label'], $item['url']) ?>
                </li>
            <?php endforeach; ?>

            <!-- Auth buttons in mobile menu -->
            <?php if ($showAuthButtons): ?>
                <li class="mobile-auth">
                    <?php if ($isGuest): ?>
                        <?= Html::a($signupLabel, ['/site/signup'], ['class' => 'btn-signup']) ?>
                        <?= Html::a($loginLabel, ['/site/login'], ['class' => 'btn-login']) ?>
                    <?php else: ?>
                        <!-- User info in hamburger menu -->
                        <div class="mobile-user-section">
                            <div class="mobile-user-header">
                                <div class="mobile-user-avatar">
                                    <img src="<?= Html::encode($userImage) ?>" alt="User Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                </div>
                                <div class="mobile-user-name"><?= Html::encode($username) ?></div>
                            </div>
                            <a href="<?= Url::to(['/profile/me']) ?>" class="mobile-menu-link">Perfil</a>
                            <a href="<?= Url::to(['/favorito/index']) ?>" class="mobile-menu-link">Favoritos</a>
                            <a href="<?= Url::to(['/reserva/index']) ?>" class="mobile-menu-link">Meus Bilhetes</a>
                            <?= Html::beginForm(['/site/logout'], 'post', ['style' => 'margin: 0']) ?>
                            <button type="submit" class="mobile-menu-link logout">Logout</button>
                            <?= Html::endForm() ?>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endif; ?>

        </ul>

        <!-- Auth Buttons / User Info (Desktop) -->
        <?php if ($showAuthButtons): ?>
            <div class="navbar-auth">
                <?php if ($isGuest): ?>
                    <?= Html::a($signupLabel, ['/site/signup'], ['class' => 'btn-signup']) ?>
                    <?= Html::a($loginLabel, ['/site/login'], ['class' => 'btn-login']) ?>
                <?php else: ?>
                    <div class="user-info" id="user-dropdown-toggle" onclick="toggleUserDropdown(event)">
                        <div class="user-avatar" title="<?= Html::encode($username) ?>">
                            <?php if ($userImage): ?>
                                <img src="<?= Html::encode($userImage) ?>" alt="User Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <?= strtoupper(substr($username, 0, 1)) ?>
                            <?php endif; ?>
                        </div>

                        <!-- Dropdown Menu -->
                        <div class="user-dropdown" id="user-dropdown-menu" onclick="event.stopPropagation()">
                            <div class="user-dropdown-header">
                                <div class="user-dropdown-avatar">
                                    <?php if ($userImage): ?>
                                        <img src="<?= Html::encode($userImage) ?>" alt="User Image" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                    <?php else: ?>
                                        <?= strtoupper(substr($username, 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="user-dropdown-info">
                                    <strong><?= Html::encode($username) ?></strong>
                                    <small>Utilizador</small>
                                </div>
                            </div>
                            
                            <ul class="user-dropdown-menu">
                                <li>
                                    <a href="<?= Url::to(['/profile/me']) ?>" class="user-dropdown-item">
                                        <img src="<?= Url::to('@web/images/icons/blue/icon-profile.svg') ?>" alt="">
                                        Perfil
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= Url::to(['/favorito/index']) ?>" class="user-dropdown-item">
                                        <img src="<?= Url::to('@web/images/icons/blue/icon-favorite.svg') ?>" alt="">
                                        Favoritos
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= Url::to(['/reserva/index']) ?>" class="user-dropdown-item">
                                        <img src="<?= Url::to('@web/images/icons/blue/icon-ticket.svg') ?>" alt="">
                                        Meus Bilhetes
                                    </a>
                                </li>
                            </ul>
                            
                            <div class="user-dropdown-divider"></div>
                            
                            <?= Html::beginForm(['/site/logout'], 'post', ['style' => 'margin-bottom: 10px']) ?>
                                <button type="submit" class="user-dropdown-item logout-item">
                                    <img src="<?= Url::to('@web/images/icons/icon-logout.svg') ?>" alt="">
                                    Logout
                                </button>
                            <?= Html::endForm() ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('mobile-toggle-btn');
        const menu = document.getElementById('navbar-menu');

        if (toggleBtn && menu) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('active');
                toggleBtn.textContent = menu.classList.contains('active') ? '✕' : '☰';
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if (!menu.contains(e.target) && !toggleBtn.contains(e.target)) {
                    menu.classList.remove('active');
                    toggleBtn.textContent = '☰';
                }
            });

            // Close menu when clicking a link
            menu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    menu.classList.remove('active');
                    toggleBtn.textContent = '☰';
                });
            });
        }
    });

    // User dropdown toggle
    function toggleUserDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('user-dropdown-menu');
        dropdown.classList.toggle('show');
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('user-dropdown-menu');
        const toggle = document.getElementById('user-dropdown-toggle');

        if (dropdown && toggle && !toggle.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
</script>
