<?php
require __DIR__ . '/bootstrap.php';
$user = current_user();
$action = $_GET['action'] ?? 'list';

function assert_member(int $conversationId, int $userId): void {
    $stmt = db()->prepare('SELECT 1 FROM conversation_members WHERE conversation_id = ? AND user_id = ?');
    $stmt->execute([$conversationId, $userId]);
    if (!$stmt->fetchColumn()) json_out(['error' => 'forbidden_conversation'], 403);
}

if ($action === 'contacts') {
    $q = '%' . ($_GET['q'] ?? '') . '%';
    $stmt = db()->prepare('SELECT id, username, display_name, avatar_color, peer_id FROM users WHERE id <> ? AND (username LIKE ? OR display_name LIKE ?) ORDER BY display_name LIMIT 30');
    $stmt->execute([$user['id'], $q, $q]);
    json_out(['contacts' => $stmt->fetchAll()]);
}

if ($action === 'start') {
    $data = input();
    $other = (int)($data['user_id'] ?? 0);
    if ($other < 1 || $other === (int)$user['id']) json_out(['error' => 'invalid_user'], 422);

    $existing = db()->prepare('SELECT cm1.conversation_id FROM conversation_members cm1 JOIN conversation_members cm2 ON cm2.conversation_id = cm1.conversation_id WHERE cm1.user_id = ? AND cm2.user_id = ? GROUP BY cm1.conversation_id HAVING COUNT(*) = 1 LIMIT 1');
    $existing->execute([$user['id'], $other]);
    $existingId = $existing->fetchColumn();
    if ($existingId) json_out(['conversation_id' => (int)$existingId]);

    $pdo = db();
    $pdo->beginTransaction();
    $pdo->prepare('INSERT INTO conversations (created_by) VALUES (?)')->execute([$user['id']]);
    $cid = (int)$pdo->lastInsertId();
    $pdo->prepare('INSERT INTO conversation_members (conversation_id, user_id, role) VALUES (?, ?, ?), (?, ?, ?)')
        ->execute([$cid, $user['id'], 'owner', $cid, $other, 'member']);
    $pdo->commit();
    json_out(['conversation_id' => $cid]);
}

if ($action === 'send') {
    $data = input();
    $conversationId = (int)($data['conversation_id'] ?? 0);
    assert_member($conversationId, (int)$user['id']);
    $body = trim($data['body'] ?? '');
    if ($body === '') json_out(['error' => 'empty_message'], 422);
    $stmt = db()->prepare('INSERT INTO messages (conversation_id, sender_id, body, message_type, client_id) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$conversationId, $user['id'], $body, $data['message_type'] ?? 'text', $data['client_id'] ?? null]);
    json_out(['id' => (int)db()->lastInsertId(), 'created_at' => date(DATE_ATOM)]);
}

$stmt = db()->prepare('SELECT c.id, COALESCE(c.title, GROUP_CONCAT(u.display_name SEPARATOR ", ")) title, MAX(m.created_at) last_message_at FROM conversations c JOIN conversation_members cm ON cm.conversation_id=c.id JOIN conversation_members mine ON mine.conversation_id=c.id AND mine.user_id=? JOIN users u ON u.id=cm.user_id AND u.id<>? LEFT JOIN messages m ON m.conversation_id=c.id GROUP BY c.id ORDER BY last_message_at DESC, c.created_at DESC');
$stmt->execute([$user['id'], $user['id']]);
$conversations = $stmt->fetchAll();
$messages = [];
if (!empty($_GET['conversation_id'])) {
    $conversationId = (int)$_GET['conversation_id'];
    assert_member($conversationId, (int)$user['id']);
    $stmt = db()->prepare('SELECT m.id, m.body, m.message_type, m.created_at, u.id sender_id, u.display_name sender_name, u.avatar_color FROM messages m JOIN users u ON u.id=m.sender_id WHERE m.conversation_id=? ORDER BY m.id DESC LIMIT 100');
    $stmt->execute([$conversationId]);
    $messages = array_reverse($stmt->fetchAll());
}
json_out(['conversations' => $conversations, 'messages' => $messages]);
