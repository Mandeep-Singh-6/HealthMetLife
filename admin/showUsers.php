<?php
    require('../user/connect.php');
    session_start();
if(!isset($_SESSION['login_role']) || $_SESSION['login_role'] !== 1){
    header("Location: ../login.php");
}

    // Writing a query that pulls all users from the users table.
    $query = "SELECT * FROM users WHERE username <> 'admin' ORDER BY username ASC";

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
    <!-- Importing google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include('adminNav.php') ?>
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
    </main>
    </div>
</body>
</html>