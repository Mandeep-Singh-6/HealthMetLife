<?php
require('connect.php');
session_start();
if(!isset($_SESSION['login_role'])){
    header("Location: login.php");
}


if (isset($_GET['comment_id'])){
    // Validating the comment_id entered by the user.
    $comment_id = filter_input(INPUT_GET,'comment_id', FILTER_VALIDATE_INT);
    if($comment_id){
        // Creating a query to select the specified record from the blogs table based on comment_id.
        $query = "SELECT * FROM comments WHERE comment_id = :comment_id LIMIT 1";
    
        // Preparing the query.
        $statement = $db->prepare($query);
      
        //Binding values to the query.
        $statement->bindValue(":comment_id", $comment_id, PDO::PARAM_INT);
      
        // Executing the query.
        $statement->execute();
      
        // Fetching the returned row.
        $result = $statement->fetch();
    }
    // If comment_id is non-numeric, redirecting user to showPlan.php.
    else{
        header("Location: Plans.php");
    }

    // Redirecting a user back if the comment doesn't belong to them.
    if($result['user_id'] != $_SESSION['user_id'] || $result == false){
        header("Location: showPlan.php?plan_id=" . $result['plan_id']);
    }
}
if($_POST){
    // Sanitizing user input from the form.
    $comment_id = filter_input(INPUT_POST,"comment_id", FILTER_VALIDATE_INT);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if($comment_id && trim($comment) != "" ){
        $query = "UPDATE comments
                  SET content = :content
                  WHERE comment_id = :comment_id";
                
        // Preparing the query.
        $statement = $db->prepare($query);

        // Binding values.
        $statement->bindValue(':content', $comment, PDO::PARAM_STR);
        $statement->bindValue(':comment_id', $comment_id, PDO::PARAM_INT);

        // Executing the query.
        if($statement->execute()){
            header("Location: showPlan.php?plan_id=" . $result['plan_id']);
        }
    }
    else{
        header("Location: editComment.php?comment_id=" . $comment_id);
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
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('header.php') ?>
    <h1>Edit a comment</h1>

    <form method = "post" class = "pageForm">
        <fieldset>
            <input type="hidden" name = "comment_id" value = "<?= $result['comment_id'] ?>">
            <div class="formSeparator">
                <label for="comment">Comment:</label>
                <textarea name="comment" id="comment"><?= $result['content'] ?></textarea>
            </div>
            <div class="formSeparator">
                <button type = "submit">Update</button>
            </div>
        </fieldset>
    </form>
</body>
</html>