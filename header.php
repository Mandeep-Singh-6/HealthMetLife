<?php 
// Creating a query to get all the pages.

    $query = "SELECT title FROM genericpages ORDER BY title DESC";

    // Loading the query into the MySql server cache.
    $statement = $db->prepare($query);

    // Execute the query.
    $statement->execute();


    $results = $statement->fetchAll();

?>
<div id = "header">
    <h1>HealthMetLife</h1>
    <nav>
        <ul>
            <?php foreach($results as $result): ?>
                <li><a href = "?" ><?= $result['title'] ?></a></li>
            <?php endforeach ?>
        </ul>
    </nav>
</div>
