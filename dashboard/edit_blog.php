<?php
session_start();
require '../includes/database.php';

function generateSlug($title, $conn)
{
    // Create a slug from the title
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($title)));
    $originalSlug = $slug;

    // Check if slug exists and increment
    $i = 1;
    while (true) {
        $sql = "SELECT COUNT(*) AS count FROM blogs WHERE slug=?";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $slug);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if ($row['count'] == 0) {
                break; // Slug is unique
            }

            // Increment slug if it already exists
            $slug = $originalSlug . '-' . $i;
            $i++;
        } else {
            echo "SQL error during slug generation.";
            exit();
        }
    }

    return $slug;
}

if (isset($_POST['submit'])) {
    // Fetch form data
    $currentSlug = $_POST['blog_slug']; // Store the original slug
    $title = $_POST['title'];
    $blog_information = $_POST['blog_information'];

    // Check if the title has changed
    $sql = "SELECT title FROM blogs WHERE slug=?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $currentSlug);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        // Update slug if title has changed
        if ($row['title'] !== $title) {
            $newSlug = generateSlug($title, $conn); // Generate new slug based on title
        } else {
            $newSlug = $currentSlug; // Keep the same slug if the title hasn't changed
        }
    } else {
        echo "SQL error while checking for title change.";
        exit();
    }

    // Update blog details with the new slug
    $sql = "UPDATE blogs SET title=?, blog_information=?, slug=? WHERE slug=? AND user_id=?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "SQL error while updating the blog.";
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "ssssi", $title, $blog_information, $newSlug, $currentSlug, $_SESSION['user_id']);
        if (!mysqli_stmt_execute($stmt)) {
            echo "Error executing update query.";
            exit();
        }
    }

    // Handling file upload if there's a new image
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['featured_image']['tmp_name'];
        $fileName = $_FILES['featured_image']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExts = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowedExts)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $sql = "UPDATE blogs SET featured_image=? WHERE slug=?";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo "SQL error while updating the featured image.";
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ss", $newFileName, $newSlug);
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "Error executing image update query.";
                        exit();
                    }
                }
            } else {
                echo "File upload failed.";
                exit();
            }
        }
    }

    // Redirect after successful update
    header("Location: blogs.php?success=blogupdated");
    exit();
} else {
    echo "Form was not submitted properly.";
}
