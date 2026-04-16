<?php
require_once __DIR__ . '/functions.php';
require_login();

$token = $_SESSION['token'];
$mode = $_POST['mode'] ?? 'create';
$id = (int) ($_POST['id'] ?? 0);

$payload = [
    'title' => trim($_POST['title'] ?? ''),
    'description' => trim($_POST['description'] ?? ''),
    'status' => trim($_POST['status'] ?? 'todo'),
    'due_date' => trim($_POST['due_date'] ?? ''),
];

if ($payload['due_date'] === '') {
    $payload['due_date'] = null;
}

if ($mode === 'update' && $id > 0) {
    $result = api_request('PUT', '/tasks/' . $id, $payload, $token);
    if ($result['status'] === 200) {
        set_flash('success', 'Task berhasil diupdate.');
    } else {
        $message = $result['data']['message'] ?? 'Gagal update task.';
        if (!empty($result['data']['errors'])) {
            $message .= ' ' . implode(' ', $result['data']['errors']);
        }
        set_flash('danger', $message);
    }
    redirect_to('dashboard.php');
}

$result = api_request('POST', '/tasks', $payload, $token);
if ($result['status'] === 201) {
    set_flash('success', 'Task berhasil ditambah.');
} else {
    $message = $result['data']['message'] ?? 'Gagal bikin task.';
    if (!empty($result['data']['errors'])) {
        $message .= ' ' . implode(' ', $result['data']['errors']);
    }
    set_flash('danger', $message);
}

redirect_to('dashboard.php');
