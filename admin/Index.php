<?php 
require('../user/connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../login.php");
}

if($_GET){
    //Sanitizing input from the get superglobal.
    $page_id = filter_input(INPUT_GET,"page_id", FILTER_VALIDATE_INT);
    $slug = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}
else{
    header("Location: index.php?page_id=1&p=home");
    exit();
}

if($page_id){
    // Creating a query to select the specified record from the genericpages table based on page_id.
    $query = "SELECT * FROM genericpages 
              WHERE page_id = :page_id 
                AND slug = :slug
              LIMIT 1";
    
    // Preparing the query.
    $statement = $db->prepare($query);
    
    //Binding values to the query.
    $statement->bindValue(":page_id", $page_id, PDO::PARAM_INT);
    $statement->bindValue(":slug", $slug, PDO::PARAM_STR);
    
    // Executing the query.
    $statement->execute();
    
    // Fetching the returned row.
    $result = $statement->fetch();
}
// If page_id is non-numeric, redirecting user to index.php.
else{
    header("Location: index.php?page_id=1&p=home");
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
        <?php if(!empty($result)):?>
            <?php if($page_id === 1): ?>
            <h1>Welcome <?= (isset($_SESSION['username'])) ? "back, " . $_SESSION['username'] : ""?>!</h1>
            <?php else: ?>
            <h1><?= $result['title'] ?></h1>
            <?php endif ?>
            <div><?= $result['content'] ?></div>
        <?php else: ?>
            <p class="error">Sorry, this page doesn't exist.</p>
        <?php endif ?>
    </div>
</body>
</html>