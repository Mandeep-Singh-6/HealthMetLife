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
    <h1 class = "centerText"><a href="http://localhost:31337/wd2/Final Project/HealthMetLife/home">HealthMetLife</a></h1>
    <nav>
        <ul>
            <?php foreach($headeResults as $headeResult): ?>
                <li><a href = "<?="http://localhost:31337/wd2/Final Project/HealthMetLife/" . $headeResult['slug']?>"><?= $headeResult['title'] ?></a></li>
            <?php endforeach ?>
            <li><a href="plans.php?page_num=1">Plans</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
</div>
