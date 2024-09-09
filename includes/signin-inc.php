<?php
session_start();
$_SESSION['sessionUserId'] = $userId; // Ensure $userId is correctly set after authentication


if (isset($_POST['submit'])) {

    require 'database.php';
    $username = $_POST['username'];
    $password = $_POST['password'];

    $_SESSION['sessionUserInput'] = $username; // Store the input username in session

    if (empty($username) || empty($password)) {
        header("Location: ../signin.php?error=emptyfields");
        exit();
    } else {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../signin.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $passCheck = password_verify($password, $row["password"]);
                if ($passCheck == false) {
                    header("Location: ../signin.php?error=wrongpass");
                    exit();
                } elseif ($passCheck == true) {
                    $_SESSION['sessionUser'] = $username; // The logged-in username
                    $_SESSION['sessionUserId'] = $user_id; // The user ID from the database
                    unset($_SESSION['sessionUserInput']); // Clear the username from session after successful login
                    header("Location: ../signin.php?success=loggedin");
                    exit();
                }
            } else {
                header("Location: ../signin.php?error=nouser");
                exit();
            }
        }
    }
} else {
    header("Location: ../register.php?error=accessforbidden");
    exit();
}
