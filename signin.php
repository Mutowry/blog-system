<?php
session_start(); // Start the session

// Retrieve the stored username from the session, if available
$usernameValue = isset($_SESSION['sessionUserInput']) ? $_SESSION['sessionUserInput'] : "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body>
    <!------- Navbar ------>
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <?php
            echo '<img src="includes/imgs/logo.png" alt="My Image">';
            ?>
        </a>
    </nav>

    <div class="container">
        <div class="go-back">
            <a href="index.php"><- Back</a>
        </div>
    </div>

    <!------ Sign-In Form ----->
    <div class="signin-register-container">
        <h1>Sign in</h1>
        <p>No account? <a href="register.php">Register here!</a></p>
        <form action="includes/signin-inc.php" method="post">
            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($usernameValue); ?>"> <!-- Pre-fill username -->
            <input type="password" name="password" placeholder="Password" value=""> <!-- Always clear password -->
            <button type="submit" name="submit">LOGIN</button>
        </form>
    </div>

    <?php
    if (isset($_GET['error'])) {
        $error = $_GET['error'];
        if ($error == 'emptyfields') {
            echo "<script>
                swal({
                    title: 'Oops!',
                    text: 'Please fill in all fields!',
                    icon: 'warning',
                    button: 'Okay',
                });
            </script>";
        } elseif ($error == 'sqlerror') {
            echo "<script>
                swal({
                    title: 'SQL Error!',
                    text: 'There was an issue with the database.',
                    icon: 'error',
                    button: 'Okay',
                });
            </script>";
        } elseif ($error == 'wrongpass') {
            echo "<script>
                swal({
                    title: 'Wrong Password!',
                    text: 'The password you entered is incorrect.',
                    icon: 'error',
                    button: 'Try Again',
                });
            </script>";
        } elseif ($error == 'nouser') {
            echo "<script>
                swal({
                    title: 'No User Found!',
                    text: 'No account found with that username.',
                    icon: 'error',
                    button: 'Okay',
                });
            </script>";
        }
    } elseif (isset($_GET['success']) && $_GET['success'] == 'loggedin') {
        echo "<script>
            swal({
                title: 'Success!',
                text: 'You have successfully logged in!',
                icon: 'success',
                button: 'Continue',
            }).then(function() {
                window.location.href = 'dashboard/dashboard.php';
            });
        </script>";
    }

    // Clear the username session variable after displaying it
    unset($_SESSION['sessionUserInput']);
    ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>