<?php
    require('../connect.php');
    require('authenticate.php');
    // Writing a query that pulls all users from the users table.
    $query = "SELECT * FROM users ORDER BY username ASC";

    // Loading the query into the MySql server cache.
    $statement = $db->prepare($query);

    // Execute the query.
    $statement->execute();

    $userResults = $statement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List All Pages</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require('header.php') ?>
    <?php if (isset($userResults)): ?>
        <table id="users">
            <caption>Users</caption>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userResults as $userResult):?>
                    <tr>
                        <td><?=$userResult['username']?></td>
                        <td><?=$userResult['email']?></td>
                        <td><a href="<?= "editUser.php?user_id=" . $userResult['user_id']?>">edit</a></td>
                    </tr>
                <?php endforeach?>
            </tbody>
        </table>
        <div class="centerText">
            <a href="register.php" class="aButton">Create new User</a>
        </div>
    <?php else: ?>
        <h1 class="error">Sorry, there are no users to view.</h1>
    <?php endif ?>
    
    
</body>
</html>