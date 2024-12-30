<?php
require('../user/connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../user/login.php");
}


if($_POST && !empty($_POST["content"]) && !empty($_POST["title"])){

    // Sanitizing user input from the form.
    $title = filter_input(INPUT_POST,"title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $content = filter_input(INPUT_POST,"content", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = $_POST['content'];

    // Validating if all input is correct, else redirect user to index.php.
    if($title && $content){
        // Creating an insert query to insert data into the table.
        $query = "INSERT INTO genericpages (title, content, created_at) VALUES (:title, :content, :created_at)";

        // Loads the query into the SQL server's cache and returns a PDOStatement object.
        $statement = $db->prepare($query);

        // Getting the current datetime.

        $created_at = date("Y-m-d H:i:s");

        // Binding values to the loaded query.
        $statement->bindValue(":title", $title, PDO::PARAM_STR);
        $statement->bindValue(":content", $content, PDO::PARAM_STR);
        $statement->bindValue(":created_at", $created_at, PDO::PARAM_STR);

        // Executing the query.
        if($statement->execute()){
            header("Location: Index.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add General Page</title>
    <!-- include libraries(jQuery, bootstrap) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <!-- Including own css at the end. -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include('adminNav.php') ?>
    <h1>Create a General Page</h1>

    <form method = "post" class = "pageForm">
        <fieldset>
            <div class="formSeparator">
                <label for="title">Title</label>
                <input type="text" id = "title" name = "title">
            </div>
            <div class="formSeparator">
                <label for="summernote">Content</label>
                <textarea id = "summernote" name = "content"></textarea>
            </div>
            <div class="formSeparator">
                <button type = "submit">Create</button>
            </div>
        </fieldset>
    </form>
    </main>
    </div>
    <script>
        $(document).ready(function() {
        $('#summernote').summernote();
        });
    </script>
</body>
</html>