<?php
require('../connect.php');
require('authenticate.php');

$error="";

if($_POST){

    // Sanitizing user input from the form.
    $username = filter_input(INPUT_POST,"username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST,"email", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST,"password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_id = filter_input(INPUT_POST,"user_id", FILTER_VALIDATE_INT);

    // Validating if all inputs are correct.
    if(($username !== false) && ($user_id !== false) && ($email !== false) && ($password !== false)){
        // Checking if edit button is clicked.
        if($_POST['action'] == "Update"){
            // Updating the specified record.
            if(!empty($_POST["username"]) && !empty($_POST["user_id"]) && !empty($_POST["email"]) && !empty($_POST["password"])){
            
                // Creating a query to update the data.
                $query = "UPDATE users SET username = :username, user_id = :user_id, email = :email, password = :password WHERE user_id = :user_id LIMIT 1";
                
                // Preparing the query.
                $statement = $db->prepare( $query );
                
                // Binding values to the query.
                $statement->bindValue(":username", $username, PDO::PARAM_STR);
                $statement->bindValue(":email", $email, PDO::PARAM_STR);
                $statement->bindValue(":password", $password, PDO::PARAM_STR);
                $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
                
                // Executing the statement. Redirecting to index.php if succeeded.
                if($statement->execute()){
                    header("Location: showUsers.php");
                }
            }
            else{
                // Redirecting the user to the same page if title or description field is empty.
                header("Location: editUser.php?user_id={$user_id}");
            }
        }
        // Checking if delete button is clicked.
        elseif($_POST['action'] == "Delete"){
            // Creating a query to delete the specified field.
            $query = "DELETE FROM users WHERE user_id = :user_id LIMIT 1";

            // Preparing the query.
            $statement = $db->prepare( $query );

            // Binding values.
            $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);

            //Executing the query and redirecting to index.php if succeeded.
            if($statement->execute()){
                header("Location: showUsers.php");
            }
        }
        exit;
    }
    else{
        // Showing error to the user.
        $error = "There is a validation error in your data.";
    }
}
elseif (isset($_GET['user_id'])){
    // Validating the plan_id entered by the user.
    $user_id = filter_input(INPUT_GET,'user_id', FILTER_VALIDATE_INT);
    if($user_id){
        // Creating a query to select the specified record from the blogs table based on plan_id.
        $query = "SELECT * FROM users WHERE user_id = :user_id LIMIT 1";
    
        // Preparing the query.
        $statement = $db->prepare($query);
      
        //Binding values to the query.
        $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
      
        // Executing the query.
        $statement->execute();
      
        // Fetching the returned row.
        $result = $statement->fetch();
    }
    // If plan_id is non-numeric, redirecting user to index.php.
    else{
        header("Location: showUsers.php");
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require('header.php') ?>
    <?php if($error): ?>
        <p class = "error"><?= $error ?></p>
    <?php else: ?>
        <?php if(isset($result)):?> 
        <h1 class = "centerText">Edit User</h1>

        <form method = "post" class = "pageForm">
            <fieldset>
            <input type="hidden" name = "user_id" value = "<?= $result['user_id'] ?>">
            <div id="formSeparator">
                <label for="username">Username</label>
                <input type="text" id = "username" name = "username" value = "<?= $result['username'] ?>" autofocus>
            </div>
            <div id="formSeparator">
                <label for="email">Email</label>
                <input type="email" id = "email" name = "email" value = "<?= $result['email'] ?>">
            </div>
            <div id="formSeparator">
                <label for="password">Password</label>
                <input type="password" id = "password" name = "password" value = "<?= $result['password'] ?>">
            </div>
            <div id="formSeparator">
            <button type = "submit" name = "action" value = "Update" >Update</button>
            <button type = "submit" name = "action" value = "Delete" onclick = "return confirm('Do you really want to delete?')">Delete</button>
            </div>
        </fieldset>
    </form>
        </form>
        <?php else: ?>
            <p class = "error">We couldn't find any record with the specified id.</p>
        <?php endif ?>
    <?php endif ?>
</body>
</html>