<?php
require_once __DIR__ . '/functions.php';
require_login();

$token = $_SESSION['token'];
$user = $_SESSION['user'] ?? ['name' => 'User'];
$flash = get_flash();

$tasksResponse = api_request('GET', '/tasks', null, $token);
$tasks = $tasksResponse['data']['data'] ?? [];

$logsResponse = api_request('GET', '/logs', null, $token);
$logs = $logsResponse['data']['data'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <div class="topbar">
            <div>
                <h1>Task Manager</h1>
                <p class="muted">Halo, <?= htmlspecialchars($user['name'] ?? 'User') ?> 👋</p>
            </div>
            <a href="logout.php" class="btn-secondary">Logout</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert <?= $flash['type'] === 'success' ? 'alert-success' : 'alert-danger' ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <div class="grid-two">
            <div class="card">
                <h2>Tambah Task</h2>
                <form method="POST" action="save_task.php" class="form-grid">
                    <input type="hidden" name="mode" value="create">
                    <div>
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div>
                        <label>Description</label>
                        <textarea name="description" rows="4"></textarea>
                    </div>
                    <div>
                        <label>Status</label>
                        <select name="status" required>
                            <option value="todo">todo</option>
                            <option value="in-progress">in-progress</option>
                            <option value="done">done</option>
                        </select>
                    </div>
                    <div>
                        <label>Due date</label>
                        <input type="date" name="due_date">
                    </div>
                    <button type="submit">Simpan Task</button>
                </form>
            </div>

            <div class="card">
                <h2>Log Aktivitas</h2>
                <?php if (!$logs): ?>
                    <p class="muted">Belum ada log.</p>
                <?php else: ?>
                    <ul class="log-list">
                        <?php foreach ($logs as $log): ?>
                            <li>
                                <strong><?= htmlspecialchars($log['action']) ?></strong>
                                <span><?= htmlspecialchars($log['description'] ?? '-') ?></span>
                                <small><?= htmlspecialchars($log['created_at']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mt-20">
            <h2>Daftar Task</h2>
            <?php if (!$tasks): ?>
                <p class="muted">Belum ada task. Tambahin dulu ya.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?= htmlspecialchars($task['title']) ?></td>
                                    <td><?= htmlspecialchars($task['description'] ?: '-') ?></td>
                                    <td><span class="badge badge-<?= htmlspecialchars($task['status']) ?>"><?= htmlspecialchars($task['status']) ?></span></td>
                                    <td><?= htmlspecialchars($task['due_date'] ?: '-') ?></td>
                                    <td class="actions">
                                        <a href="edit.php?id=<?= (int) $task['id'] ?>">Edit</a>
                                        <form action="delete_task.php" method="POST" onsubmit="return confirm('Yakin mau hapus task ini?');">
                                            <input type="hidden" name="id" value="<?= (int) $task['id'] ?>">
                                            <button type="submit" class="btn-link danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
