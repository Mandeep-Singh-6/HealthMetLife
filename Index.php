<?php 
require('connect.php');
session_start();
if(!isset($_SESSION['login_role'])){
    header("Location: http://localhost:31337/wd2/Final Project/HealthMetLife/login");
}
$page_id = 0;
if($_GET){
    //Sanitizing input from the get superglobal.
    global $page_id;
    $page_id = filter_input(INPUT_GET,"page_id", FILTER_VALIDATE_INT);
}
else{
    // Defaulting to the home page.
    // Still need to implement a functionality to prevent its deletion.
    header("Location: http://localhost:31337/wd2/Final Project/HealthMetLife/1/home");
    exit();
}

if($page_id){
    // Creating a query to select the specified record from the genericpages table based on page_id.
    $query = "SELECT * FROM genericpages WHERE page_id = :page_id LIMIT 1";
    
    // Preparing the query.
    $statement = $db->prepare($query);
    
    //Binding values to the query.
    $statement->bindValue(":page_id", $page_id, PDO::PARAM_INT);
    
    // Executing the query.
    $statement->execute();
    
    // Fetching the returned row.
    $result = $statement->fetch();

    
    if(!isset($_GET['p']) && isset($result['slug'])){
        header("Location: http://localhost:31337/wd2/Final Project/HealthMetLife/" . $result['page_id'] . '/' . $result['slug']);
        exit();
    }
}
// If page_id is non-numeric, redirecting user to index.php.
else{
    header("Location: http://localhost:31337/wd2/Final Project/HealthMetLife/1/home");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthMetLife</title>
    <base href="http://localhost:31337/wd2/Final%20Project/HealthMetLife/">
    <link rel="stylesheet" href="style.css">
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <?php require('header.php') ?>
    <div id="wrapper">
        <?php if($page_id === 1): ?>
        <h1>Welcome <?= (isset($_SESSION['username'])) ? "back, " . $_SESSION['username']: "Fitness Enthusiast" ?>!</h1>
        <?php else: ?>
        <h1><?= $result['title'] ?></h1>
        <?php endif ?>
        <div><?= $result['content'] ?></div>
    </div>
</body>
</html>