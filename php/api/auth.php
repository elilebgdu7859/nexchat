<?php
require __DIR__ . '/bootstrap.php';
$data = input();
$action = $_GET['action'] ?? $data['action'] ?? 'login';

if ($action === 'register') {
    foreach (['username','display_name','email','password'] as $field) {
        if (empty($data[$field])) json_out(['error' => "missing_$field"], 422);
    }
    $stmt = db()->prepare('INSERT INTO users (username, display_name, email, password_hash, avatar_color) VALUES (?, ?, ?, ?, ?)');
    $color = sprintf('#%06X', random_int(0x4455AA, 0xFFFFFF));
    $stmt->execute([
        strtolower(trim($data['username'])), trim($data['display_name']), strtolower(trim($data['email'])),
        password_hash($data['password'], PASSWORD_DEFAULT), $color
    ]);
    $user = ['id' => db()->lastInsertId()] + $data;
    json_out(['token' => token_for($user), 'user' => ['id' => (int)$user['id'], 'username' => strtolower($data['username']), 'display_name' => $data['display_name'], 'email' => strtolower($data['email']), 'avatar_color' => $color]]);
}

if ($action === 'me') json_out(['user' => current_user()]);

$stmt = db()->prepare('SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1');
$stmt->execute([strtolower($data['login'] ?? ''), strtolower($data['login'] ?? '')]);
$user = $stmt->fetch();
if (!$user || !password_verify($data['password'] ?? '', $user['password_hash'])) json_out(['error' => 'invalid_credentials'], 401);
json_out(['token' => token_for($user), 'user' => ['id' => (int)$user['id'], 'username' => $user['username'], 'display_name' => $user['display_name'], 'email' => $user['email'], 'avatar_color' => $user['avatar_color'], 'ai_persona' => $user['ai_persona']]]);
