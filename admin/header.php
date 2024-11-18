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
                <li><a href = "<?="Index.php?page_id=" . $headeResult["page_id"]?>"><?= $headeResult['title'] ?></a></li>
            <?php endforeach ?>
            <li><a href="Plans.php">Plans</a></li>
            <li><a href="addPlan.php">Add a Plan</a></li>
            <li><a href = "addGeneralPage.php">Add General Page</a></li>
            <li><a href = "addPlanCategory.php">Add Plan Category</a></li>
            <li><a href = "showUsers.php">Users</a></li>
            <li><a href = "listAllPages.php">List Pages</a></li>
            <li><a href = "../logout.php">Logout</a></li>
        </ul>
    </nav>
</div>
