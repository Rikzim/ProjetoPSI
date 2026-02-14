<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Dashboard';
$this->params['breadcrumbs'] = [['label' => $this->title]];
?>
<div class="container-fluid">
    <!-- Info Boxes Row -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Locais Culturais',
                'number' => $locaisCount,
                'icon' => 'fas fa-landmark',
                'theme' => 'info',
            ]) ?>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Eventos Ativos',
                'number' => $eventosCount,
                'icon' => 'fas fa-calendar-alt',
                'theme' => 'success',
            ]) ?>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Utilizadores',
                'number' => $usersCount,
                'icon' => 'fas fa-users',
                'theme' => 'warning',
            ]) ?>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <?= \hail812\adminlte\widgets\InfoBox::widget([
                'text' => 'Reservas (Mês)',
                'number' => $reservasMesCount,
                'icon' => 'fas fa-ticket-alt',
                'theme' => 'danger',
            ]) ?>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <!-- Latest Reservations -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-check"></i> Últimas Reservas</h3>
                    <div class="card-tools">
                        <a href="<?= Url::to(['reserva/index']) ?>" class="btn btn-tool btn-sm">
                            <i class="fas fa-eye"></i> Ver Todas
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Reservas</th>
                                <th>Utilizador</th>
                                <th>Data</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimasReservas as $reserva): ?>
                                <tr>
                                    <td><?= Html::encode($reserva->local->nome ?? '-') ?></td>
                                    <td><?= Html::encode($reserva->utilizador->username ?? '-') ?></td>
                                    <td><?= Yii::$app->formatter->asDate($reserva->data_visita) ?></td>
                                    <td>
                                        <?php
                                            $status = $reserva->estado ?? '';
                                            $badge = 'badge-secondary';
                                            if ($status == 'Confirmada') $badge = 'badge-success';
                                            elseif ($status == 'Pendente') $badge = 'badge-warning';
                                            elseif ($status == 'Cancelada') $badge = 'badge-danger';
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= Html::encode($status) ?></span>
                                    </td>
                                    <td>
                                        <?= Html::a('<i class="fas fa-eye"></i>', ['reserva/view', 'id' => $reserva->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Ver Reserva']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Latest Reviews -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star"></i> Últimas Avaliações</h3>
                    <div class="card-tools">
                        <a href="<?= Url::to(['avaliacao/index']) ?>" class="btn btn-tool btn-sm">
                            <i class="fas fa-eye"></i> Ver Todas
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Local</th>
                                <th>Utilizador</th>
                                <th>Rating</th>
                                <th>Data</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimasAvaliacoes as $avaliacao): ?>
                                <tr>
                                    <td><?= Html::encode($avaliacao->local->nome ?? '-') ?></td>
                                    <td><?= Html::encode($avaliacao->utilizador->username ?? '-') ?></td>
                                    <td>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= ($avaliacao->classificacao ?? 0)): ?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php else: ?>
                                                <i class="far fa-star text-warning"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </td>
                                    <td><?= Yii::$app->formatter->asDate($avaliacao->data_avaliacao) ?></td>
                                    <td>
                                        <?= Html::a('<i class="fas fa-eye"></i>', ['avaliacao/view', 'id' => $avaliacao->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Ver Avaliação']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Vendas - Últimos 6 Meses</h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (array_sum($salesData) == 0): ?>
    <div class="alert alert-info mt-3 text-center">
        Não existem vendas registadas nos últimos 6 meses.
    </div>
<?php else: ?>
<?php
$this->registerJs("
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: " . json_encode($salesLabels) . ",
            datasets: [{
                label: 'Lucro (€)',
                data: " . json_encode($salesData) . ",
                backgroundColor: '#5cb85c',
                borderColor: '#4cae4c',
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
                        callback: function(value) {
                            return '€' + value;
                        }
                    }
                }
            }
        }
    });
");
?>
<?php endif; ?>