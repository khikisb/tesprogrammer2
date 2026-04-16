<?php
require_once __DIR__ . '/functions.php';
require_login();

$token = $_SESSION['token'];
$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    set_flash('danger', 'ID task tidak valid.');
    redirect_to('dashboard.php');
}

$result = api_request('DELETE', '/tasks/' . $id, null, $token);

if ($result['status'] === 200) {
    set_flash('success', 'Task berhasil dihapus.');
} else {
    set_flash('danger', $result['data']['message'] ?? 'Gagal hapus task.');
}

redirect_to('dashboard.php');
