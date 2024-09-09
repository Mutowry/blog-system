<?php
session_start();
require '../includes/database.php';

// Ensuring the user is logged in
if (!isset($_SESSION['sessionUser'])) {
    header("Location: blogs.php?error=nouser");
    exit();
}

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $username = $_SESSION['sessionUser'];
    $blog_information = $_POST['blog_information'];

    if (empty($title) || empty($blog_information)) {
        header("Location: blogs.php?error=emptyfields");
        exit();
    }

    // Fetch user_id from users table based on username
    $userIdSql = "SELECT user_id FROM users WHERE username = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $userIdSql)) {
        header("Location: blogs.php?error=sqlerror");
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $user_id = $row['user_id'];
        } else {
            header("Location: blogs.php?error=sqlerror");
            exit();
        }
    }

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

            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
            $slugCheckSql = "SELECT COUNT(*) as count FROM blogs WHERE slug LIKE ?";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $slugCheckSql)) {
                header("Location: blogs.php?error=sqlerror");
                exit();
            } else {
                $slugWithSuffix = $slug;
                $suffix = 1;

                while (true) {
                    $likeSlug = $slugWithSuffix . '%';
                    mysqli_stmt_bind_param($stmt, "s", $likeSlug);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $row = mysqli_fetch_assoc($result);

                    if ($row['count'] == 0) {
                        break;
                    }
                    $slugWithSuffix = $slug . '-' . $suffix;
                    $suffix++;
                }

                // Move the uploaded file
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $sql = "INSERT INTO blogs (slug, title, user_id, featured_image, blog_information) VALUES (?, ?, ?, ?, ?)";
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        header("Location: blogs.php?error=sqlerror");
                        exit();
                    } else {
                        mysqli_stmt_bind_param($stmt, "sssss", $slugWithSuffix, $title, $user_id, $newFileName, $blog_information);
                        mysqli_stmt_execute($stmt);
                        header("Location: blogs.php?success=blogadded");
                        exit();
                    }
                } else {
                    header("Location: blogs.php?error=fileerror");
                    exit();
                }
            }
        } else {
            header("Location: blogs.php?error=fileerror");
            exit();
        }
    } else {
        header("Location: blogs.php?error=fileerror");
        exit();
    }
} else {
    header("Location: blogs.php?error=emptyfields");
    exit();
}
