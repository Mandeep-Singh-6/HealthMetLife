<?php 
require('../connect.php');
require('authenticate.php');

// Getting all the plan categories.
// Creating a query to select the specified record from the plan categories table.
$query = "SELECT * FROM plan_categories ORDER BY plan_category_name";
    
// Preparing the query.
$statement = $db->prepare($query);
    
// Executing the query.
$statement->execute();
    
// Fetching the returned row.
$planCategoryResults = $statement->fetchAll();

// Determining the user selection based on which to sort the categories.
// and querying the database accordingly.

// If the user hasn't clicked on the sort button or selected all categories.
if(!$_POST || $_POST['plan_category_id'] === "all"){

    // Defining a global array to store all result sets.
    $resultsArray = [];

    foreach($planCategoryResults as $planCategoryResult){
        // Creating a query to select the specified records from the plans table based on plan_category_id.
        $query = "SELECT * FROM plans WHERE plan_category_id = :plan_category_id ORDER BY price DESC";
    
        // Preparing the query.
        $statement = $db->prepare($query);
    
        //Binding values to the query.
        $statement->bindValue(":plan_category_id", $planCategoryResult['plan_category_id'], PDO::PARAM_INT);

        // Executing the query.
        $statement->execute();
    
        // Fetching the returned row.
        $results = $statement->fetchAll();
        global $resultsArray;
        $resultsArray[] = $results;
    }
}
elseif($_POST['plan_category_id'] === "NULL"){
    // Creating a query to select the specified records from the plans table based on plan_category_id.
    $query = "SELECT * FROM plans WHERE plan_category_id = NULL ORDER BY price DESC";
    
    // Preparing the query.
    $statement = $db->prepare($query);

    // Executing the query.
    $statement->execute();

    // Fetching the returned row.
    $results = $statement->fetchAll();
}
else{
    // Getting the selected category.
    $plan_category_id = filter_input(INPUT_POST,'plan_category_id', FILTER_VALIDATE_INT);

    // Creating a query to select the specified records from the plans table based on plan_category_id.
    // We needed a join just to get the category_name here. Is there a better way to do it?
    $query = "SELECT * FROM plans JOIN plan_categories ON plan_categories.plan_category_id = plans.plan_category_id WHERE plans.plan_category_id = :plan_category_id ORDER BY price DESC";
    
    // Preparing the query.
    $statement = $db->prepare($query);

    //Binding values to the query.
    $statement->bindValue(":plan_category_id", $plan_category_id, PDO::PARAM_INT);

    // Executing the query.
    $statement->execute();

    // Fetching the returned row.
    $results = $statement->fetchAll();

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin- Plans</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require('header.php') ?>
    <h1 class = "centerText">Here is the list of Fitness Packages that we offer:</h1>

    <!-- Form to sort plans by categories. -->
    <form method="post" class = "sortForm">
    <label for="plan_category_id">Category:</label>
        <select name="plan_category_id" id="plan_category_id">
            <option value="all" <?php if (isset($_POST['plan_category_id']) && $_POST['plan_category_id'] === "all") echo 'selected';?>>All</option>
            <option value="NULL" <?php if (isset($_POST['plan_category_id']) && $_POST['plan_category_id'] === "NULL") echo 'selected';?>>No Category</option>
            <?php foreach($planCategoryResults as $planCategoryResult): ?>
            <option value="<?= $planCategoryResult['plan_category_id'] ?>" <?php if (isset($_POST['plan_category_id']) && $_POST['plan_category_id'] == $planCategoryResult['plan_category_id']) echo 'selected';?>><?= $planCategoryResult['plan_category_name'] ?></option>
            <?php endforeach ?>
        </select>
    <button type="submit">Sort</button>
    </form>
    
    <!-- To amount for a scenario where no records are returned. -->
    <?php if((isset($results) && count($results) > 0) || (isset($resultsArray) && count($resultsArray) > 0)):?>
        <!-- If no selected or 'all' is selected. Then show all categories. -->
        <?php if(!$_POST || $_POST['plan_category_id'] === "all"): ?>
            <?php for($i = 0; $i < count($planCategoryResults); $i++): ?>
            <h2><?= $planCategoryResults[$i]['plan_category_name'] ?>:</h2>
                <?php foreach ($resultsArray[$i] as $result):?>
                    <div class = "planDiv" style="background-color:<?= $result['bgcolour'] ?>; color:<?= $result['colour'] ?>;">            
                        <h2><?= $result['title'] ?></h2>
                        <h3><?= "Price - $" . $result['price'] . " Annually"?></h3>
                        <h4><?= $result['description'] ?></h4>
                    </div>
                <?php endforeach ?>             
            <?php endfor ?>
        <?php elseif ($_POST['plan_category_id'] === "NULL"):?>
            <h2>No Category:</h2>
            <?php foreach ($results as $result):?>
                <div class = "planDiv" style="background-color:<?= $result['bgcolour'] ?>; color:<?= $result['colour'] ?>;">            
                    <h2><?= $result['title'] ?></h2>
                    <h3><?= "Price - $" . $result['price'] . " Annually"?></h3>
                    <h4><?= $result['description'] ?></h4>
                </div>
            <?php endforeach ?>
        <?php else: ?>
            <!-- Getting the plan name from the first record. Could take from any record but we know 
            that there will be one record for sure. -->
            <h2><?= $results[0]['plan_category_name'] ?>:</h2>
            <?php foreach ($results as $result):?>
                <div class = "planDiv" style="background-color:<?= $result['bgcolour'] ?>; color:<?= $result['colour'] ?>;">            
                    <h2><?= $result['title'] ?></h2>
                    <h3><?= "Price - $" . $result['price'] . " Annually"?></h3>
                    <h4><?= $result['description'] ?></h4>
                </div>
            <?php endforeach ?>
        <?php endif ?>
    <!-- If no results are returned, show a message. -->
     <?php else: ?>
        <h1>There are no plans associated with this category.</h1>
    <?php endif ?>

</body>
</html>