<?php
require('connect.php');

// Checking for sort criteria.
if($_POST){
    $criteria = filter_input(INPUT_POST,"criteria", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $direction = filter_input(INPUT_POST,"direction", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}
else{
    $criteria = 'title';
    $direction = 'ASC';
}
// Creating a query to get all the pages.
// Why couldn't we bind here.
$query = "SELECT * FROM genericpages ORDER BY $criteria $direction";

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
    <form method="post" class = "sortForm">
    <label for="criteria">Sort By:</label> 
    <select id="criteria" name="criteria"> 
        <option value="title" <?php if (isset($_POST['criteria']) && $_POST['criteria'] == 'title') echo 'selected';?>>Title</option> 
        <option value="created_at" <?php if (isset($_POST['criteria']) && $_POST['criteria'] == 'created_at') echo 'selected';?>>Created At</option> 
        <option value="updated_at" <?php if (isset($_POST['criteria']) && $_POST['criteria'] == 'updated_at') echo 'selected';?>>Updated At</option> 
    </select> 
    <select name="direction" id="direction">
    <option value="ASC" <?php if (isset($_POST['direction']) && $_POST['direction'] == 'ASC') echo 'selected';?>>Ascending</option>
    <option value="DESC" <?php if (isset($_POST['direction']) && $_POST['direction'] == 'DESC') echo 'selected';?>>Descending</option>
    </select>
    <button type="submit">Sort</button>
    </form>
    <h1>Generic Pages</h1>
    <ul>
        <?php foreach($genericResults as $genericResult): ?>
        <li><a href = "<?= "editGeneralPage.php?page_id=" . $genericResult['page_id'] ?>"><?= $genericResult['title'] ?></a></li>
        <?php endforeach ?>
    </ul>
</body>
</html>