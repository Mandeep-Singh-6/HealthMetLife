<?php
require('user/connect.php');
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
        if(password_verify($password, $result['password'])){
            $_SESSION['login_role'] = $result['role_id'];
            $_SESSION['username'] = $result['username'];
            $_SESSION['user_id'] = $result['user_id'];

            if($result['role_id'] === 1){
                header("Location: admin/1/home");
            }
            elseif($result['role_id'] === 2){
                header("Location: user/1/home");
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
    <base href="/wd2/Final%20Project/HealthMetLife%20-%20Improved/">
    <title>HealthMetLife</title>
    <link rel="stylesheet" href="http://localhost:31337/wd2/Final%20Project/HealthMetLife%20-%20Improved/style.css">
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <?php require('header.php') ?>
    <div id="wrapper">

        <?php if($error):?>
            <h1 class="error"><?= $error ?></h1>
        <?php endif ?>
        <h1 class = "centerText">Login</h1>
            <form method = "post" class = "pageForm centerForm">
                <fieldset>
                    <div class="formSeparator">
                        <label for="email">Email</label>
                        <input type="email" id = "email" name = "email" autofocus>
                    </div>
                    <div class="formSeparator">
                        <label for="password">Password</label>
                        <input type="password" id = "password" name = "password">
                    </div>
                    <div class="formSeparator">
                        <button type = "submit">Login</button>
                    </div>
                    <div class="centerText formSeparator">
                        <p>- or -</p>
                    </div>
                    <div class="centerText formSeparator">
                        <a href="register.php" class="aButton">Register New User</a>
                    </div>
                </fieldset>
            </form>
    </div>
</body>
</html>