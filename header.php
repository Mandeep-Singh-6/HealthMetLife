<?php 
// Creating a query to get all the pages.

    $query = "SELECT * FROM genericpages ORDER BY created_at ASC";

    // Loading the query into the MySql server cache.
    $statement = $db->prepare($query);

    // Execute the query.
    $statement->execute();

    // Fetching the first row to remove the Home page in the navbar.
    $homeRow = $statement->fetch();

    $headeResults = $statement->fetchAll();

?>
<div id = "header">
    <h1 class = "centerText"><a href="Index.php?page_id=1">HealthMetLife</a></h1>
    <nav>
        <ul>
            <?php foreach($headeResults as $headeResult): ?>
                <li><a href = "<?="Index.php?page_id=" . $headeResult["page_id"]?>"><?= $headeResult['title'] ?></a></li>
            <?php endforeach ?>
            <li><a href="Plans.php">Plans</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
</div>
