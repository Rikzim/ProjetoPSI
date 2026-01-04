<?php
/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProviderAtivas */
/** @var yii\data\ActiveDataProvider $dataProviderExpiradas */

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\LinkPager;

$this->title = 'Meus Bilhetes';
$this->registerCssFile('@web/css/reservas/index.css');
?>

<div class="my-tickets-page">
    <!-- Hero Section -->
    <div class="tickets-hero">
        <div class="hero-content">
            <h1 class="hero-title">Meus Bilhetes</h1>
            <p class="hero-subtitle">Gerencie todos os seus bilhetes de museus e monumentos</p>
        </div>
    </div>

    <div class="tickets-container">
        <?php if ($dataProviderAtivas->totalCount == 0 && $dataProviderExpiradas->totalCount == 0): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <img src="<?= Yii::getAlias('@web/images/icons/blue/icon-ticket.svg') ?>" alt="Ticket Icon">
                </div>
                <h2>Ainda não tem bilhetes</h2>
                <p>Explore nossos museus e monumentos e comece sua jornada cultural!</p>
                <?= Html::a('Explorar Locais', ['/mapa/index'], ['class' => 'btn-explore']) ?>
            </div>
        <?php else: ?>
            
            <!-- Reservas Ativas -->
            <?php if ($dataProviderAtivas->totalCount > 0): ?>
                <div class="section-header">
                    <h2 class="section-title">Bilhetes Ativos</h2>
                    <p class="section-subtitle">Seus próximos bilhetes válidos para visitas</p>
                </div>
                
                <?= ListView::widget([
                    'dataProvider' => $dataProviderAtivas,
                    'itemView' => '_reserva_item',
                    'itemOptions' => ['class' => 'tickets-grid-item'],
                    'options' => ['class' => 'tickets-grid'],
                    'layout' => "{items}\n<div class='pagination-wrapper'>{pager}</div>",
                    'pager' => [
                        'class' => LinkPager::class,
                        'options' => ['class' => 'pagination'],
                        'linkOptions' => ['class' => 'page-link'],
                        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
                    ],
                    'emptyText' => '',
                ]) ?>
            <?php endif; ?>

            <!-- Reservas Expiradas -->
            <?php if ($dataProviderExpiradas->totalCount > 0): ?>
                <div class="section-header expired-section">
                    <h2 class="section-title">Bilhetes Expirados</h2>
                    <p class="section-subtitle">Histórico de bilhetes com data de visita ultrapassada</p>
                </div>
                
                <?= ListView::widget([
                    'dataProvider' => $dataProviderExpiradas,
                    'itemView' => '_reserva_item',
                    'itemOptions' => ['class' => 'tickets-grid-item'],
                    'options' => ['class' => 'tickets-grid'],
                    'layout' => "{items}\n<div class='pagination-wrapper'>{pager}</div>",
                    'pager' => [
                        'class' => LinkPager::class,
                        'options' => ['class' => 'pagination'],
                        'linkOptions' => ['class' => 'page-link'],
                        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
                    ],
                    'emptyText' => '',
                ]) ?>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>