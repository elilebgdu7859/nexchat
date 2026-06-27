<?php
require __DIR__ . '/bootstrap.php';
$user = current_user();
$data = input();
$prompt = trim($data['prompt'] ?? '');
if ($prompt === '') json_out(['error' => 'missing_prompt'], 422);

global $config;
if ($config['groq_api_key'] === '') json_out(['reply' => "Je suis NexIA. Ajoute ta clé GROQ_API_KEY côté serveur pour activer mes réponses intelligentes."]);

$context = '';
$conversationId = (int)($data['conversation_id'] ?? 0);
if ($conversationId > 0) {
    $member = db()->prepare('SELECT 1 FROM conversation_members WHERE conversation_id = ? AND user_id = ?');
    $member->execute([$conversationId, $user['id']]);
    if (!$member->fetchColumn()) json_out(['error' => 'forbidden_conversation'], 403);

    $stmt = db()->prepare('SELECT u.display_name, m.body FROM messages m JOIN users u ON u.id=m.sender_id WHERE m.conversation_id=? ORDER BY m.id DESC LIMIT 20');
    $stmt->execute([$conversationId]);
    $rows = array_reverse($stmt->fetchAll());
    $context = implode("\n", array_map(fn($m) => $m['display_name'] . ': ' . $m['body'], $rows));
}

$system = "Tu es NexIA, l'assistant essentiel de NexChat: résume les conversations, propose des réponses courtes, traduit, détecte les urgences, reste bienveillant et respecte la confidentialité.";
$userPrompt = $context ? "Conversation récente:\n$context\n\nDemande utilisateur:\n$prompt" : $prompt;
$payload = json_encode(['model' => $config['groq_model'], 'messages' => [['role'=>'system','content'=>$system], ['role'=>'user','content'=>$userPrompt]], 'temperature' => 0.7]);
$ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $config['groq_api_key']], CURLOPT_POSTFIELDS => $payload, CURLOPT_TIMEOUT => 20]);
$response = curl_exec($ch);
if ($response === false) json_out(['error' => 'groq_unreachable'], 502);
$json = json_decode($response, true);
$reply = $json['choices'][0]['message']['content'] ?? 'Réponse IA indisponible.';
json_out(['reply' => $reply]);
