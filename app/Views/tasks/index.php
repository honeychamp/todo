<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    
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

        .todo-container {
            width: 100%;
            max-width: 500px;
            background: #fff;
            padding: 40px 30px 70px;
            border-radius: 10px;
            position: relative;
        }

        .auth-bar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .auth-bar a {
            text-decoration: none;
            margin-left: 15px;
            color: #555;
            font-weight: 500;
        }

        .auth-bar a:hover {
            color: #ff5945;
        }

        .todo-container h2 {
            color: #002765;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .todo-container h2 img {
            width: 30px;
            margin-left: 10px;
        }

        .row-input {
            display: flex;
            align-items: center;
            background: #edeef0;
            border-radius: 30px;
            padding-left: 20px;
            margin-bottom: 25px;
        }

        .row-input.disabled {
            background: #f0f0f0;
            opacity: 0.7;
            cursor: not-allowed;
        }

        .row-input input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            padding: 10px;
            font-size: 15px;
        }

        .row-input button {
            border: none;
            outline: none;
            padding: 16px 50px;
            background: #ff5945;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            border-radius: 40px;
        }
        
        .row-input button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .task-list {
            list-style: none;
            padding: 0;
        }

        .task-item {
            font-size: 17px;
            user-select: none;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1px;
            border-radius: 5px;
        }

        .task-item:hover {
            background-color: #f9f9f9;
        }

        .toggle-form {
            flex-grow: 1;
            display: flex;
            align-items: center;
            padding: 12px 10px 12px 45px;
            cursor: pointer;
            position: relative;
        }

        .toggle-form::before {
            content: '';
            position: absolute;
            height: 25px;
            width: 25px;
            border-radius: 50%;
            border: 1px solid #ccc;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .task-item.checked .toggle-form {
            color: #555;
            text-decoration: line-through;
        }

        .task-item.checked .toggle-form::before {
            background-color: #ff5945;
            border-color: #ff5945;
        }
        
        .task-item.checked .toggle-form::after {
            content: '\2713';
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #fff;
            font-size: 14px;
        }

        .close-btn {
            color: #555;
            font-size: 22px;
            line-height: 40px;
            height: 40px;
            width: 40px;
            text-align: center;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
            margin-right: 5px;
        }

        .close-btn:hover {
            background: #edeef0;
            color: red;
        }

        .task-form-inline {
            display: contents;
        }
    </style>
</head>
<body>

<div class="todo-container">
    <div class="auth-bar">
        <?php if(session()->get('logged_in')): ?>
            <span>Welcome, <b><?= esc(session()->get('username')) ?></b></span>
            <a href="<?= base_url('auth/logout') ?>">Logout</a>
        <?php else: ?>
            <a href="<?= base_url('auth/login') ?>">Login</a>
            <a href="<?= base_url('auth/register') ?>">Sign Up</a>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0">To-Do List <img src="https://em-content.zobj.net/source/microsoft-teams/337/clipboard_1f4cb.png" alt="icon"></h2>
        <?php if(session()->get('logged_in')): ?>
            <a href="<?= base_url('tasks/history') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 13px;">View Records</a>
        <?php endif; ?>
    </div>
    

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show p-2" role="alert" style="font-size: 14px;">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show p-2" role="alert" style="font-size: 14px;">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row-input <?= session()->get('logged_in') ? '' : 'disabled' ?>">
        <form action="<?= base_url('tasks/create') ?>" method="POST" class="d-flex w-100">
            <input type="text" name="title" placeholder="<?= session()->get('logged_in') ? 'Add your task' : 'Please login to add tasks' ?>" required <?= session()->get('logged_in') ? '' : 'disabled' ?>>
            <button type="submit" <?= session()->get('logged_in') ? '' : 'disabled' ?>>Add</button>
        </form>
    </div>

    <ul class="task-list">
        <?php if (!session()->get('logged_in')): ?>
             <li class="text-center text-muted py-3">Login to view or manage your tasks.</li>
        <?php elseif (empty($tasks)): ?>
             <li class="text-center text-muted py-3">No tasks found. Add one above!</li>
        <?php else: ?>
            <?php foreach ($tasks as $task): ?>
                <li class="task-item <?= $task['status'] == 'completed' ? 'checked' : '' ?>">
                    
                    <form action="<?= base_url('tasks/update/' . $task['id']) ?>" method="POST" class="task-form-inline">
                        <div class="toggle-form" onclick="this.parentNode.submit()">
                            <?= esc($task['title']) ?>
                        </div>
                    </form>

                    <form action="<?= base_url('tasks/delete/' . $task['id']) ?>" method="POST" class="task-form-inline">
                        <span class="close-btn" onclick="if(confirm('Are you sure you want to delete this task?')) this.parentNode.submit()">&times;</span>
                    </form>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
