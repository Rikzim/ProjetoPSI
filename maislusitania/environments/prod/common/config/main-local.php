<?php

$dbUrl = getenv('DATABASE_URL');
if ($dbUrl) {
    // Parse DATABASE_URL if provided (e.g., from Render)
    $url = parse_url($dbUrl);
    $driver = isset($url['scheme']) ? ($url['scheme'] === 'postgres' ? 'pgsql' : $url['scheme']) : 'mysql';
    $host = isset($url['host']) ? $url['host'] : 'localhost';
    $port = isset($url['port']) ? ';' . 'port=' . $url['port'] : '';
    $dbName = isset($url['path']) ? substr($url['path'], 1) : 'yii2advanced';
    $dsn = "{$driver}:host={$host}{$port};dbname={$dbName}";
    $username = isset($url['user']) ? $url['user'] : 'root';
    $password = isset($url['pass']) ? $url['pass'] : '';
} else {
    // Fallback to individual variables or defaults
    $dsn = getenv('DB_DSN') ?: 'mysql:host=localhost;dbname=yii2advanced';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
}

return [
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => $dsn,
            'username' => $username,
            'password' => $password,
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
        ],
    ],
];
