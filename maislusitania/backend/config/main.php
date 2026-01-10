<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'name' => '+Lusitânia',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'on beforeRequest' => function ($event) {
        if (strpos(Yii::$app->request->getPathInfo(), 'api/') === 0) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
    },
    'modules' => [
        'api' => [
            'class' => 'backend\modules\api\ModuleAPI',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'pattern' => 'api/<local_id:\d+>/tipo-bilhete',
                    'route' => 'api/tipo-bilhete/index',
                    'defaults' => ['local_id' => null],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/local-cultural', 
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET distrito/{nome}' => 'distrito', // Permite filtrar por distrito
                        'GET tipo-local/{nome}' => 'tipo-local', // Permite filtrar por tipo de local
                        'GET search/{nome}' => 'search', // Permite pesquisa por nome
                        'GET {id}/avaliacoes' => 'avaliacoes', // Permite obter avaliações de um local
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>', // id numérico
                        '{nome}' => '<nome:[a-zA-Z0-9\\-\s]+>', // nome alfanumérico com hífens e espaços
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/noticia', // Necessário ter Autenticação
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET tipo-local/{nome}' => 'tipo-local', // Permite filtrar por tipo de local
                        'GET search/{nome}' => 'search', // Permite pesquisa por nome
                        'GET data/{data}' => 'data', // Permite filtrar por data
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>', // id numérico
                        '{nome}' => '<nome:[a-zA-Z0-9\\-\s]+>', // nome alfanumérico com hífens e espaços
                        '{data}' => '<data:\\d{4}-\\d{2}-\\d{2}>', // data no formato YYYY-MM-DD
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/evento', // Necessário ter Autenticação
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET tipo-local/{nome}' => 'tipo-local', // Permite filtrar por tipo de local
                        'GET search/{nome}' => 'search', // Permite pesquisa por nome
                        'GET data/{data}' => 'data', // Permite filtrar por data
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{nome}' => '<nome:[a-zA-Z0-9\\-\s]+>',
                        '{data}' => '<data:\\d{4}-\\d{2}-\\d{2}>', // data no formato YYYY-MM-DD
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/favorito', 
                    'pluralize' => true,
                    'extraPatterns' => [
                        'POST toggle/{localid}' => 'toggle', // Permite filtrar por distrito
                        'POST add/{localid}' => 'add', // Permite filtrar por distrito
                        'DELETE remove/{localid}' => 'remove', // Permite filtrar por distrito
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>', // id numérico
                        '{localid}' => '<localid:\\d+>', // id numérico
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/reserva', 
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET search/{nome}' => 'search', // Permite pesquisa por nome
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>', // id numérico
                        '{nome}' => '<nome:[a-zA-Z0-9\\-\s]+>', // nome alfanumérico com hífens e espaços
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/mapa', 
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET search/{nome}' => 'search', // Permite pesquisa por nome
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>', // id numérico
                        '{nome}' => '<nome:[a-zA-Z0-9\\-\s]+>', // nome alfanumérico com hífens e espaços
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/avaliacao',
                    'pluralize' => true,
                    'extraPatterns' => [
                        'POST add/{localid}' => 'add', // Adiciona uma avaliação a um local
                        'PUT edit/{id}' => 'edit', // Edita uma avaliação existente
                        'DELETE remove/{id}' => 'remove', // Remove uma avaliação existente
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>', // id numérico
                        '{localid}' => '<localid:\\d+>', // id numérico
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/user-profile',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET me' => 'me', // Obtém o perfil do usuário autenticado
                        'PUT update-profile' => 'update-profile', // Atualiza o perfil do utilizador
                        'PUT change-password' => 'change-password', // Altera a passe do utilizador
                        'DELETE delete-account' => 'delete-account', // Exclui a conta do utilizador
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>', // id numérico
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                    'api/linha-reserva',
                    'api/login-form', //Funciona
                    'api/signup-form', //Funciona 
                    'api/tipo-bilhete',
                    ],
                    'pluralize' => true,
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{nome}' => '<nome:[a-zA-Z0-9\\-]+>',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];