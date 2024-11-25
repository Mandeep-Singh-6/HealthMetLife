<?php 
require('./connect.php');
session_start();
if(!isset($_SESSION['login_role'])){
    header("Location: login.php");
}

// Getting all the plan categories.
// Creating a query to select the specified record from the plan categories table.
$query = "SELECT * FROM plan_categories ORDER BY plan_category_name";

// Preparing the query.
$statement = $db->prepare($query);
    
// Executing the query.
$statement->execute();
    
// Fetching the returned row.
$planCategoryResults = $statement->fetchAll();

// Determining if user entered a name to search or not.
if(!isset($_POST['name']) || trim($_POST['name']) === ""){
    $name = "%";
}
else{
    $name = filter_input(INPUT_POST,'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $name = "%" . $name . "%";
}

// If the user hasn't clicked on the sort button or selected all categories.
// Determining the user selection based on which to sort the categories.
// and querying the database accordingly.
if(!$_POST || $_POST['plan_category_id'] === "all"){
    // Defining a global array to store all result sets.
    $resultsHashArray = [];

    foreach($planCategoryResults as $planCategoryResult){
        // Creating a query to select the specified records from the plans table based on plan_category_id.
        $query = "SELECT * FROM plans WHERE plan_category_id = :plan_category_id AND title LIKE :name ORDER BY price DESC";
        
        // Preparing the query.
        $statement = $db->prepare($query);
        
        //Binding values to the query.
        $statement->bindValue(":plan_category_id", $planCategoryResult['plan_category_id'], PDO::PARAM_INT);
        $statement->bindValue(":name", $name, PDO::PARAM_STR);
    
        // Executing the query.
        $statement->execute();
        
        // Fetching the returned row.
        $results = $statement->fetchAll();
        if(count($results) > 0){
            $resultHash = [$planCategoryResult['plan_category_name'] => $results];
            $resultsHashArray[] = $resultHash;
        }
    }
}
else{
        // Getting the selected category.
        $plan_category_id = filter_input(INPUT_POST,'plan_category_id', FILTER_VALIDATE_INT);
    
        // Creating a query to select the specified records from the plans table based on plan_category_id.
        // We needed a join just to get the category_name here. Is there a better way to do it?
        $query = "SELECT * FROM plans JOIN plan_categories ON plan_categories.plan_category_id = plans.plan_category_id WHERE plans.plan_category_id = :plan_category_id AND title LIKE :name ORDER BY price DESC";
        
        // Preparing the query.
        $statement = $db->prepare($query);
    
        //Binding values to the query.
        $statement->bindValue(":plan_category_id", $plan_category_id, PDO::PARAM_INT);
        $statement->bindValue(":name", $name, PDO::PARAM_STR);
    
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
    <title>Plans</title>
    <link rel="stylesheet" href="./style.css">
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <?php require('header.php') ?>
    <div id="wrapper">
        <h1 class = "centerText">Our Offerings:</h1>

        <!-- Form to sort plans by categories. -->
        <form method="post" class = "sortForm marginedForm">
        <label for="plan_category_id">Category:</label>
            <select name="plan_category_id" id="plan_category_id">
                <option value="all" <?php if (isset($_POST['plan_category_id']) && $_POST['plan_category_id'] === "all") echo 'selected';?>>All</option>
                <?php foreach($planCategoryResults as $planCategoryResult): ?>
                <option value="<?= $planCategoryResult['plan_category_id'] ?>" <?php if (isset($_POST['plan_category_id']) && $_POST['plan_category_id'] == $planCategoryResult['plan_category_id']) echo 'selected';?>><?= $planCategoryResult['plan_category_name'] ?></option>
                <?php endforeach ?>
            </select>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value = "<?= (isset($_POST['name'])) ? $_POST['name'] : "" ?>">
        <button type="submit">Search</button>
        </form>

    <!-- To amount for a scenario where no records are returned. -->
        <?php if((isset($results) && count($results) > 0) || (isset($resultsHashArray) && count($resultsHashArray) > 0)):?>
            <!-- If no selected or 'all' is selected. Then show all categories. -->
            <?php if(!$_POST || $_POST['plan_category_id'] === "all"): ?>
                <?php foreach($resultsHashArray as $resultHashEntry): ?>
                <?php foreach($resultHashEntry as $category => $resultsArrayElement): ?>
                    <h2 class="planH2"><?= $category ?>:</h2>
                        <?php foreach ($resultsArrayElement as $result):?>
                            <div class = "planDiv" style="background-color:<?= $result['bgcolour'] ?>; color:<?= $result['colour'] ?>;">            
                                <h1><?= $result['title'] ?></h1>
                                <h2><?= "Price - $" . $result['price'] . " Annually" ?></h2>
                                <h3>Click here to - <a href="<?= "showPlan.php?plan_id=" . $result['plan_id']?>">Learn More...</a></h3>
                            </div>
                        <?php endforeach ?>             
                    <?php endforeach ?>             
                <?php endforeach ?>
            <?php else:?>
                <!-- Remove this ternary operator for user. -->
                <h2 class="planH2"><?= $results[0]['plan_category_name'] ?></h2>
                <?php foreach ($results as $result):?>
                    <div class = "planDiv" style="background-color:<?= $result['bgcolour'] ?>; color:<?= $result['colour'] ?>;">            
                            <h1><?= $result['title'] ?></h1>
                            <h2><?= "Price - $" . $result['price'] . " Annually" ?></h2>
                            <h3>Click here to - <a href="<?= "showPlan.php?plan_id=" . $result['plan_id']?>">Learn More...</a></h3>
                        </div>
                <?php endforeach ?>
            <?php endif ?>
        <!-- If no results are returned, show a message. -->
        <?php else: ?>
            <h1>There are no plans associated with this category.</h1>
        <?php endif ?>
     </div>
</body>
</html>