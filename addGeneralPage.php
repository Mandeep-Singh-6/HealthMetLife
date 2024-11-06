<?php
require('connect.php');
require('authenticate.php');

if($_POST && !empty($_POST["content"]) && !empty($_POST["title"])){

    // Sanitizing user input from the form.
    $title = filter_input(INPUT_POST,"title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = filter_input(INPUT_POST,"content", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

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
            header("Location: index1.php");
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require('header.php') ?>
    <h1 class = "centerText">Create a General Page</h1>

    <form method = "post">
        <fieldset>
            <p>
                <label for="title">Title</label>
                <input type="text" id = "title" name = "title">
            </p>
            <p>
                <label for="content">Content</label>
                <textarea id = "content" name = "content"></textarea>
            </p>
            <p>
                <button type = "submit">Create</button>
            </p>
        </fieldset>
    </form>
</body>
</html>