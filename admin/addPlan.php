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


if($_POST && !empty($_POST["description"]) && !empty($_POST["title"]) && !empty($_POST["price"])){

    // Sanitizing user input from the form.
    $title = filter_input(INPUT_POST,"title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $colour = filter_input(INPUT_POST,"colour", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $bgcolour = filter_input(INPUT_POST,"bgcolour", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $price = filter_input(INPUT_POST,"price", FILTER_VALIDATE_FLOAT);
    // $content = filter_input(INPUT_POST,"content", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = $_POST['description'];

    // Checking for a null value.
    if($_POST['plan_category_id'] === "NULL"){
        $plan_category_id = NULL;
    }
    else{
        $plan_category_id = filter_input(INPUT_POST,"plan_category_id", FILTER_VALIDATE_INT); 
    }
    // Validating if all input is correct, else redirect user to index.php.
    if($title && $description && $price && $colour && $bgcolour){
        
        $query = "INSERT INTO plans (title, description, created_at, price, colour, bgcolour, plan_category_id) VALUES (:title, :description, :created_at, :price, :colour, :bgcolour, :plan_category_id)";

        // Loads the query into the SQL server's cache and returns a PDOStatement object.
        $statement = $db->prepare($query);

        // Getting the current datetime.

        $created_at = date("Y-m-d H:i:s");

        // Binding values to the loaded query.
        $statement->bindValue(":title", $title, PDO::PARAM_STR);
        $statement->bindValue(":colour", $colour, PDO::PARAM_STR);
        $statement->bindValue(":bgcolour", $bgcolour, PDO::PARAM_STR);
        $statement->bindValue(":description", $description, PDO::PARAM_STR);
        $statement->bindValue(":created_at", $created_at, PDO::PARAM_STR);
        $statement->bindValue(":price", $price);
        $statement->bindValue(":plan_category_id", $plan_category_id);

        // Executing the query.
        if($statement->execute()){
            header("Location: Plans.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add General Page</title>
    <!-- include libraries(jQuery, bootstrap) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <!-- Importing css at the end. -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<?php include('adminNav.php') ?>
    <h1>Create a New Plan</h1>

    <form method = "post" class = "pageForm">
        <fieldset>
            <div id="formSeparator">
                <label for="title">Title</label>
                <input type="text" id = "title" name = "title">
            </div>
            <div id="formSeparator">
                <label for="price">Price</label>
                <input type="number" id = "price" name = "price" step = ".01" >
            </div>
            <div id="formSeparator">
                <label for="bgcolour">Background</label>
                <input type="color" id = "bgcolour" name = "bgcolour" value = "#000000">
            </div>
            <div id="formSeparator">
                <label for="colour">Text Colour</label>
                <input type="color" id = "colour" name = "colour" value = "#FFFFFF">
            </div>
            <div id="formSeparator">
                <label for="plan_category_id">Category:</label>
                <select name="plan_category_id" id="plan_category_id">
                    <option value="NULL">None</option>
                    <?php foreach($planCategoryResults as $planCategoryResult): ?>
                    <option value="<?= $planCategoryResult['plan_category_id'] ?>"><?= $planCategoryResult['plan_category_name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div id="formSeparator">
                <label for="summernote">Description</label>
                <textarea id = "summernote" name = "description"></textarea>
            </div>
            <div id="formSeparator">
                <button type = "submit">Create</button>
            </div>
        </fieldset>
    </form>
    </main>
    </div>
    <script>
        $(document).ready(function() {
        $('#summernote').summernote();
        });
    </script>
</body>
</html>