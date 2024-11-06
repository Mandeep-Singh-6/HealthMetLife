<?php 
// Creating a query to get all the pages.

    $query = "SELECT * FROM genericpages ORDER BY created_at ASC";

    // Loading the query into the MySql server cache.
    $statement = $db->prepare($query);

    // Execute the query.
    $statement->execute();


    $headeResults = $statement->fetchAll();

?>
<div id = "header">
    <h1 class = "centerText">HealthMetLife</h1>
    <nav>
        <ul>
            <?php foreach($headeResults as $headeResult): ?>
                <li><a href = "<?="index1.php?page_id=" . $headeResult["page_id"]?>"><?= $headeResult['title'] ?></a></li>
            <?php endforeach ?>
        </ul>
    </nav>
</div>
