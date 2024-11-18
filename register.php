<?php 
require('connect.php');

// Inserting the user record into the users table.
if($_POST && !empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["email"])){

    // Sanitizing user input from the form.
    $username = filter_input(INPUT_POST,"username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST,"password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST,"email", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $role_id = 2;

    // Validating if all input is correct, else redirect user to index.php.
    if($username && $password && $email){
        // Creating an insert query to insert data into the table.
        $query = "INSERT INTO users (username, email, password, role_id) VALUES (:username, :email, :password, :role_id)";

        // Loads the query into the SQL server's cache and returns a PDOStatement object.
        $statement = $db->prepare($query);

        // Binding values to the loaded query.
        $statement->bindValue(":username", $username, PDO::PARAM_STR);
        $statement->bindValue(":password", $password, PDO::PARAM_STR);
        $statement->bindValue(":email", $email, PDO::PARAM_STR);
        $statement->bindValue(":role_id", $role_id, PDO::PARAM_INT);

        // Executing the query.
        if($statement->execute()){
            header("Location: Index.php");
            // Write code to start session and login user.
            // Either send username and password directly to login.php or a simple redirect.
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register a new user</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require('header.php') ?>
    <h1 class = "centerText">Register a new user</h1>
    <form method = "post" class = "pageForm">
        <fieldset>
            <div id="formSeparator">
                <label for="username">Username</label>
                <input type="text" id = "username" name = "username" autofocus>
            </div>
            <div id="formSeparator">
                <label for="email">Email</label>
                <input type="email" id = "email" name = "email" autofocus>
            </div>
            <div id="formSeparator">
                <label for="password">Password</label>
                <input type="password" id = "password" name = "password" autofocus>
            </div>
            <div id="formSeparator">
                <button type = "submit">Create</button>
            </div>
        </fieldset>
    </form>
</body>
</html>