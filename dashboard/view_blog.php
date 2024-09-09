<?php
session_start();
// Ensure you have included your database connection file
require '../includes/database.php';

// Get the slug from the URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Check if the slug is empty
if (empty($slug)) {
    echo "Error: No slug provided.";
    exit();
}

// Fetch blog details from the database based on the slug
$sql = "SELECT title, featured_image, blog_information, views FROM blogs WHERE slug = ?";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo "Error: Could not prepare statement.";
    exit();
} else {
    mysqli_stmt_bind_param($stmt, "s", $slug);
    if (!mysqli_stmt_execute($stmt)) {
        echo "Error: Could not execute statement.";
        exit();
    }
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        // Prepare blog data for display
        $blog = $row;
        // Strip HTML tags while preserving line breaks and paragraph tags
        $cleaned_blog_information = strip_tags($blog['blog_information'], '<br><p>');
        // Convert new lines to <br> tags
        $formatted_blog_information = nl2br($cleaned_blog_information);
    } else {
        echo "Error: Blog not found.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        .blog-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .blog-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: bold;
        }

        .blog-image {
            display: block;
            margin: 0 auto 20px auto;
            max-width: 100%;
            height: 350px;
        }

        .blog-content {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="blog-container">
        <h1 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
        <?php if (!empty($blog['featured_image'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($blog['featured_image']); ?>" alt="Featured Image" class="blog-image">
        <?php endif; ?>
        <div class="blog-content">
            <?php echo $formatted_blog_information; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>