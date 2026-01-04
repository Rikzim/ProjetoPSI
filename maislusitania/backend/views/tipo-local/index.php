<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel backend\models\TipoLocalSearch */

$this->title = 'Tipos de Locais';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tags mr-2"></i>
                        Gestão de Tipos de Locais
                    </h3>
                    <div class="card-tools">
                        <?= Html::a('<i class="fas fa-plus"></i> Criar Tipo de Local', ['create'], ['class' => 'btn btn-success btn-sm']) ?>
                    </div>
                </div>

                <!-- Filtros e Pesquisa -->
                <div class="card-body border-bottom">
                    <?php $form = \yii\widgets\ActiveForm::begin([
                        'action' => ['index'],
                        'method' => 'get',
                        'options' => [
                            'data-pjax' => true,
                            'id' => 'local-filter-form'
                        ],
                    ]); ?>
                    
                    <div class="row w-100">
                        <!-- Campo de Pesquisa Geral -->
                        <div class="col-md-4 mb-2">
                            <div class="input-group input-group-sm w-100">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                </div>
                                <?= Html::activeTextInput($searchModel, 'globalSearch', [
                                    'class' => 'form-control',
                                    'placeholder' => 'Pesquisar por nome ou morada...'
                                ]) ?>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="col-lg-2 col-md-3 mb-2">
                            <div class="btn-group w-100">
                                <?= Html::submitButton('<i class="fas fa-search"></i>', [
                                    'class' => 'btn btn-primary btn-sm',
                                    'title' => 'Pesquisar'
                                ]) ?>
                                <?= Html::a('<i class="fas fa-redo"></i>', ['index'], [
                                    'class' => 'btn btn-secondary btn-sm',
                                    'title' => 'Limpar Filtros'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php \yii\widgets\ActiveForm::end(); ?>
                </div>
                <div class="card-body p-0">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
                        'headerRowOptions' => ['class' => 'bg-light'],
                        'summaryOptions' => ['class' => 'text-muted small px-3 py-2'],
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => ['style' => 'width: 50px; text-align: center;'],
                                'contentOptions' => ['class' => 'text-center align-middle'],
                            ],

                            // Coluna de Imagem
                            [
                                'attribute' => 'icone',
                                'label' => 'Imagem',
                                'format' => 'raw',
                                'value' => function($model) {
                                    return Html::img($model->getImage(), [
                                        'style' => 'max-height: 40px; max-width: 40px; object-fit: contain;', 
                                        'class' => 'img-thumbnail shadow-sm'
                                    ]); 
                                },
                                'headerOptions' => ['style' => 'width: 100px; text-align: center;'],
                                'contentOptions' => ['class' => 'text-center align-middle'],
                            ],
                            // Coluna de Nome
                            [
                                'attribute' => 'nome',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    return Html::tag('strong', Html::encode($model->nome), ['class' => 'text-dark']);
                                },
                                'contentOptions' => ['class' => 'align-middle'],
                            ],

                            // Coluna de Descrição
                            [
                                'attribute' => 'descricao',
                                'format' => 'ntext',
                                'contentOptions' => ['class' => 'align-middle text-muted'],
                            ],

                            // Coluna de Ações
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Ações',
                                'template' => '{view} {update} {delete}',
                                'buttons' => [
                                    'view' => function ($url, $model, $key) {
                                        return Html::a(
                                            '<i class="fas fa-eye"></i>',
                                            ['view', 'id' => $model->id],
                                            [
                                                'title' => 'Ver Detalhes',
                                                'class' => 'btn btn-sm btn-info mr-1',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'update' => function ($url, $model, $key) {
                                        return Html::a(
                                            '<i class="fas fa-edit"></i>',
                                            ['update', 'id' => $model->id],
                                            [
                                                'title' => 'Editar',
                                                'class' => 'btn btn-sm btn-warning mr-1',
                                                'data-pjax' => '0',
                                            ]
                                        );
                                    },
                                    'delete' => function ($url, $model, $key) {
                                        return Html::a(
                                            '<i class="fas fa-trash"></i>',
                                            ['delete', 'id' => $model->id],
                                            [
                                                'title' => 'Eliminar',
                                                'class' => 'btn btn-sm btn-danger',
                                                'data' => [
                                                    'confirm' => 'Tem a certeza que deseja eliminar este tipo de local?. OBSERVAÇÃO: Todos os locais culturais associados a este tipo de local serão também eliminados.',
                                                    'method' => 'post',
                                                ],
                                            ]
                                        );
                                    },
                                ],
                                'headerOptions' => ['style' => 'width: 180px; text-align: center;'],
                                'contentOptions' => ['class' => 'text-center align-middle'],
                            ],
                        ],
                        'pager' => [
                            'class' => 'yii\bootstrap4\LinkPager',
                            'options' => ['class' => 'pagination pagination-sm justify-content-center mb-0'],
                        ]
                    ]); ?>
                </div>
                <!--.card-body-->
            </div>
            <!--.card-->
        </div>
        <!--.col-md-12-->
    </div>
    <!--.row-->
</div>