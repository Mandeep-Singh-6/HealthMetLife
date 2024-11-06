<?php 
// Creating a query to get all the pages.

    $query = "SELECT * FROM genericpages ORDER BY created_at ASC";

    // Loading the query into the MySql server cache.
    $statement = $db->prepare($query);

    // Execute the query.
    $statement->execute();


    $results = $statement->fetchAll();

?>
<div id = "header">
    <h1 class = "centerText"><a href="index1.php">HealthMetLife</a></h1>
    <nav>
        <ul>
            <?php foreach($results as $result): ?>
                <li><a href = "?" ><?= $result['title'] ?></a></li>
            <?php endforeach ?>
        </ul>
    </nav>
</div>
