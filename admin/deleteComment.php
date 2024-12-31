<?php
require('../user/connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../login.php");
}

if (isset($_GET['comment_id'])){
    // Validating the comment_id entered by the user.
    $comment_id = filter_input(INPUT_GET,'comment_id', FILTER_VALIDATE_INT);
    if($comment_id){

        // Creating a query to select the specified record from the blogs table based on comment_id.
        $query = "SELECT * FROM comments WHERE comment_id = :comment_id LIMIT 1";

        // Preparing the query.
        $statement = $db->prepare($query);

        //Binding values to the query.
        $statement->bindValue(":comment_id", $comment_id, PDO::PARAM_INT);

        // Executing the query.
        $statement->execute();

        // Fetching the returned row.
        $result = $statement->fetch();

        // Deleting the comment.
        $query = "DELETE from comments
                  WHERE comment_id = :comment_id";

        $statement = $db->prepare($query);

        $statement->bindValue(":comment_id", $comment_id, PDO::PARAM_INT);

        if($statement->execute()){
            header("Location: showPlan.php?plan_id=" . $result['plan_id']);
        }
    }
    // If comment_id is non-numeric, redirecting user to Plans.php.
    else{
        header("Location: Plans.php");
    }
}
?>