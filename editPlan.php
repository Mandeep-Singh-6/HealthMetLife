<?php
require('connect.php');
require('authenticate.php');

$error="";

if($_POST){

    // Sanitizing user input from the form.
    $title = filter_input(INPUT_POST,"title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $content = filter_input(INPUT_POST,"content", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = $_POST['description'];
    $price = filter_input(INPUT_POST,"price", FILTER_VALIDATE_FLOAT);
    $plan_id = filter_input(INPUT_POST,"plan_id", FILTER_VALIDATE_INT);

    // Validating if all inputs are correct, else redirect user to index.php.
    if(($title !== false) && ($description !== false) && ($plan_id !== false) && ($price !== false)){
        // Checking if edit button is clicked.
        if($_POST['action'] == "Update"){
            // Updating the specified record.
            if(!empty($_POST["description"]) && !empty($_POST["title"]) && !empty($_POST["plan_id"]) && !empty($_POST["price"])){
            
                    // Creating a query to update the data.
                    $query = "UPDATE plans SET title = :title, description = :description, price = :price WHERE plan_id = :plan_id LIMIT 1";
                
                    // Preparing the query.
                    $statement = $db->prepare( $query );
                
                    // Binding values to the query.
                    $statement->bindValue(":title", $title, PDO::PARAM_STR);
                    $statement->bindValue(":description", $description, PDO::PARAM_STR);
                    $statement->bindValue(":price", $price);
                    $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);
                
                    // Executing the statement. Redirecting to index.php if succeeded.
                    if($statement->execute()){
                        header("Location: adminPlans.php");
                    }
            }
            else{
                // Redirecting the user to the same page if title or description field is empty.
                header("Location: editPlan.php?plan_id={$plan_id}");
            }
        }
        // Checking if delete button is clicked.
        else if($_POST['action'] == "Delete"){
            // Creating a query to delete the specified field.
            $query = "DELETE FROM plans WHERE plan_id = :plan_id LIMIT 1";

            // Preparing the query.
            $statement = $db->prepare( $query );

            // Binding values.
            $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);

            //Executing the query and redirecting to index.php if succeeded.
            if($statement->execute()){
                header("Location: adminPlans.php");
            }
        }
        exit;
    }
    else{
        // Showing error to the user.
        $error = "There is a validation error in your data.";
    }
}
else if (isset($_GET['plan_id'])){
    // Validating the plan_id entered by the user.
    $plan_id = filter_input(INPUT_GET,'plan_id', FILTER_VALIDATE_INT);
    if($plan_id){
        // Creating a query to select the specified record from the blogs table based on plan_id.
        $query = "SELECT * FROM plans WHERE plan_id = :plan_id LIMIT 1";
    
        // Preparing the query.
        $statement = $db->prepare($query);
      
        //Binding values to the query.
        $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);
      
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
    <title>Add General Page</title>
    <link rel="stylesheet" href="style.css">
        <!-- include libraries(jQuery, bootstrap) -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
</head>
<body>
    <?php require('adminHeader.php') ?>
    <?php if($error): ?>
        <p class = "error"><?= $error ?></p>
    <?php else: ?>
        <?php if(isset($result)):?> 
        <h1 class = "centerText">Edit a Plan</h1>

        <form method = "post" class = "pageForm">
            <fieldset>
            <input type="hidden" name = "plan_id" value = "<?= $result['plan_id'] ?>">
            <div id="formSeparator">
                <label for="title">Title</label>
                <input type="text" id = "title" name = "title" value = "<?= $result['title'] ?>">
            </div>
            <div id="formSeparator">
                <label for="price">Price</label>
                <input type="number" id = "price" name = "price" value = "<?= $result['price'] ?>">
            </div>
            <div id="formSeparator">
                <label for="summernote">Description</label>
                <textarea id = "summernote" name = "description">value = "<?= $result['description'] ?>"</textarea>
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
    <script>
        $(document).ready(function() {
        $('#summernote').summernote();
        });
    </script>
</body>
</html>