<?php
require __DIR__ . '/bootstrap.php';
$user = current_user();
$data = input();
if (empty($data['peer_id'])) json_out(['error' => 'missing_peer_id'], 422);
$stmt = db()->prepare('UPDATE users SET peer_id=? WHERE id=?');
$stmt->execute([preg_replace('/[^a-zA-Z0-9_-]/', '', $data['peer_id']), $user['id']]);
json_out(['ok' => true, 'peer_id' => $data['peer_id'], 'secure_host' => 'nexchat.alwaysdata.net']);
