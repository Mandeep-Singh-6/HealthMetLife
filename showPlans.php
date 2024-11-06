<?php 
require('connect.php');

// Creating a query to select the specified record from the plans table based on plan_id.
$query = "SELECT * FROM plans";
    
// Preparing the query.
$statement = $db->prepare($query);
    
// Executing the query.
$statement->execute();
    
// Fetching the returned row.
$results = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plans</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require('header.php') ?>
    <h1 class = "centerText">Here is the list of Fitness Packages that we offer:</h1>
    <?php foreach($results as $result): ?>
        <div class = "planDiv" style="background-color:<?= $result['title']; ?>; color: <?php if($result['title'] === "Black") echo "white"  ?>">
            <h1><?= $result['title'] ?></h1>
            <h2><?= "Price - $" . $result['price'] ?></h2>
            <p><?= $result['description'] ?></p>
        </div>
    <?php endforeach ?>
    
</body>
</html>