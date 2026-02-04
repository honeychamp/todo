<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List 📝</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(135deg, #153677, #4e085f);
            padding: 50px 10px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .todo-app {
            width: 100%;
            max-width: 540px;
            background: #fff;
            margin: 100px auto 20px;
            padding: 40px 30px 70px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .todo-app h2 {
            color: #002765;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .todo-app h2 img {
            width: 30px;
            margin-left: 10px;
        }

        .row-input {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #edeef0;
            border-radius: 30px;
            padding-left: 20px;
            margin-bottom: 25px;
        }

        .row-input input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            padding: 10px;
            font-size: 16px;
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
            font-weight: 500;
            transition: 0.3s;
        }

        .row-input button:hover {
            background: #e64a39;
        }

        ul {
            padding: 0;
        }

        ul li {
            list-style: none;
            font-size: 17px;
            padding: 12px 8px 12px 10px;
            user-select: none;
            cursor: pointer;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #f3f3f3;
        }

        ul li span.text {
            flex-grow: 1;
            color: #555;
        }

        ul li.checked span.text {
            color: #888;
            text-decoration: line-through;
        }

        .btn-delete {
            color: #ff5945;
            text-decoration: none;
            font-size: 18px;
            padding: 5px 10px;
            border-radius: 50%;
            transition: 0.2s;
        }

        .btn-delete:hover {
            background: #fff0ef;
        }

        .task-status-btn {
            text-decoration: none;
            margin-right: 15px;
            font-size: 18px;
        }

        .status-pending { color: #ccc; }
        .status-completed { color: #28a745; }

    </style>
</head>
<body>

    <div class="todo-app">
        <h2>To-Do List <img src="https://cdn-icons-png.flaticon.com/512/3589/3589839.png" alt="icon"></h2>
        
        <form action="<?= base_url('tasks/create') ?>" method="post">
            <div class="row-input">
                <input type="text" name="title" placeholder="Add your task" required autocomplete="off">
                <button type="submit">Add</button>
            </div>
        </form>

        <ul>
            <?php if (empty($tasks)): ?>
                <li class="text-center border-0 text-muted">No tasks added yet.</li>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <li class="<?= $task['status'] === 'completed' ? 'checked' : '' ?>">
                        <a href="<?= base_url('tasks/update/' . $task['id']) ?>" class="task-status-btn">
                            <i class="status-icon <?= $task['status'] === 'completed' ? 'status-completed' : 'status-pending' ?>">
                                <?= $task['status'] === 'completed' ? '●' : '○' ?>
                            </i>
                        </a>
                        
                        <span class="text"><?= esc($task['title']) ?></span>
                        
                        <a href="<?= base_url('tasks/delete/' . $task['id']) ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Are you sure you want to delete this task?');">
                            ×
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

</body>
</html>



