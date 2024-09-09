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
            <a href="signin.php"><- Back</a>
        </div>
    </div>

    <!----Register Form ----->
    <div class="signin-register-container">

        <h1>Register</h1>
        <p>Already have an account? <a href="signin.php">Sign in!</a></p>

        <form action="includes/register-inc.php" method="post">
            <input type="text" name="username" placeholder="Username">
            <input type="password" name="password" placeholder="Password">
            <input type="password" name="confirmPassword" placeholder="Confirm password">
            <button type="submit" name="submit">REGISTER</button>
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
        } elseif ($error == 'invalidusername') {
            echo "<script>
                swal({
                    title: 'Invalid username!',
                    text: 'The username you've selected is invalid!',
                    icon: 'error',
                    button: 'Try Again',
                });
            </script>";
        } elseif ($error == 'usernametaken') {
            echo "<script>
                swal({
                    title: 'Username taken!',
                    text: 'That username already exists!',
                    icon: 'error',
                    button: 'Try Again',
                });
            </script>";
        } elseif ($error == 'passwordsdonotmatch') {
            echo "<script>
                swal({
                    title: 'Passwords do not match!',
                    text:'Your passwords do not match!',
                    icon: 'error',
                    button: 'Try again',
                });
            </script>";
        }
    } elseif (isset($_GET['success']) && $_GET['success'] == 'registered') {
        echo "<script>
            swal({
                title: 'Success!',
                text: 'You have successfully Registered!',
                icon: 'success',
                button: 'Continue',
            }).then(function() {
                window.location.href = 'signin.php';
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