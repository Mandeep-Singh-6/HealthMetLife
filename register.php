<?php 
require('connect.php');

$error = "";
// Inserting the user record into the users table.
if($_POST && !empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["email"])){

    // Sanitizing user input from the form.
    $username = filter_input(INPUT_POST,"username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST,"password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmPassword = filter_input(INPUT_POST,"confirmPassword", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST,"email", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $role_id = 2;

    if($password !== $confirmPassword){
        global $error;
        $error = "The passwords don't match please try again.";
    }
    // Validating if all input is correct, else redirect user to index.php.
    elseif($username && $password && $email && $confirmPassword){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Creating an insert query to insert data into the table.
        $query = "INSERT INTO users (username, email, password, role_id) VALUES (:username, :email, :password, :role_id)";

        // Loads the query into the SQL server's cache and returns a PDOStatement object.
        $statement = $db->prepare($query);

        // Binding values to the loaded query.
        $statement->bindValue(":username", $username, PDO::PARAM_STR);
        $statement->bindValue(":password", $hashed_password, PDO::PARAM_STR);
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
    <base href="http://localhost:31337/wd2/Final%20Project/HealthMetLife/">
    <link rel="stylesheet" href="style.css">
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>

    <?php require('header.php') ?>
    <?php if($error):?>
        <h1 class="error"><?= $error ?></h1>
    <?php endif ?>
    <h1 class = "centerText">Register a new user</h1>
    <form method = "post" class = "pageForm centerForm">
        <fieldset>
            <div class="formSeparator">
                <label for="username">Username</label>
                <input type="text" id = "username" name = "username" autofocus>
            </div>
            <div class="formSeparator">
                <label for="email">Email</label>
                <input type="email" id = "email" name = "email" autofocus>
            </div>
            <div class="formSeparator">
                <label for="password">Password</label>
                <input type="password" id = "password" name = "password" autofocus>
            </div>
            <div class="formSeparator">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id = "confirmPassword" name = "confirmPassword" autofocus>
            </div>
            <div class="formSeparator">
                <button type = "submit">Create</button>
            </div>
        </fieldset>
    </form>
    <div class="centerText">
            <a href="login.php" class="aButton">Login Instead</a>
        </div>
</body>
</html>