<?php
require('connect.php');
require('authenticate.php');

$error="";

if($_POST){

    // Sanitizing user input from the form.
    $title = filter_input(INPUT_POST,"title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $content = filter_input(INPUT_POST,"content", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $content = $_POST['content'];
    $page_id = filter_input(INPUT_POST,"page_id", FILTER_VALIDATE_INT);

    // Validating if all inputs are correct, else redirect user to index.php.
    if(($title !== false) && ($content !== false) && ($page_id !== false)){
        // Checking if edit button is clicked.
        if($_POST['action'] == "Update"){
            // Updating the specified record.
            if(!empty($_POST["content"]) && !empty($_POST["title"] && !empty($_POST["page_id"]))){
            
                    // Creating a query to update the data.
                    $query = "UPDATE genericpages SET title = :title, content = :content WHERE page_id = :page_id LIMIT 1";
                
                    // Preparing the query.
                    $statement = $db->prepare( $query );
                
                    // Binding values to the query.
                    $statement->bindValue(":title", $title, PDO::PARAM_STR);
                    $statement->bindValue(":content", $content, PDO::PARAM_STR);
                    $statement->bindValue(":page_id", $page_id, PDO::PARAM_INT);
                
                    // Executing the statement. Redirecting to index.php if succeeded.
                    if($statement->execute()){
                        header("Location: index1.php?page_id={$page_id}");
                    }
            }
            else{
                // Redirecting the user to the same page if title or content field is empty.
                header("Location: editGeneralPage.php?page_id={$page_id}");
            }
        }
        // Checking if delete button is clicked.
        else if($_POST['action'] == "Delete"){
            // Creating a query to delete the specified field.
            $query = "DELETE FROM genericpages WHERE page_id = :page_id LIMIT 1";

            // Preparing the query.
            $statement = $db->prepare( $query );

            // Binding values.
            $statement->bindValue(":page_id", $page_id, PDO::PARAM_INT);

            //Executing the query and redirecting to index.php if succeeded.
            if($statement->execute()){
                header("Location: index1.php");
            }
        }
        exit;
    }
    else{
        // Showing error to the user.
        $error = "There is a validation error in your data.";
    }
}
else if (isset($_GET['page_id'])){
    // Validating the page_id entered by the user.
    $page_id = filter_input(INPUT_GET,'page_id', FILTER_VALIDATE_INT);
    if($page_id){
        // Creating a query to select the specified record from the blogs table based on page_id.
        $query = "SELECT * FROM genericpages WHERE page_id = :page_id LIMIT 1";
    
        // Preparing the query.
        $statement = $db->prepare($query);
      
        //Sanitizing input from the get superglobal.
        $page_id = filter_input(INPUT_GET,"page_id", FILTER_VALIDATE_INT);
      
        //Binding values to the query.
        $statement->bindValue(":page_id", $page_id, PDO::PARAM_INT);
      
        // Executing the query.
        $statement->execute();
      
        // Fetching the returned row.
        $result = $statement->fetch();
    }
    // If page_id is non-numeric, redirecting user to index.php.
    else{
        header("Location: index1.php");
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
    <?php require('header.php') ?>
    <?php if($error): ?>
        <p class = "error"><?= $error ?></p>
    <?php else: ?>
        <?php if(isset($result)):?> 
        <h1 class = "centerText">Edit a General Page</h1>

        <form method = "post">
            <fieldset>
            <input type="hidden" name = "page_id" value = "<?= $result['page_id'] ?>">
                <div id="formSeparator">
                    <label for="title">Title</label>
                    <input type="text" id = "title" name = "title" value = "<?= $result['title'] ?>">
                </div>
                <div id="formSeparator">
                    <label for="summernote">Content</label>
                    <textarea id = "summernote" name = "content"><?= $result['content'] ?></textarea>
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