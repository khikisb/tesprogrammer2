<?php
require_once __DIR__ . '/functions.php';

if (is_logged_in()) {
    redirect_to('dashboard.php');
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $result = api_request('POST', '/auth/login', [
        'email' => $email,
        'password' => $password,
    ]);

    if ($result['status'] === 200) {
        $_SESSION['token'] = $result['data']['token'] ?? null;
        $_SESSION['user'] = $result['data']['user'] ?? null;
        set_flash('success', 'Login berhasil. Yuk lanjut ngatur task.');
        redirect_to('dashboard.php');
    }

    $errorMessage = $result['data']['message'] ?? 'Login gagal.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <h1>Task Manager</h1>
        <p class="muted">Login dulu buat masuk ke dashboard.</p>

        <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <form method="POST" class="form-grid">
            <div>
                <label>Email</label>
                <input type="email" name="email" value="<?= old('email', 'admin@example.com') ?>" required>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" value="admin123" required>
            </div>
            <button type="submit">Login</button>
        </form>

        <div class="demo-box">
            <strong>Akun demo:</strong><br>
            admin@example.com / admin123
        </div>
    </div>
</body>
</html>
