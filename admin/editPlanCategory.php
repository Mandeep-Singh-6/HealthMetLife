<?php
require('../connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../login.php");
}


$error="";

if($_POST){

    // Sanitizing user input from the form.
    $name = filter_input(INPUT_POST,"name", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $plan_category_id = filter_input(INPUT_POST,"plan_category_id", FILTER_VALIDATE_INT);

    // Validating if all inputs are correct, else redirect user to index.php.
    if(($name !== false) && ($plan_category_id !== false) ){
        // Checking if edit button is clicked.
        if($_POST['action'] == "Update"){
            // Updating the specified record.
            if(!empty($_POST["name"]) && !empty($_POST["plan_category_id"])){
            
                // Creating a query to update the data.
                $query = "UPDATE plan_categories SET plan_category_name = :name, plan_category_id = :plan_category_id WHERE plan_category_id = :plan_category_id LIMIT 1";
                
                // Preparing the query.
                $statement = $db->prepare( $query );
                
                // Binding values to the query.
                $statement->bindValue(":name", $name, PDO::PARAM_STR);
                $statement->bindValue(":plan_category_id", $plan_category_id, PDO::PARAM_INT);
                
                // Executing the statement. Redirecting to index.php if succeeded.
                if($statement->execute()){
                    header("Location: listAllPages.php");
                }
            }
            else{
                // Redirecting the user to the same page if title or description field is empty.
                header("Location: editPlanCategory.php?plan_category_id={$plan_category_id}");
            }
        }
        // Checking if delete button is clicked.
        elseif($_POST['action'] == "Delete"){
            // Creating a query to delete the specified field.
            $query = "DELETE FROM plan_categories WHERE plan_category_id = :plan_category_id LIMIT 1";

            // Preparing the query.
            $statement = $db->prepare( $query );

            // Binding values.
            $statement->bindValue(":plan_category_id", $plan_category_id, PDO::PARAM_INT);

            //Executing the query and redirecting to index.php if succeeded.
            if($statement->execute()){
                header("Location: listAllPages.php");
            }
        }
        exit;
    }
    else{
        // Showing error to the user.
        $error = "There is a validation error in your data.";
    }
}
elseif (isset($_GET['plan_category_id'])){
    // Validating the plan_id entered by the user.
    $plan_category_id = filter_input(INPUT_GET,'plan_category_id', FILTER_VALIDATE_INT);
    if($plan_category_id){
        // Creating a query to select the specified record from the blogs table based on plan_id.
        $query = "SELECT * FROM plan_categories WHERE plan_category_id = :plan_category_id LIMIT 1";
    
        // Preparing the query.
        $statement = $db->prepare($query);
      
        //Binding values to the query.
        $statement->bindValue(":plan_category_id", $plan_category_id, PDO::PARAM_INT);
      
        // Executing the query.
        $statement->execute();
      
        // Fetching the returned row.
        $result = $statement->fetch();
    }
    // If plan_id is non-numeric, redirecting user to index.php.
    else{
        header("Location: listAllPages.php");
    }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Plan Category</title>
    <base href="http://localhost:31337/wd2/Final%20Project/HealthMetLife/admin/">
    <link rel="stylesheet" href="../style.css">
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('adminNav.php') ?>
    <?php if($error): ?>
        <p class = "error"><?= $error ?></p>
    <?php else: ?>
        <?php if(isset($result)):?> 
        <h1>Edit Plan Category</h1>

        <form method = "post" class = "pageForm">
            <fieldset>
            <input type="hidden" name = "plan_category_id" value = "<?= $result['plan_category_id'] ?>">
            <div class="formSeparator">
                <label for="name">Name</label>
                <input type="text" id = "name" name = "name" value = "<?= $result['plan_category_name'] ?>" autofocus>
            </div>
            <div class="formSeparator">
            <button type = "submit" name = "action" value = "Update" >Update</button>
            <button type = "submit" name = "action" value = "Delete" onclick = "return confirm('Do you really want to delete?')">Delete</button>
            </div>
            </fieldset>
        </form>
        <?php else: ?>
            <p class = "error">We couldn't find any record with the specified id.</p>
        <?php endif ?>
    <?php endif ?>
    </main>
    </div>
</body>
</html>