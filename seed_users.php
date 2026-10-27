
<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/bootstrap.php';

use Cake\ORM\TableRegistry;

$usersTable = TableRegistry::getTableLocator()->get('Users');

$users = [
    ['username' => 'じゃすみん',   'password' => 'jasmine123', 'role' => 'user'],
    ['username' => 'ちゃちゃまる', 'password' => 'chacha123',  'role' => 'user'],
    ['username' => 'しずえ',       'password' => 'shizue123', 'role' => 'admin'],
    ['username' => 'たぬきち',     'password' => 'tanuki123', 'role' => 'admin'],
    ['username' => 'ジュン',       'password' => 'jun12345',  'role' => 'user'],
    ['username' => 'みすず',       'password' => 'misuzu123', 'role' => 'user'],
    ['username' => 'ビアンカ',     'password' => 'bianca123', 'role' => 'user'],
    ['username' => 'ラムネ',       'password' => 'ramune123', 'role' => 'user'],
    ['username' => 'リリアン',     'password' => 'lilian123', 'role' => 'user'],
    ['username' => 'ジャック',     'password' => 'jack12345', 'role' => 'user'],
];

foreach ($users as $data) {
    $existing = $usersTable->find()
        ->where(['username' => $data['username']])
        ->first();

    if ($existing) {
        echo "SKIP: {$data['username']} (already exists)\n";
        continue;
    }

    $user = $usersTable->newEntity($data);

    if ($usersTable->save($user)) {
        echo "OK: {$data['username']}\n";
    } else {
        echo "ERROR: {$data['username']}\n";
        print_r($user->getErrors());
    }
}
