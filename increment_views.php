<?php
// Start the session if not already started
session_start();

// Include the database connection
require 'includes/database.php';

// Check if slug is passed through the GET request
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];

    // Check if the blog exists
    $sql = "SELECT views FROM blogs WHERE slug = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo json_encode(['status' => 'error', 'message' => 'Error: Could not prepare statement.']);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, 's', $slug);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Increment the views count
            $update_sql = "UPDATE blogs SET views = views + 1 WHERE slug = ?";
            $update_stmt = mysqli_stmt_init($conn);

            if (mysqli_stmt_prepare($update_stmt, $update_sql)) {
                mysqli_stmt_bind_param($update_stmt, 's', $slug);
                if (mysqli_stmt_execute($update_stmt)) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update views.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error preparing update statement.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Blog not found.']);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No slug provided.']);
}
