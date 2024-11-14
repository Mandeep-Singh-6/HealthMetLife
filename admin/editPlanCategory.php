<?php
require('../connect.php');
require('authenticate.php');

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
        else if($_POST['action'] == "Delete"){
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
else if (isset($_GET['plan_category_id'])){
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
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require('header.php') ?>
    <?php if($error): ?>
        <p class = "error"><?= $error ?></p>
    <?php else: ?>
        <?php if(isset($result)):?> 
        <h1 class = "centerText">Edit Plan Category</h1>

        <form method = "post" class = "pageForm">
            <fieldset>
            <input type="hidden" name = "plan_category_id" value = "<?= $result['plan_category_id'] ?>">
            <div id="formSeparator">
                <label for="name">Name</label>
                <input type="text" id = "name" name = "name" value = "<?= $result['plan_category_name'] ?>" autofocus>
            </div>
            <div id="formSeparator">
            <button type = "submit" name = "action" value = "Update" >Update</button>
            <button type = "submit" name = "action" value = "Delete" onclick = "return confirm('Do you really want to delete?')">Delete</button>
            </div>
            </fieldset>
        </form>
        <?php else: ?>
            <p class = "error">We couldn't find any record with the specified id.</p>
        <?php endif ?>
    <?php endif ?>
</body>
</html>