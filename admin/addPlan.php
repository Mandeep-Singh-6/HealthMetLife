<?php
require('../user/connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../login.php");
}

// Set the default timezone to Central Time (America/Winnipeg) 
date_default_timezone_set('America/Winnipeg');

// Getting all the plan categories.
// Creating a query to select the specified record from the plan categories table.
$query = "SELECT * FROM plan_categories ORDER BY plan_category_name";

// Preparing the query.
$statement = $db->prepare($query);

// Executing the query.
$statement->execute();

// Fetching the returned row.
$planCategoryResults = $statement->fetchAll();

$current_dir = dirname(__FILE__);
$parent_dir = dirname($current_dir);
require "{$parent_dir}". DIRECTORY_SEPARATOR . "php-image-resize-master". DIRECTORY_SEPARATOR . "lib". DIRECTORY_SEPARATOR . "ImageResize.php";
require "{$parent_dir}". DIRECTORY_SEPARATOR . "php-image-resize-master". DIRECTORY_SEPARATOR . "lib". DIRECTORY_SEPARATOR . "ImageResizeException.php";
use \Gumlet\ImageResize;

// Checking if the uploaded file is an image.
function file_is_an_image($temp_file_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
    
    $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type        = mime_content_type($temp_file_path);
    
    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
    
    return $file_extension_is_valid && $mime_type_is_valid;
}

// For Showing errors.
$error = false;


$image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
$upload_error = isset($_FILES['image']) && ($_FILES['image']['error'] > 0 && $_FILES['image']['error'] !== 4);


if($upload_error){
    $error = "We couldn't upload your image at the moment... Please try again later.";
}

if(!$upload_error && $_POST && !empty($_POST["description"]) && !empty($_POST["title"]) && !empty($_POST["price"])){

    if($image_upload_detected){
        $temp_file_path = $_FILES['image']['tmp_name'];
        $file_name      = $_FILES['image']['name'];

        $file_name = basename($file_name);
            
        if (file_is_an_image($temp_file_path, $file_name)){
            // Getting just the filename without the extension.
            $onlyFilename   = pathinfo($file_name, PATHINFO_FILENAME);
            $onlyExtension   = pathinfo($file_name, PATHINFO_EXTENSION);

            // Saving a medium quality copy.(400px)
            $image = new ImageResize($temp_file_path);
            $image->resizeToWidth(400);
            $new_path_medium = "uploads" . DIRECTORY_SEPARATOR . $onlyFilename ."_meduim" . '.'. $onlyExtension;
            $image->save($new_path_medium);
                
            // Saving a small quality copy.(200px)
            $image = new ImageResize($temp_file_path);
            $image->resizeToWidth(300);
            $new_path_small = "uploads" . DIRECTORY_SEPARATOR . $onlyFilename ."_small" . '.'. $onlyExtension;
            $image->save($new_path_small);

            // Inserting the plan record.

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
            // Validating if all input is correct.
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
                $statement->execute();
            }

                // Adding the image record in the images table associated with the correct page.

                // Getting the plan_id of newly executed plan.
                // According to our business rules two plans in the same category cannot have the same name.
                $query = "SELECT plan_id FROM plans WHERE title = :title AND plan_category_id = :plan_category_id";
            
                // Preparing the query.
                $statement = $db->prepare($query);
                    
                // Binding values.
                $statement->bindValue(":title", $title, PDO::PARAM_STR);
                $statement->bindValue(":plan_category_id", $plan_category_id);
            
                // Executing the query.
                $statement->execute();
            
                // Fetching the result.
                $result = $statement->fetch();
                $plan_id = $result['plan_id'];

                $query = "INSERT INTO images(medium_path, small_path, plan_id) VALUES (:medium_path, :small_path, :plan_id)";
                
                // Preparing query.
                $statement = $db->prepare($query);

                // Getting the relative path of images and converting it to url friendly path.
                $medium_path = str_replace(DIRECTORY_SEPARATOR, '/', $new_path_medium);
                $small_path = str_replace(DIRECTORY_SEPARATOR, '/', $new_path_small);

                // Binding values.
                $statement->bindValue(":medium_path", $medium_path, PDO::PARAM_STR);
                $statement->bindValue(":small_path", $small_path, PDO::PARAM_STR);
                $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);

                // Executing the query.
                $statement->execute();    
                header("Location: Plans.php");
        }
        else{
            $error = "The uploaded file is not an image.";
        }
    }
    else{
        // Inserting just the plan record if no image is uploaded.

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
            $statement->execute();
        }
        header("Location: Plans.php");
        
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
    <?php if($error):?>
        <p class="error"><?= $error ?></p>
    <?php endif ?>
    <h1>Create a New Plan</h1>

    <form method = "post" class = "pageForm" enctype="multipart/form-data">
        <fieldset>
            <div class="formSeparator">
                <label for="title">Title</label>
                <input type="text" id = "title" name = "title">
            </div>
            <div class="formSeparator">
                <label for="price">Price</label>
                <input type="number" id = "price" name = "price" step = ".01" >
            </div>
            <div class="formSeparator">
                <label for="bgcolour">Background</label>
                <input type="color" id = "bgcolour" name = "bgcolour" value = "#000000">
            </div>
            <div class="formSeparator">
                <label for="colour">Text Colour</label>
                <input type="color" id = "colour" name = "colour" value = "#FFFFFF">
            </div>
            <div class="formSeparator">
                <label for="image">Upload Image here:</label>
                <input type="file" id="image" name="image">
            </div>
            <div class="formSeparator">
                <label for="plan_category_id">Category:</label>
                <select name="plan_category_id" id="plan_category_id">
                    <option value="NULL">None</option>
                    <?php foreach($planCategoryResults as $planCategoryResult): ?>
                    <option value="<?= $planCategoryResult['plan_category_id'] ?>"><?= $planCategoryResult['plan_category_name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="formSeparator">
                <label for="summernote">Description</label>
                <textarea id = "summernote" name = "description"></textarea>
            </div>
            <div class="formSeparator">
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