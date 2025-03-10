<?php
require('../user/connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../../login.php");
}

// Checking for sort criteria.
if(isset($_POST['criteria'])){
    $criteria = filter_input(INPUT_POST,"criteria", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}
// Trying to retian values when a different form is submitted.
elseif (isset($criteria)){
    // Want to implement this. But variables don't retain values. Do
    // I need to use cookies?
}
else{
    $criteria = 'title';
}

if(isset($_POST['direction'])){
    $direction = filter_input(INPUT_POST,"direction", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}
elseif (isset($direction)){
    
}
else{
    $direction = 'ASC';
}
    
if (isset($_POST['direction2'])){
    $direction2 = filter_input(INPUT_POST,"direction2", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}
elseif (isset($direction2)){
    
}
else{
    $direction2 = 'ASC';
}

// Creating a query to get all the generic pages.
// Why couldn't we bind here.
$query = "SELECT * FROM genericpages ORDER BY $criteria $direction";

// Loading the query into the MySql server cache.
$statement = $db->prepare($query);

// Execute the query.
$statement->execute();

$genericResults = $statement->fetchAll();

// Getting all the plans.
// Creating a query to select the specified record from the plans table based on plan_id.
$query = "SELECT * FROM plans ORDER BY $criteria $direction";
    
// Preparing the query.
$statement = $db->prepare($query);
    
// Executing the query.
$statement->execute();
    
// Fetching the returned row.
$planResults = $statement->fetchAll();

// Getting all the plan categories.
// Creating a query to select the specified record from the plan categories table.
$query = "SELECT * FROM plan_categories ORDER BY plan_category_name $direction2";
    
// Preparing the query.
$statement = $db->prepare($query);
    
// Executing the query.
$statement->execute();
    
// Fetching the returned row.
$planCategoryResults = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="/wd2/Final%20Project/HealthMetLife/admin/">
    <title>List All Pages</title>
    <link rel="stylesheet" href="http://localhost:31337/wd2/Final%20Project/HealthMetLife/style.css">
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <?php require('header.php') ?>
    <div id="wrapper">
        <?php require('adminNav.php') ?>
        <main>
            <form method="post" class = "sortForm">
                <fieldset>
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
                </fieldset>
            </form>
            <h1>General Pages:</h1>
            <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($genericResults as $genericResult): ?>
                            <tr>
                                <td><?=$genericResult['title']?></td>
                                <td><?=$genericResult['created_at']?></td>
                                <td><?=$genericResult['updated_at']?></td>
                                <td><a href="<?= "editGeneralPage.php?page_id=" . $genericResult['page_id'] ?>">edit</a></td>
                            </tr>
                        <?php endforeach?>
                    </tbody>
                </table>

            <h1>Plans:</h1>
            <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($planResults as $planResult): ?>
                            <tr>
                                <td><?=$planResult['title']?></td>
                                <td><?=$planResult['created_at']?></td>
                                <td><?=$planResult['updated_at']?></td>
                                <td><a href="<?= "editPlan.php?plan_id=" . $planResult['plan_id'] ?>">edit</a></td>
                            </tr>
                        <?php endforeach?>
                    </tbody>
                </table>
            <!-- A separate sort form for plan categories. -->
            <form method="post" class = "sortForm">
                <fieldset>
                    <label for="direction2">Sort By:</label> 
                    <select name="direction2" id="direction2">
                        <option value="ASC" <?php if (isset($_POST['direction2']) && $_POST['direction2'] == 'ASC') echo 'selected';?>>Oldest</option>
                        <option value="DESC" <?php if (isset($_POST['direction2']) && $_POST['direction2'] == 'DESC') echo 'selected';?>>Latest</option>
                    </select>
                    <button type="submit">Sort</button>
                </fieldset>
            </form>

            <h1>Plan Categories:</h1>
            <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($planCategoryResults as $planCategoryResult): ?>
                            <tr>
                                <td><?=$planCategoryResult['plan_category_name']?></td>
                                <td><a href="<?= "editPlanCategory.php?plan_category_id=" . $planCategoryResult["plan_category_id"]?>">edit</a></td>
                            </tr>
                        <?php endforeach?>
                    </tbody>
                </table>
        </main>
    </div>
</body>
</html>