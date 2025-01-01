<?php 
require('connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 2){
    header("Location: ../login.php");
}

// Set the default timezone to Central Time (America/Winnipeg) 
date_default_timezone_set('America/Winnipeg');

$plan_id = 0;
if($_GET){
    //Sanitizing input from the get superglobal.
    global $plan_id;
    $plan_id = filter_input(INPUT_GET,"plan_id", FILTER_VALIDATE_INT);
}

if($plan_id){
    // Creating a query to select the specified record from the plans table based on plan_id.
    $query = "SELECT p.plan_id, p.title, p.price, p.description, p.colour, 
              p.bgcolour, p.plan_category_id, i.image_id, i.medium_path, i.small_path,
              pc.plan_category_name
              FROM plans p 
              JOIN plan_categories pc 
              ON pc.plan_category_id = p.plan_category_id
              LEFT JOIN images i
              ON p.plan_id = i.plan_id 
              WHERE p.plan_id = :plan_id LIMIT 1";
    
    // Preparing the query.
    $statement = $db->prepare($query);
    
    //Binding values to the query.
    $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);
    
    // Executing the query.
    $statement->execute();
    
    // Fetching the returned row.
    $result = $statement->fetch();
}
// If plan_id is non-numeric, redirecting user to Plans.php.
else{
    header("Location: plans.php?page_num=1");
}

// Checking for comments to display.
$query = "SELECT u.username AS 'Uname', u.user_id, c.content, 
                 c.updated_at, c.comment_id, c.username AS 'Cname'
          FROM users u
          RIGHT OUTER JOIN comments c
          ON u.user_id = c.user_id
          WHERE c.plan_id = :plan_id
          ORDER BY updated_at DESC";

// Preparing the query.
$statement = $db->prepare($query);

// Binding values.
$statement->bindValue(":plan_id", $plan_id, PDO::PARAM_STR);

// Executing the query.
$statement->execute();

// Getting the comment results.
$commentResults = $statement->fetchAll();

if($_POST){
    // Sanitizing the user input.
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if(trim($comment) != ""){
        // Creating a query to insert the comment.
        $query = "INSERT INTO comments(plan_id, user_id, content, created_at)
                    VALUES(:plan_id, :user_id, :content, :created_at)";
            
        // Preparing the statement.
        $statement = $db->prepare($query);
    
        // Gettig the user id.
        $user_id = $_SESSION['user_id'];
    
        // Getting the current datetime.
        $created_at = date("Y-m-d H:i:s");
    
        // Binding values.
        $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);
        $statement->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $statement->bindValue(":content", $comment, PDO::PARAM_STR);
        $statement->bindValue(":created_at", $created_at);
    
        // Executing the query.
        $statement->execute();
    
        // Redirecting to break the PRG pattern.
        header("Location: showPlan.php?plan_id=" . $plan_id);
    }
    else{
        header("Location: showPlan.php?plan_id=" . $plan_id);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthMetLife</title>
    <link rel="stylesheet" href="../style.css">
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <?php require('header.php') ?>
    <div id="wrapper">
        <?php if($result !==  false): ?>
            <div class = "showPlanDiv" style="background-color:<?= $result['bgcolour'] ?>; color:<?= $result['colour'] ?>;">            
                <?php if(isset($result['medium_path'])):?>
                    <img src="<?= "../admin/" . $result['medium_path'] ?>" alt="An image depicting a workout plan"> 
                <?php endif ?>
                <h1><?= $result['title']?></h1>
                <h2><?= $result['plan_category_name']  ?></h2>
                <h2><?= "Price - $" . $result['price'] . " Annually" ?></h2>
                <div><?= $result['description'] ?></div>
            </div>
            <div class = "commentContainer">
                <?php foreach ($commentResults as $commentResult): ?>
                    <div class = "commentDiv">
                        <img class="replyImg" src="reply.png" alt="reply symbol">
                        <h4><?= ($commentResult["Uname"] === NULL) ? $commentResult["Cname"] : $commentResult["Uname"] ?></h4>
                        <?php if($_SESSION['user_id'] == $commentResult['user_id']): ?>
                            <h5>
                                <?= "Updated at : " . $commentResult['updated_at'] . " - "?>
                                <a href="<?= "editComment.php?comment_id=" . $commentResult['comment_id']?>">Edit</a>
                                <a href="<?= "deleteComment.php?comment_id=" . $commentResult['comment_id']?>" onclick = "return confirm('Do you really want to delete?')">Delete</a>
                            </h5>
                        <?php endif ?>
                        <p>
                            <?= $commentResult['content'] ?>
                        </p>
                    </div>
                <?php endforeach ?>
            </div>
            <form method = "post" class = "centerForm">
                <fieldset class="commentSet">
                        <label for="comment">Comment? Type here:</label>
                        <textarea name="comment" id="comment"></textarea>
                        <button type="submit" id="sendButtonNC"><img id="sendImg" src="send.png" alt="Send button"></button>
                </fieldset>
            </form>
        <?php else: ?>
            <p class = "error">Sorry, we couldn't find your plan.</p>
        <?php endif ?>
    </div>
</body>
</html>