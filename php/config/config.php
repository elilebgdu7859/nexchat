<?php
$envFiles = [
    dirname(__DIR__, 2) . '/.env',
    dirname(__DIR__) . '/.env',
];

foreach ($envFiles as $envFile) {
    if (!is_readable($envFile)) {
        continue;
    }

    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        if (getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

return [
    'db_host' => getenv('NEXCHAT_DB_HOST') ?: 'localhost',
    'db_name' => getenv('NEXCHAT_DB_NAME') ?: 'nexchat_index',
    'db_user' => getenv('NEXCHAT_DB_USER') ?: 'root',
    'db_pass' => getenv('NEXCHAT_DB_PASS') ?: '',
    'jwt_secret' => getenv('NEXCHAT_JWT_SECRET') ?: 'change-me-in-production',
    'groq_api_key' => getenv('GROQ_API_KEY') ?: '',
    'groq_model' => getenv('GROQ_MODEL') ?: 'llama-3.1-70b-versatile',
];
