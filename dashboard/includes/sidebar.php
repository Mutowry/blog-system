<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>

    <!------ Sidebar ------>
    <div class="sidebar">

        <div class="logo">
            <?php
            echo '<img src="../includes/imgs/logo.png" alt="My Image">';
            ?>
        </div>

        <!--logged-in username-->
        <div class="user-info">
            <p>
                <?php
                session_start();
                echo $_SESSION['sessionUser'];
                ?>
            </p>
        </div>

        <nav class="nav-links">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="blogs.php" class="nav-link">Blogs</a>
        </nav>

    </div>


    <!--username styling-->
    <style>
        .sidebar .user-info {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .sidebar .user-info p {
            font-weight: bold;
            color: #f5976e;
        }
    </style>