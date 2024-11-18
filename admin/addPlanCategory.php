<?php
require('../connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../login.php");
}

if($_POST && !empty($_POST["name"])){

    // Sanitizing user input from the form.
    $name = filter_input(INPUT_POST,"name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Validating if all input is correct, else redirect user to index.php.
    if($name){
        // Creating an insert query to insert data into the table.
        $query = "INSERT INTO plan_categories (plan_category_name) VALUES (:name)";

        // Loads the query into the SQL server's cache and returns a PDOStatement object.
        $statement = $db->prepare($query);

        // Binding values to the loaded query.
        $statement->bindValue(":name", $name, PDO::PARAM_STR);

        // Executing the query.
        if($statement->execute()){
            header("Location: listAllPages.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Plan Category</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require('header.php') ?>
    <h1 class = "centerText">Create a New Plan Category</h1>

    <form method = "post" class = "pageForm">
        <fieldset>
            <div id="formSeparator">
                <label for="name">Name</label>
                <input type="text" id = "name" name = "name" autofocus>
            </div>
            <div id="formSeparator">
                <button type = "submit">Create</button>
            </div>
        </fieldset>
    </form>
</body>
</html>