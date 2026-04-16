<?php
require_once __DIR__ . '/functions.php';
require_login();

$token = $_SESSION['token'];
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    set_flash('danger', 'ID task tidak valid.');
    redirect_to('dashboard.php');
}

$response = api_request('GET', '/tasks/' . $id, null, $token);
$task = $response['data']['data'] ?? null;

if (!$task) {
    set_flash('danger', $response['data']['message'] ?? 'Task tidak ditemukan.');
    redirect_to('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container small-container">
        <div class="topbar">
            <div>
                <h1>Edit Task</h1>
                <p class="muted">Ubah data task di bawah ini.</p>
            </div>
            <a href="dashboard.php" class="btn-secondary">Kembali</a>
        </div>

        <div class="card">
            <form method="POST" action="save_task.php" class="form-grid">
                <input type="hidden" name="mode" value="update">
                <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">

                <div>
                    <label>Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
                </div>
                <div>
                    <label>Description</label>
                    <textarea name="description" rows="5"><?= htmlspecialchars($task['description']) ?></textarea>
                </div>
                <div>
                    <label>Status</label>
                    <select name="status" required>
                        <option value="todo" <?= $task['status'] === 'todo' ? 'selected' : '' ?>>todo</option>
                        <option value="in-progress" <?= $task['status'] === 'in-progress' ? 'selected' : '' ?>>in-progress</option>
                        <option value="done" <?= $task['status'] === 'done' ? 'selected' : '' ?>>done</option>
                    </select>
                </div>
                <div>
                    <label>Due date</label>
                    <input type="date" name="due_date" value="<?= htmlspecialchars($task['due_date']) ?>">
                </div>

                <button type="submit">Update Task</button>
            </form>
        </div>
    </div>
</body>
</html>
