<?php
return [
    'db_host' => getenv('NEXCHAT_DB_HOST') ?: 'localhost',
    'db_name' => getenv('NEXCHAT_DB_NAME') ?: 'nexchat_index',
    'db_user' => getenv('NEXCHAT_DB_USER') ?: 'root',
    'db_pass' => getenv('NEXCHAT_DB_PASS') ?: '',
    'jwt_secret' => getenv('NEXCHAT_JWT_SECRET') ?: 'change-me-in-production',
    'groq_api_key' => getenv('GROQ_API_KEY') ?: '',
    'groq_model' => getenv('GROQ_MODEL') ?: 'llama-3.1-70b-versatile',
];
