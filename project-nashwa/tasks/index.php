<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT * FROM tasks WHERE user_id = :user_id');
$stmt->execute(['user_id' => $user_id]);
$tasks = $stmt->fetchAll();

if (isset($_POST['task_name']) && isset($_POST['task_description']) && !isset($_POST['task_id'])) {
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];

    $stmt = $pdo->prepare('INSERT INTO tasks (user_id, task_name, task_description, status) VALUES (:user_id, :task_name, :task_description, "incomplete")');
    $stmt->execute([
        'user_id' => $user_id,
        'task_name' => $task_name,
        'task_description' => $task_description
    ]);
    echo 'Task Added';
    exit();
}

if (isset($_POST['task_name']) && isset($_POST['task_description']) && isset($_POST['task_id'])) {
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $task_id = $_POST['task_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare('UPDATE tasks SET task_name = :task_name, task_description = :task_description, status = :status WHERE id = :task_id');
    $stmt->execute([
        'task_name' => $task_name,
        'task_description' => $task_description,
        'status' => $status,
        'task_id' => $task_id
    ]);
    echo 'Task Updated';
    exit();
}
?>

<?php include('../includes/header.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding-bottom: 60px;
        }

        .table th, .table td {
            text-align: center;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #2980b9;
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            padding: 15px;
        }

        .btn {
            border-radius: 25px;
            font-weight: bold;
        }

        .btn-success {
            background-color: #27ae60;
        }

        .btn-warning {
            background-color: #f39c12;
        }

        .btn-danger {
            background-color: #e74c3c;
        }

        .btn:hover {
            filter: brightness(1.1);
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>

<div class="container content">
    <div class="card shadow-lg" style="width: 100%; max-width: 900px;">
        <div class="card-header">
            <h3 class="text-center mb-0">Task List</h3>
        </div>
        <div class="card-body">
            <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                Add New Task
            </button>

            <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="addTaskForm">
                                <div class="mb-3">
                                    <label for="task_name" class="form-label">Task Name</label>
                                    <input type="text" class="form-control" id="task_name" name="task_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="task_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="task_description" name="task_description" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Add Task</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editTaskForm">
                                <input type="hidden" id="task_id" name="task_id">
                                <div class="mb-3">
                                    <label for="edit_task_name" class="form-label">Task Name</label>
                                    <input type="text" class="form-control" id="edit_task_name" name="task_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_task_description" class="form-label">Description</label>
                                    <textarea class="form-control" id="edit_task_description" name="task_description" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select class="form-control" id="edit_status" name="status">
                                        <option value="incomplete">Incomplete</option>
                                        <option value="complete">Complete</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-bordered table-responsive mt-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Task Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    <?php 
    $counter = 1;
    foreach ($tasks as $task): ?>
    <tr>
        <td><?php echo $counter++; ?></td> 
        <td><?php echo $task['task_name']; ?></td>
        <td><?php echo $task['task_description']; ?></td>
        <td><?php echo $task['status']; ?></td>
        <td>
            <button class="btn btn-warning edit-btn" data-id="<?php echo $task['id']; ?>" data-name="<?php echo $task['task_name']; ?>" data-description="<?php echo $task['task_description']; ?>" data-status="<?php echo $task['status']; ?>" data-bs-toggle="modal" data-bs-target="#editTaskModal">
                Edit
            </button>
            <button class="btn btn-danger delete-btn" data-id="<?php echo $task['id']; ?>">Delete</button>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>

            </table>
        </div>
    </div>
</div>

<div class="footer">
    <p>Task Management System &copy; 2024</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const name = button.getAttribute('data-name');
        const description = button.getAttribute('data-description');
        const status = button.getAttribute('data-status');

        document.getElementById('task_id').value = id;
        document.getElementById('edit_task_name').value = name;
        document.getElementById('edit_task_description').value = description;
        document.getElementById('edit_status').value = status;
    });
});

document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        Swal.fire({
            title: 'Are you sure?',
            text: "This task will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
        }).then(result => {
            if (result.isConfirmed) {
                window.location.href = `delete.php?id=${id}`;
            }
        });
    });
});

document.getElementById('addTaskForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    fetch('index.php', {
        method: 'POST',
        body: formData
    }).then(response => response.text()).then(data => {
        Swal.fire({
            icon: 'success',
            title: 'Task Added',
            text: data,
        });
        setTimeout(() => window.location.reload(), 1500);
    }).catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong!',
        });
    });
});

document.getElementById('editTaskForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    fetch('index.php', {
        method: 'POST',
        body: formData
    }).then(response => response.text()).then(data => {
        Swal.fire({
            icon: 'success',
            title: 'Task Updated',
            text: data,
        });
        setTimeout(() => window.location.reload(), 1500);
    }).catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Something went wrong!',
        });
    });
});
</script>

</body>
</html>
