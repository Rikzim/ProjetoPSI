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
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/local-cultural', 
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET distrito/{nome}' => 'distrito', // Permite filtrar por distrito
                        'GET tipo-local/{nome}' => 'tipo-local', // Permite filtrar por tipo de local
                        'GET search/{nome}' => 'search', // Permite pesquisa por nome
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{nome}' => '<nome:[a-zA-Z0-9\\-\s]+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/noticia', // Necessário ter Autenticação
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET tipo-local/{nome}' => 'tipo-local', // Permite filtrar por tipo de local
                        'GET search/{nome}' => 'search', // Permite pesquisa por nome
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{nome}' => '<nome:[a-zA-Z0-9\\-\s]+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/evento', // Necessário ter Autenticação
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET tipo-local/{nome}' => 'tipo-local', // Permite filtrar por tipo de local
                        'GET search/{nome}' => 'search', // Permite pesquisa por nome
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{nome}' => '<nome:[a-zA-Z0-9\\-\s]+>',
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
                        '{id}' => '<id:\\d+>',
                        '{localid}' => '<localid:\\d+>',
                    ],
                ],
                                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/reserva', 
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET search/{nome}' => 'search',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{nome}' => '<nome:[a-zA-Z0-9\\-\s]+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/mapa', 
                    'pluralize' => true,
                    'extraPatterns' => [
                        'GET search/{nome}' => 'search',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{nome}' => '<nome:[a-zA-Z0-9\\-]+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/avaliacao',
                    'pluralize' => true,
                    'extraPatterns' => [
                        'POST add/{localid}' => 'add',
                        'DELETE remove/{id}' => 'remove',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                        '{localid}' => '<localid:\\d+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/user-profile',
                    'pluralize' => false,
                    'extraPatterns' => [
                        'GET me' => 'me',
                        'PUT update-profile' => 'update-profile',
                        'PUT change-password' => 'change-password',
                        'DELETE delete-account' => 'delete-account',
                    ],
                    'tokens' => [
                        '{id}' => '<id:\\d+>',
                    ],
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                    'api/avaliacao',
                    'api/distrito',
                    'api/linha-reserva',
                    'api/login-form', //Funciona
                    'api/signup-form', //Funciona 
                    'api/tipo-bilhete',
                    'api/tipo-local',
                    'api/user',
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