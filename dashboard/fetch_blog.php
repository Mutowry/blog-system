<?php
require '../includes/database.php';

if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

    // Fetch blog details using the slug
    $sql = "SELECT title, featured_image, blog_information FROM blogs WHERE slug = ?";
    $stmt = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $slug);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            echo json_encode($row);
        } else {
            echo json_encode(["error" => "Blog not found."]);
        }
    } else {
        echo json_encode(["error" => "Database query failed."]);
    }
}
