<?php 
require('connect.php');
$page_id = 0;
if($_GET){
    //Sanitizing input from the get superglobal.
    global $page_id;
    $page_id = filter_input(INPUT_GET,"page_id", FILTER_VALIDATE_INT);
}
else{
    // Defaulting to the home page.
    // Still need to implement a functionality to prevent its deletion.
    global $page_id;
    $page_id = 1;
}

if($page_id){
    // Creating a query to select the specified record from the blogs table based on page_id.
    $query = "SELECT * FROM genericpages WHERE page_id = :page_id LIMIT 1";
    
    // Preparing the query.
    $statement = $db->prepare($query);
    
    //Binding values to the query.
    $statement->bindValue(":page_id", $page_id, PDO::PARAM_INT);
    
    // Executing the query.
    $statement->execute();
    
    // Fetching the returned row.
    $result = $statement->fetch();
}
// If page_id is non-numeric, redirecting user to index.php.
else{
    header("Location: index1.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthMetLife</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require('header.php') ?>
        <h1><?= $result['title'] ?></h1>
        <p><?= $result['content'] ?></p>
</body>
</html>