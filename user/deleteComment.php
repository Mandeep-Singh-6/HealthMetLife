<?php
require('connect.php');
session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 2){
    header("Location: ../../login.php");
}

if (isset($_GET['comment_id'])){
    // Validating the comment_id entered by the user.
    $comment_id = filter_input(INPUT_GET,'comment_id', FILTER_VALIDATE_INT);
    if($comment_id){
        // Creating a query to select the specified record from the blogs table based on comment_id.
        $query = "SELECT c.*, p.slug
                  FROM comments c
                  JOIN plans p
                  ON p.plan_id = c.plan_id 
                  WHERE comment_id = :comment_id LIMIT 1";

        // Preparing the query.
        $statement = $db->prepare($query);
    
        //Binding values to the query.
        $statement->bindValue(":comment_id", $comment_id, PDO::PARAM_INT);
    
        // Executing the query.
        $statement->execute();
    
        // Fetching the returned row.
        $result = $statement->fetch();
    }
    // If comment_id is non-numeric, redirecting user to plans.php.
    else{
        header("Location: /wd2/Final%20Project/HealthMetLife/user/1/plans");
    }
    // Redirecting a user back if the comment doesn't belong to them.
    // or no comment found.
    if($result['user_id'] != $_SESSION['user_id'] || $result == false){
        header("Location: /wd2/Final%20Project/HealthMetLife/user/plans/" . $result['plan_id'] . "/" . $result['slug']);
    }
    else{
        $query = "DELETE from comments
                  WHERE comment_id = :comment_id";

        $statement = $db->prepare($query);

        $statement->bindValue(":comment_id", $comment_id, PDO::PARAM_INT);

        if($statement->execute()){
            header("Location: /wd2/Final%20Project/HealthMetLife/user/plans/" . $result['plan_id'] . "/" . $result['slug']);
        }
        
    }
}
?>