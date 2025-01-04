<?php
require('../user/connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../../login.php");
}

function generateSlug($word){
    return strtolower(str_replace(" ", "-", $word));
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

$image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
$upload_error = isset($_FILES['image']) && ($_FILES['image']['error'] > 0 && $_FILES['image']['error'] !== 4);


if($upload_error){
    $error = "We couldn't upload your image at the moment... Please try again later.";
}

$error="";

if($_POST){
    // Sanitizing user input from the form.
    $title = filter_input(INPUT_POST,"title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $colour = filter_input(INPUT_POST,"colour", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $bgcolour = filter_input(INPUT_POST,"bgcolour", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $content = filter_input(INPUT_POST,"content", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = $_POST['description'];
    $price = filter_input(INPUT_POST,"price", FILTER_VALIDATE_FLOAT);
    $plan_id = filter_input(INPUT_POST,"plan_id", FILTER_VALIDATE_INT);

    // Checking for a null value.
    if($_POST['plan_category_id'] === "NULL"){
        $plan_category_id = NULL;
    }
    else{
        $plan_category_id = filter_input(INPUT_POST,"plan_category_id", FILTER_VALIDATE_INT); 
    }

    // Validating if all inputs are correct, else redirect user to index.php.
    if(($title !== false) && ($description !== false) && ($plan_id !== false) && ($price !== false) && ($colour !== false) && ($bgcolour !== false) ){
        // Checking if edit button is clicked.
        if($_POST['action'] == "Update"){
            // Updating the specified record.
            if(!$upload_error && !empty($_POST["description"]) && !empty($_POST["title"]) && !empty($_POST["plan_id"]) && !empty($_POST["price"])){

                if($image_upload_detected){
                    $temp_file_path = $_FILES['image']['tmp_name'];
                    $file_name      = $_FILES['image']['name'];

                    $file_name = basename($file_name);
                        
                    if(file_is_an_image($temp_file_path, $file_name)){
                        // Removing the previous files from file system.

                        // Getting the filenames.
                        $query = "SELECT medium_path, small_path FROM images WHERE plan_id = :plan_id";

                        $statement = $db->prepare($query);

                        $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);

                        $statement->execute();

                        $image_paths = $statement->fetch();

                        $medium_path_old = $image_paths['medium_path'];
                        $small_path_old = $image_paths['small_path'];

                        // Converting the paths to be file system friendly.
                        $medium_path_old = str_replace('/', DIRECTORY_SEPARATOR, $medium_path_old);
                        $small_path_old = str_replace('/', DIRECTORY_SEPARATOR, $small_path_old);

                        if(file_exists($medium_path_old)){
                            unlink($medium_path_old);
                        }
                        else{
                            $error = "Couldn't remove the medium sized image.";
                        }

                        if(file_exists($small_path_old)){
                            unlink($small_path_old);
                        }
                        else{
                            $error = "Couldn't remove the small sized image.";
                        }
                        
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

                        
                        // Tried using just update but that doesn't account for new image inserts.
                        // Removing the previous image record.
                        $query = "DELETE FROM images
                                  WHERE plan_id = :plan_id";

                        $statement = $db->prepare($query);

                        $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);

                        // Executing the statement. Redirecting to plans.php if succeeded.
                        if($statement->execute()){
                            // If the deletion succeeded, inserting a new record.
                            // We won't get an error for deleting a record that doesn't exit.
                            $query = "INSERT INTO images(medium_path, small_path, plan_id)
                                      VALUES(:medium_path, :small_path, :plan_id)";
    
                            $statement = $db->prepare($query);

                            // Getting the relative path of images and converting it to url friendly path.
                            $medium_path = str_replace(DIRECTORY_SEPARATOR, '/', $new_path_medium);
                            $small_path = str_replace(DIRECTORY_SEPARATOR, '/', $new_path_small);
    
                            $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);
                            $statement->bindValue(":medium_path", $medium_path, PDO::PARAM_STR);
                            $statement->bindValue(":small_path", $small_path, PDO::PARAM_STR);
    
                            $statement->execute();
                        }


                        // Updating the plan record.
                        // Creating a query to update the data.
                        $query = "UPDATE plans 
                                  SET title = :title, 
                                      description = :description, 
                                      price = :price, 
                                      colour = :colour, 
                                      bgcolour = :bgcolour, 
                                      plan_category_id = :plan_category_id,
                                      slug = :slug
                                  WHERE plan_id = :plan_id LIMIT 1";
                    
                        // Preparing the query.
                        $statement = $db->prepare( $query );

                        // Generating slug.
                        $slug = generateSlug($title);
                    
                        // Binding values to the query.
                        $statement->bindValue(":title", $title, PDO::PARAM_STR);
                        $statement->bindValue(":description", $description, PDO::PARAM_STR);
                        $statement->bindValue(":colour", $colour, PDO::PARAM_STR);
                        $statement->bindValue(":bgcolour", $bgcolour, PDO::PARAM_STR);
                        $statement->bindValue(":slug", $slug, PDO::PARAM_STR);
                        $statement->bindValue(":price", $price);
                        $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);
                        $statement->bindValue(":plan_category_id", $plan_category_id);
                    
                        // Executing the statement. Redirecting to plans.php if succeeded.
                        if($statement->execute()){
                            header("Location: 1/plans");
                        }
                    }
                    else{
                        $error = "The uploaded file is not an image.";
                    }
                }
                else{
                    // Checking if delete image checkbox is selected.
                    if(isset($_POST['imageCheck'])){
                        // Removing the previous files from file system.

                        // Getting the filenames.
                        $query = "SELECT medium_path, small_path FROM images WHERE plan_id = :plan_id";

                        $statement = $db->prepare($query);

                        $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);

                        $statement->execute();

                        $image_paths = $statement->fetch();

                        $medium_path_old = $image_paths['medium_path'];
                        $small_path_old = $image_paths['small_path'];

                        // Converting the paths to be file system friendly.
                        $medium_path_old = str_replace('/', DIRECTORY_SEPARATOR, $medium_path_old);
                        $small_path_old = str_replace('/', DIRECTORY_SEPARATOR, $small_path_old);

                        if(file_exists($medium_path_old)){
                            unlink($medium_path_old);
                        }
                        else{
                            $error = "Couldn't remove the medium sized image.";
                        }

                        if(file_exists($small_path_old)){
                            unlink($small_path_old);
                        }
                        else{
                            $error = "Couldn't remove the small sized image.";
                        }

                        // Deleting the image record from the images table.
                        $query = "DELETE FROM images
                                  WHERE plan_id = :plan_id";

                        $statement = $db->prepare($query);

                        $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);

                        $statement->execute();
                    }

                    // Updating just the plan record if no image uploaded.
                    // Creating a query to update the data.
                    $query = "UPDATE plans 
                              SET title = :title, 
                                  description = :description, 
                                  price = :price, 
                                  colour = :colour, 
                                  bgcolour = :bgcolour, 
                                  plan_category_id = :plan_category_id,
                                  slug = :slug
                              WHERE plan_id = :plan_id LIMIT 1";
                    
                    // Preparing the query.
                    $statement = $db->prepare( $query );

                    // Generating slug.
                    $slug = generateSlug($title);
                    
                    // Binding values to the query.
                    $statement->bindValue(":title", $title, PDO::PARAM_STR);
                    $statement->bindValue(":description", $description, PDO::PARAM_STR);
                    $statement->bindValue(":colour", $colour, PDO::PARAM_STR);
                    $statement->bindValue(":bgcolour", $bgcolour, PDO::PARAM_STR);
                    $statement->bindValue(":slug", $slug, PDO::PARAM_STR);
                    $statement->bindValue(":price", $price);
                    $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);
                    $statement->bindValue(":plan_category_id", $plan_category_id);
                    
                    // Executing the statement. Redirecting to plans.php if succeeded.
                    if($statement->execute()){
                        header("Location: 1/plans");
                    }
                }
            }
            else{
                // Redirecting the user to the same page if title or description field is empty.
                header("Location: editPlan.php?plan_id={$plan_id}");
            }
        }
        // Checking if delete button is clicked.
        elseif($_POST['action'] == "Delete"){
            
            // Deleting images from the file system.
            // Getting the filenames.
            $query = "SELECT medium_path, small_path FROM images WHERE plan_id = :plan_id";
            
            $statement = $db->prepare($query);
            
            $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);
            
            $statement->execute();
            
            $image_paths = $statement->fetch();
            
            $medium_path_old = $image_paths['medium_path'];
            $small_path_old = $image_paths['small_path'];

            // Converting the paths to be file system friendly.
            $medium_path_old = str_replace('/', DIRECTORY_SEPARATOR, $medium_path_old);
            $small_path_old = str_replace('/', DIRECTORY_SEPARATOR, $small_path_old);
            
            if(file_exists($medium_path_old)){
                unlink($medium_path_old);
            }
            else{
                $error = "Couldn't remove the medium sized image.";
            }
            
            if(file_exists($small_path_old)){
                unlink($small_path_old);
            }
            else{
                $error = "Couldn't remove the small sized image.";
            }
            
            // Creating a query to delete the specified field.
            $query = "DELETE FROM plans WHERE plan_id = :plan_id LIMIT 1";
    
            // Preparing the query.
            $statement = $db->prepare( $query );
    
            // Binding values.
            $statement->bindValue(":plan_id", $plan_id, PDO::PARAM_INT);
    
            // No need for a query to delete the images record
            // because we have ON DELETE CASCADE on foreign key.

            //Executing the query.
            if($statement->execute()){
                header("Location: 1/plans");
            }
        }
    }
    else{
        // Showing error to the user.
        $error = "There is a validation error in your data.";
    }
}
elseif (isset($_GET['plan_id'])){
    // Validating the plan_id entered by the user.
    $plan_id = filter_input(INPUT_GET,'plan_id', FILTER_VALIDATE_INT);
    if($plan_id){
        // Creating a query to select the specified record from the blogs table based on plan_id.
        $query = "SELECT p.plan_id, p.title, p.price, p.description, p.colour, 
                  p.bgcolour, p.plan_category_id, i.image_id, i.medium_path, i.small_path
                  FROM plans p LEFT JOIN images i ON p.plan_id = i.plan_id 
                  WHERE p.plan_id = :plan_id LIMIT 1";
    
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
    <base href="/wd2/Final%20Project/HealthMetLife%20-%20Improved/admin/">
    <title>Add General Page</title>
    <link rel="stylesheet" href="http://localhost:31337/wd2/Final%20Project/HealthMetLife%20-%20Improved/style.css">
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
</head>
<body>
    <?php require('header.php') ?>
    <div id="wrapper">
        <?php require('adminNav.php') ?>
        <main>
            <?php if($error): ?>
                <p class = "error"><?= $error ?></p>
            <?php else: ?>
                <?php if($result):?> 
                <h1>Edit a Plan</h1>

                <form method = "post" class = "pageForm" enctype="multipart/form-data">
                    <fieldset>
                    <input type="hidden" name = "plan_id" value = "<?= $result['plan_id'] ?>">
                    <div class="formSeparator">
                        <label for="title">Title</label>
                        <input type="text" id = "title" name = "title" value = "<?= $result['title'] ?>">
                    </div>
                    <div class="formSeparator">
                        <label for="price">Price</label>
                        <input type="number" id = "price" name = "price" value = "<?= $result['price'] ?>" step = ".01">
                    </div>
                    <div class="formSeparator">
                        <label for="bgcolour">Background</label>
                        <input type="color" id = "bgcolour" name = "bgcolour" value = "<?= $result['bgcolour'] ?>">
                    </div>
                    <div class="formSeparator">
                        <label for="colour">Text Colour</label>
                        <input type="color" id = "colour" name = "colour" value = "<?= $result['colour'] ?>">
                    </div>
                    <?php if(isset($result['image_id'])):?>
                    <div class="formSeparator">
                        <label class="checkLabel" for="imageCheck">Delete the current image?</label>
                        <input type="checkbox" id="imageCheck" name="imageCheck">
                    </div>
                    <?php endif ?>
                    <div class="formSeparator">
                        <label for="image">Upload Image here:</label>
                        <input type="file" id="image" name="image">
                    </div>
                    <div class="formSeparator">
                        <label for="plan_category_id">Category:</label>
                        <select name="plan_category_id" id="plan_category_id">
                            <option value="NULL">None</option>
                            <?php foreach($planCategoryResults as $planCategoryResult): ?>
                            <option value="<?= $planCategoryResult['plan_category_id'] ?>" <?php if ($planCategoryResult['plan_category_id'] === $result['plan_category_id']) echo 'selected';?>><?= $planCategoryResult['plan_category_name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="formSeparator">
                        <label for="summernote">Description</label>
                        <textarea id = "summernote" name = "description"><?= $result['description'] ?></textarea>
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
    <script>
        $(document).ready(function() {
        $('#summernote').summernote();
        });
    </script>
</body>
</html>