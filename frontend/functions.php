<?php
require_once __DIR__ . '/config.php';

function api_request(string $method, string $path, array $body = null, string $token = null): array
{
    $url = API_BASE_URL . $path;
    $headers = ['Content-Type: application/json'];

    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return [
            'status' => 500,
            'data' => ['message' => 'Gagal konek ke backend: ' . $error]
        ];
    }

    $decoded = json_decode($response, true);

    return [
        'status' => $statusCode,
        'data' => $decoded ?: ['message' => 'Response backend kosong atau tidak valid.']
    ];
}

function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['token']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect_to('index.php');
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function old(string $key, string $default = ''): string
{
    return htmlspecialchars($_POST[$key] ?? $default);
}
