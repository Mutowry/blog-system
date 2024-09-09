<?php
session_start();
require '../includes/database.php';

// Ensuring the user is logged in
if (!isset($_SESSION['sessionUser'])) {
    header("Location: blogs.php?error=nouser");
    exit();
}

// Check if the blog_slug is set
if (isset($_POST['blog_slug'])) {
    $blogSlug = $_POST['blog_slug'];

    // Prepare and execute the delete statement
    $sql = "DELETE FROM blogs WHERE slug = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: blogs.php?error=sqlerror");
        exit();
    } else {
        // Binding parameter: 's' for string (slug)
        mysqli_stmt_bind_param($stmt, "s", $blogSlug);
        mysqli_stmt_execute($stmt);

        // Checking if the deletion was successful
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            header("Location: blogs.php?success=blogdeleted");
            exit();
        } else {
            header("Location: blogs.php?error=noblogfound");
            exit();
        }
    }
} else {
    header("Location: blogs.php?error=noslugprovided");
    exit();
}
