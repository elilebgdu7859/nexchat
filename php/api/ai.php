<?php
require __DIR__ . '/bootstrap.php';
$user = current_user();
$data = input();
$prompt = trim($data['prompt'] ?? '');
if ($prompt === '') json_out(['error' => 'missing_prompt'], 422);

global $config;
if ($config['groq_api_key'] === '') json_out(['reply' => "Je suis NexIA. Ajoute ta clé GROQ_API_KEY côté serveur pour activer mes réponses intelligentes."]);

$system = "Tu es NexIA, l'assistant essentiel de NexChat: résume les conversations, propose des réponses courtes, traduit, détecte les urgences, reste bienveillant et respecte la confidentialité.";
$payload = json_encode(['model' => $config['groq_model'], 'messages' => [['role'=>'system','content'=>$system], ['role'=>'user','content'=>$prompt]], 'temperature' => 0.7]);
$ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $config['groq_api_key']], CURLOPT_POSTFIELDS => $payload, CURLOPT_TIMEOUT => 20]);
$response = curl_exec($ch);
if ($response === false) json_out(['error' => 'groq_unreachable'], 502);
$json = json_decode($response, true);
json_out(['reply' => $json['choices'][0]['message']['content'] ?? 'Réponse IA indisponible.']);
