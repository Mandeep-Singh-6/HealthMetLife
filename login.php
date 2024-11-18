<?php
require('connect.php');
if(isset($_SESSION)){
    session_destroy();
}
session_start();

$error = "";

if($_POST){
    // getting and validating input from user.
    $email = filter_input(INPUT_POST,"email", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST,"password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    //Writing a query to get the user with the specified email.
    $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
    
    // Preparing the query.
    $statement = $db->prepare($query);
    
    //Binding values to the query.
    $statement->bindValue(":email", $email, PDO::PARAM_STR);
    
    // Executing the query.
    $statement->execute();
    
    // Fetching the returned row.
    $result = $statement->fetch();
    // 1 is admin and 2 is normal user.
    if($result !== false){
        if($password === $result['password']){
            if($result['role_id'] === 1){
                $_SESSION['login_role'] = 1;
                header("Location: admin/index.php");
            }
            elseif($result['role_id'] === 2){
                $_SESSION['login_role'] = 2;
                header("Location: index.php");
            }
        }
        else{
            global $error;
            $error = "The password is incorrect.";
        }
    }
    else{
        global $error;
        $error = "We couldn't find the email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthMetLife</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id = "header">
        <h1 class = "centerText">HealthMetLife</h1>
    </div>
    <?php if($error):?>
        <h1 class="error"><?= $error ?></h1>
    <?php endif ?>
    <h1 class = "centerText">Login</h1>
    <form method = "post" class = "pageForm">
        <fieldset>
            <div id="formSeparator">
                <label for="email">Email</label>
                <input type="email" id = "email" name = "email" autofocus>
            </div>
            <div id="formSeparator">
                <label for="password">Password</label>
                <input type="password" id = "password" name = "password" autofocus>
            </div>
            <div id="formSeparator">
                <button type = "submit">Login</button>
            </div>
        </fieldset>
    </form>
    <div class="centerText">
            <a href="register.php" class="aButton">Register New User</a>
        </div>
</body>
</html>