<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To do list record</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
         font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #153677, #4e085f);
        }

        .history-card {
            max-width: 1000px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f8f9fa;
            padding-bottom: 20px;
        }

        .header-section h2 {
            color: #002765;
            font-weight: 600;
            margin: 0;
            font-size: 24px;
        }

        .btn-custom-back {
            background: #ff5945;
            color: #fff;
            padding: 10px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-custom-back:hover {
            background: #e04836;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 89, 69, 0.3);
        }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }

        .custom-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .custom-table thead {
            background-color: #f1f3f5;
        }

        .custom-table th {
            color: #495057;
            font-weight: 600;
            padding: 15px;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .custom-table tbody tr {
            border-bottom: 1px solid #edf2f7;
            transition: background 0.2s;
        }

        .custom-table tbody tr:hover {
            background-color: #fcfdfe;
        }

        .custom-table td {
            padding: 18px 15px;
            vertical-align: middle;
            text-align: center;
            color: #4a5568;
            font-size: 14px;
        }

        .id-cell {
            font-weight: 600;
            color: #ff5945;
        }

        .task-title {
            text-align: left !important;
            font-weight: 500;
            color: #2d3748;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .completed-status {
            background: #c6f6d5;
            color: #22543d;
        }

        .pending-status {
            background: #feebc8;
            color: #744210;
        }

        .date-text {
            color: #718096;
            white-space: nowrap;
        }

        .empty-box {
            text-align: center;
            padding: 60px 20px;
            color: #a0aec0;
        }
    </style>
</head>
<body>

<div class="history-card">
    <div class="header-section">
        <h2  class="m-0">To-Do List Records</h2>
        <a href="<?= base_url('/') ?>" class="btn-custom-back">&larr; Return to Dashboard</a>
    </div>

    <?php if (empty($all_tasks)): ?>
        <div class="empty-box">
            <img src="https://em-content.zobj.net/source/microsoft-teams/337/package.png" width="60" style="opacity: 0.5; margin-bottom: 15px;">
            <p>No activity logs found. All your past actions will appear here.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Task </th>
                        <th>Current Status</th>
                        <th>Created Date</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_tasks as $task): ?>
                        <tr>
                            <td class="id-cell">#<?= $task['id'] ?></td>
                            <td class="task-title"><?= esc($task['title']) ?></td>
                            <td>
                                <span class="status-badge <?= $task['status'] == 'completed' ? 'completed-status' : 'pending-status' ?>">
                                    <?= $task['status'] ?>
                                </span>
                            </td>
                            <td class="date-text">
                                <?= date('d M, Y', strtotime($task['created_at'])) ?><br>
                                <small style="font-size: 11px; opacity: 0.8;"><?= date('h:i A', strtotime($task['created_at'])) ?></small>
                            </td>
                            <td class="date-text">
                                <?= date('d M, Y', strtotime($task['updated_at'])) ?><br>
                                <small style="font-size: 11px; opacity: 0.8;"><?= date('h:i A', strtotime($task['updated_at'])) ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
