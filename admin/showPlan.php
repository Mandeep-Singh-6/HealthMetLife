<?php 
require('../connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../login.php");
}


$plan_id = 0;
if($_GET){
    //Sanitizing input from the get superglobal.
    global $plan_id;
    $plan_id = filter_input(INPUT_GET,"plan_id", FILTER_VALIDATE_INT);
}

if($plan_id){
    // Creating a query to select the specified record from the plans table based on plan_id.
    $query = "SELECT * FROM plans WHERE plan_id = :plan_id LIMIT 1";
    
    // Preparing the query.
    $statement = $db->prepare($query);
    
    //Binding values to the query.
    $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);
    
    // Executing the query.
    $statement->execute();
    
    // Fetching the returned row.
    $result = $statement->fetch();
}
// If plan_id is non-numeric, redirecting user to index.php.
else{
    header("Location: Plans.php");
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthMetLife</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require('header.php') ?>
    <?php if($result !==  FALSE): ?>
        <div class = "planDiv" style="background-color:<?= $result['bgcolour'] ?>; color:<?= $result['colour'] ?>;">            
            <h1><?= $result['title'] ?></h1>
            <h2><?= "Price - $" . $result['price'] . " Annually" ?></h2>
            <h3><?= $result['description'] ?></h3>
        </div>
    <?php else: ?>
        <p class = "error">Sorry, we couldn't find your plan.</p>
    <?php endif ?>
</body>
</html>