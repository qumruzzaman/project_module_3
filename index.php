<?php
define("TASKS_FILE", value: "tasks.json");
// function to read json file
function loadTasks():array{
 if(!file_exists(TASKS_FILE)){
    return [];
 }
 $data = file_get_contents(TASKS_FILE);
 return $data ? json_decode($data, true) : [];
}
$all_tasks = loadTasks();
// function to save task in json file
function saveTask(array $tasks_arr):void{
    file_put_contents(TASKS_FILE, json_encode($tasks_arr, JSON_PRETTY_PRINT));
}
if($_SERVER['REQUEST_METHOD'] === "POST"){
    // add a task
    if(isset($_POST["task"]) && !empty(trim($_POST["task"]))) {
        $all_tasks[] = [
            "task"=> htmlspecialchars(trim($_POST["task"])),
            "done"=> false
        ];
        saveTask($all_tasks);
        header("location: " . $_SERVER['PHP_SELF']);
        exit;
}elseif(isset($_POST['delete'])){
    // delete a task
    unset($all_tasks[$_POST['delete']]);
    $all_tasks = array_values(($all_tasks));
    saveTask($all_tasks);
    header('location: '. $_SERVER['PHP_SELF']);
    exit;
}elseif(isset($_POST['toggle'])){
    // toggle a task
    $all_tasks[$_POST['toggle']]['done'] = !$all_tasks[$_POST['toggle']]['done'];
    saveTask($all_tasks);
    header('location: '. $_SERVER['PHP_SELF']);
    exit;
}
}
?>
<!-- UI -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.min.css">
    <style>
    body {
        margin-top: 20px;
    }

    .task-card {
        border: 1px solid #ececec;
        padding: 20px;
        border-radius: 5px;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .task {
        color: #888;
    }

    .task-done {
        text-decoration: line-through;
        color: #888;
    }

    .task-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    ul {
        padding-left: 20px;
    }

    button {
        cursor: pointer;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="task-card">
            <h1>To-Do App</h1>

            <!-- Add Task Form -->
            <form method="POST">
                <div class="row">
                    <div class="column column-75">
                        <input type="text" name="task" placeholder="Enter a new task" required>
                    </div>
                    <div class="column column-25">
                        <button type="submit" class="button-primary">Add Task</button>
                    </div>
                </div>
            </form>

            <!-- Task List -->
            <h2>Task List</h2>
            <ul style="list-style: none; padding: 0;">
                <!-- TODO: Loop through tasks array and display each task with a toggle and delete option -->
                <!-- If there are no tasks, display a message saying "No tasks yet. Add one above!" -->
                <?php
                if(empty($all_tasks)) {
                ?>
                <li>No tasks yet. Add one above!</li>
                <!-- if there are tasks, display each task with a toggle and delete option -->
                <?php
                }else{
                    foreach($all_tasks as $index => $task){                  
                ?>

                <li class="task-item">
                    <form method="POST" style="flex-grow: 1;">
                        <input type="hidden" name="toggle" value="<?= $index ?>">

                        <button type="submit"
                            style="border: none; background: none; cursor: pointer; text-align: left; width: 100%;">
                            <span class="task <?= $task['done'] ? 'task-done' : '' ?>">
                                <?= $task['task'] ?>
                            </span>
                        </button>
                    </form>

                    <form method="POST">
                        <input type="hidden" name="delete" value="<?= $index ?>">
                        <button type="submit" class="button button-outline" style="margin-left: 10px;">Delete</button>
                    </form>
                </li>
                <?php
                  }
                }
                ?>

            </ul>

        </div>
    </div>
</body>

</html>