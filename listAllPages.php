<?php
require('connect.php');

// Creating a query to get all the pages.
$query = "SELECT * FROM genericpages ORDER BY created_at ASC";

// Loading the query into the MySql server cache.
$statement = $db->prepare($query);

// Execute the query.
$statement->execute();


$genericResults = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List All Pages</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require('adminHeader.php') ?>
    <h1>Generic Pages</h1>
    <ul>
        <?php foreach($genericResults as $genericResult): ?>
        <li><a href = "<?= "editGeneralPage.php?page_id=" . $genericResult['page_id'] ?>"><?= $genericResult['title'] ?></a></li>
        <?php endforeach ?>
    </ul>
</body>
</html>