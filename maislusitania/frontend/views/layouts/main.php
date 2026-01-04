<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use frontend\widgets\CustomNavBar;
use frontend\widgets\CustomFooter;

AppAsset::register($this);

//Carregar favicon.ico
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web') . '/favicon.ico']);

// Definir ações que não devem mostrar header/footer
$currentAction = Yii::$app->controller->action->id;
$authActions = ['login', 'signup', 'request-password-reset', 'reset-password', 'resend-verification-email'];
$showHeaderFooter = !in_array($currentAction, $authActions);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        body {
            padding-top: 100px; /* Altura da navbar + margem */
        }
        
        @media (max-width: 992px) {
            body {
                padding-top: 90px; /* Ajuste para mobile */
            }
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<?php if ($showHeaderFooter): ?>
<header>
    <?= CustomNavBar::widget([
        'logoUrl' => '@web/images/logo/logo.svg',
    ]) ?>
</header>
<?php endif; ?>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?php if ($showHeaderFooter): ?>
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
        <?php endif; ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<?php if ($showHeaderFooter): ?>
<footer>
    <?= CustomFooter::widget([
        'logoUrl' => '@web/images/logo/logo.svg',
    ]) ?>
</footer>
<?php endif; ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();