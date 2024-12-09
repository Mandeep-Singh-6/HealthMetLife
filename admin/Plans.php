<?php 
require('../connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../login.php");
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

// Getting the page number.
if(!$_GET){
    $page_num = 1;
}
else{
    $page_num = filter_input(INPUT_GET, 'page_num', FILTER_VALIDATE_INT);
    // Handling the cases where:
        // Larger page_num entered manually than allowed.
        // No page_num supplied.
        // Negative page_num.
    if(!$page_num || $page_num < 0 || $page_num > $_SESSION['subPages']){
        // Showing results as first page if invalid page number given.
        $page_num = 1;
    }
    // print_r("Page num:" . $page_num);
}

// Storing the post data in a session variable if new POST is done. Or updating it.
if($_POST){
    $_SESSION['post'] = $_POST;
    // If new post is done, reloading the page with page_num 1.
    header("Location: Plans.php");
}

// Determining if user entered a name to search or not.
if(isset($_POST['name']) && trim($_POST['name']) !== ""){
    $name = trim($_POST['name']);
    $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}
elseif(isset($_SESSION['post']['name']) && trim($_SESSION['post']['name'])){
    $name = trim($_SESSION['post']['name']);
    $name = filter_var($name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
}

// Determining if user has selected a plan category id or not.
if(isset($_POST['plan_category_id'])){
    $plan_category_id = $_POST['plan_category_id'];
}
elseif(isset($_SESSION['post']['plan_category_id'])){
    $plan_category_id = $_SESSION['post']['plan_category_id'];
}

// The number of plans per page.
$plansPerPage = 2;


// Calculating the offset.
$offset = ($page_num - 1) * $plansPerPage;
// print_r("Offset:" . $offset);

// If the user hasn't clicked on the sort button or selected all categories.
// Determining the user selection based on which to sort the categories.
// and querying the database accordingly.
if((!$_POST && !isset($_SESSION['post'])) || $plan_category_id === "all"){

    if(!isset($name)){

        // Creating a query to select all plans ordered by category and then price.
        $query = "SELECT (
                            SELECT COUNT(*)
                            FROM plans p
                         ) AS 'totalPages',
                  p.plan_id, p.title, p.price, p.description, p.colour, 
                  p.bgcolour, p.plan_category_id, i.image_id, i.medium_path, i.small_path,
                  pc.plan_category_name
                  FROM plans p 
                  JOIN plan_categories pc 
                  ON pc.plan_category_id = p.plan_category_id 
                  LEFT JOIN images i
                  ON p.plan_id = i.plan_id
                  ORDER BY p.plan_category_id ASC, p.price DESC
                  LIMIT :limit
                  OFFSET :offset";

        // Preparing the query.
        $statement = $db->prepare($query);

        // Binding values.
        $statement->bindValue(':limit', $plansPerPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Executing the query.
        $statement->execute();

        // Fetching the results.
        $results = $statement->fetchAll();
        }
    else{
        // Attaching wildcard.
        $searchName = "%" . $name . "%";
        // Creating a query to select the specified records from the plans table based on plan_category_id.
        $query = "SELECT (
                            SELECT COUNT(*)
                            FROM plans p
                            WHERE p.title LIKE :name
                         ) AS 'totalPages',
                  p.plan_id, p.title, p.price, p.description, p.colour, 
                  p.bgcolour, p.plan_category_id, i.image_id, i.medium_path, i.small_path,
                  pc.plan_category_name 
                  FROM plans p 
                  JOIN plan_categories pc 
                  ON pc.plan_category_id = p.plan_category_id 
                  LEFT JOIN images i
                  ON p.plan_id = i.plan_id
                  WHERE p.title LIKE :name 
                  ORDER BY p.plan_category_id ASC, p.price DESC
                  LIMIT :limit
                  OFFSET :offset";
            
        // Preparing the query.
        $statement = $db->prepare($query);
        
        //Binding values to the query.
        $statement->bindValue(":name", $searchName, PDO::PARAM_STR);
        $statement->bindValue(':limit', $plansPerPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

    
        // Executing the query.
        $statement->execute();
        
        // Fetching the returned row.
        $results = $statement->fetchAll();
    }
}
// Remove this elseif for user.
elseif($plan_category_id === "NULL"){
    if(!isset($name)){
    // Creating a query to select the specified records from the plans table based on plan_category_id.
    $query = "SELECT (
                            SELECT COUNT(*)
                            FROM plans p
                            WHERE p.plan_category_id = NULL 
                         ) AS 'totalPages',
              p.plan_id, p.title, p.price, p.description, p.colour, 
              p.bgcolour, p.plan_category_id, i.image_id, i.medium_path, i.small_path,
              pc.plan_category_name 
              FROM plans p 
              JOIN plan_categories pc 
              ON pc.plan_category_id = p.plan_category_id
              LEFT JOIN images i
              ON p.plan_id = i.plan_id 
              WHERE p.plan_category_id = NULL 
              ORDER BY p.plan_category_id ASC, p.price DESC
              LIMIT :limit
              OFFSET :offset";
        
    // Preparing the query.
    $statement = $db->prepare($query);

    // Binding values.
    $statement->bindValue(':limit', $plansPerPage, PDO::PARAM_INT);
    $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

    // Executing the query.
    $statement->execute();
    
    // Fetching the returned row.
    $results = $statement->fetchAll();
    }
    else{
        // Attaching wildcard.
        $searchName = "%" . $name . "%";

        // Creating a query to select the specified records from the plans table based on plan_category_id.
        $query = "SELECT (
                            SELECT COUNT(*)
                            FROM plans p
                            WHERE p.plan_category_id = NULL 
                                AND p.title LIKE :name  
                         ) AS 'totalPages',
                  p.plan_id, p.title, p.price, p.description, p.colour, 
                  p.bgcolour, p.plan_category_id, i.image_id, i.medium_path, i.small_path,
                  pc.plan_category_name 
                  FROM plans p 
                  JOIN plan_categories pc 
                  ON pc.plan_category_id = p.plan_category_id
                  LEFT JOIN images i
                  ON p.plan_id = i.plan_id 
                  WHERE p.plan_category_id = NULL 
                    AND p.title LIKE :name 
                  ORDER BY p.plan_category_id ASC, p.price DESC
                  LIMIT :limit
                  OFFSET :offset";
            
        // Preparing the query.
        $statement = $db->prepare($query);
        
        //Binding values.
        $statement->bindValue(":name", $searchName, PDO::PARAM_STR);
        $statement->bindValue(':limit', $plansPerPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

    
        // Executing the query.
        $statement->execute();
        
        // Fetching the returned row.
        $results = $statement->fetchAll();
    }
}
else{
        // Getting the selected category.
        $plan_category_id = filter_var($plan_category_id, FILTER_VALIDATE_INT);
        if($plan_category_id){
            if(!isset($name)){
                // Creating a query to select the specified records from the plans table based on plan_category_id.
                
                $query = "SELECT (
                                SELECT COUNT(*)
                                FROM plans p
                                WHERE p.plan_category_id = :plan_category_id 
                             ) AS 'totalPages',
                          p.plan_id, p.title, p.price, p.description, p.colour, 
                          p.bgcolour, p.plan_category_id, i.image_id, i.medium_path, i.small_path,
                          pc.plan_category_name 
                          FROM plans p 
                          JOIN plan_categories pc 
                          ON pc.plan_category_id = p.plan_category_id
                          LEFT JOIN images i
                          ON p.plan_id = i.plan_id 
                          WHERE p.plan_category_id = :plan_category_id 
                          ORDER BY p.plan_category_id ASC, p.price DESC
                          LIMIT :limit
                          OFFSET :offset";
                
                // Preparing the query.
                $statement = $db->prepare($query);
            
                //Binding values to the query.
                $statement->bindValue(":plan_category_id", $plan_category_id, PDO::PARAM_INT);
                $statement->bindValue(':limit', $plansPerPage, PDO::PARAM_INT);
                $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    
            
                // Executing the query.
                $statement->execute();
            
                // Fetching the returned row.
                $results = $statement->fetchAll();
            }
            else{
                // Attaching wildcard.
                $searchName = "%" . $name . "%";
    
                // Creating a query to select the specified records from the plans table based on plan_category_id.
                // We needed a join just to get the category_name here. Is there a better way to do it?
                $query = "SELECT (
                                SELECT COUNT(*)
                                FROM plans p
                                WHERE p.plan_category_id = :plan_category_id 
                                    AND p.title LIKE :name
                             ) AS 'totalPages',
                          p.plan_id, p.title, p.price, p.description, p.colour, 
                          p.bgcolour, p.plan_category_id, i.image_id, i.medium_path, i.small_path,
                          pc.plan_category_name 
                          FROM plans p 
                          JOIN plan_categories pc 
                          ON pc.plan_category_id = p.plan_category_id
                          LEFT JOIN images i
                          ON p.plan_id = i.plan_id 
                          WHERE p.plan_category_id = :plan_category_id 
                            AND p.title LIKE :name 
                          ORDER BY p.plan_category_id ASC, p.price DESC
                          LIMIT :limit
                          OFFSET :offset";
                
                // Preparing the query.
                $statement = $db->prepare($query);
            
                //Binding values to the query.
                $statement->bindValue(":plan_category_id", $plan_category_id, PDO::PARAM_INT);
                $statement->bindValue(":name", $searchName, PDO::PARAM_STR);
                $statement->bindValue(':limit', $plansPerPage, PDO::PARAM_INT);
                $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
    
            
                // Executing the query.
                $statement->execute();
            
                // Fetching the returned row.
                $results = $statement->fetchAll();
            }
        }
}
// Getting the no. of plans per page.
// If the results are false, it means we didn't get any results from the
// query because of no match found.
if(!empty($results)){
    $noOfPages = $results[0]['totalPages'];
    // print_r("total pages:" . $noOfPages);
    
    // No of subpages.
    $noOfSubPages = ceil($noOfPages/$plansPerPage);
    // print_r("sub pages:" . $noOfSubPages);

    // Remembering the no of subPages to check for larger than expected page number.
    $_SESSION['subPages'] = $noOfSubPages;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plans</title>
    <link rel="stylesheet" href="../style.css">
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
                <option value="all" <?php if (isset($plan_category_id) && $plan_category_id === "all") echo 'selected';?>>All</option>
                <option value="NULL" <?php if (isset($plan_category_id) && $plan_category_id === "NULL") echo 'selected';?>>No Category</option>
                <?php foreach($planCategoryResults as $planCategoryResult): ?>
                <option value="<?= $planCategoryResult['plan_category_id'] ?>" <?php if (isset($plan_category_id) && $plan_category_id == $planCategoryResult['plan_category_id']) echo 'selected';?>><?= $planCategoryResult['plan_category_name'] ?></option>
                <?php endforeach ?>
            </select>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value = "<?= (isset($name)) ? $name : "" ?>">
        <button type="submit">Search</button>
        </form>

    <!-- To amount for a scenario where no records are returned. -->
        <?php if(!empty($results)):?>
            <?php foreach ($results as $result):?>
                    <div class = "planDiv" style="background-color:<?= $result['bgcolour'] ?>; color:<?= $result['colour'] ?>;">
                        <div class = "planTextWrapper">
                            <h1><?= $result['title']?></h1>
                            <h2><?= $result['plan_category_name']  ?></h2>
                            <h2><?= "Price - $" . $result['price'] . " Annually" ?></h2>
                            <h3>Click here to - <a href="<?= "showPlan.php?plan_id=" . $result['plan_id']?>">Learn More...</a></h3>
                        </div>
                        <?php if(isset($result['small_path'])):?>
                        <div class = "planImageWrapper">
                            <img src="<?= $result['small_path'] ?>" alt="An image depicting a workout plan"> 
                        </div>
                        <?php endif ?>
                    </div>
            <?php endforeach ?>          
            <ul id = "paginationUl">
                <?php if($page_num != 1): ?>
                    <li><a href=<?= "Plans.php?page_num=". ($page_num - 1)?>>Previous</a></li>
                <?php endif?>
                <?php for($subPage = 1; $subPage <= $noOfSubPages; $subPage++): ?>
                    <li><a href=<?= "Plans.php?page_num=" . $subPage ?> <?php echo ($page_num == $subPage) ? 'class="currentPage"': '';?>><?= $subPage ?></a></li>
                <?php endfor ?>
                <?php if($page_num != $noOfSubPages): ?>
                    <li><a href=<?= "Plans.php?page_num=" . ($page_num + 1)?>>Next</a></li>
                <?php endif ?>
            </ul>
        <!-- If no results are returned, show a message. -->
        <?php else: ?>
            <h1>There are no plans associated with this category.</h1>
        <?php endif ?>
     </div>
</body>
</html>