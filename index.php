<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter System</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="" href="styles.css">
</head>

<body>

    <!------- Navbar ------>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">

        <a class="navbar-brand" href="#">
            <?php
            echo '<img src="includes/imgs/logo.png" alt="My Image">';
            ?>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">Blogs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="signin.php">Sign In</a>
                </li>
            </ul>

        </div>

    </nav>

    <!-------- Main Content/Container ------->
    <div class="container" style="margin-top: 20px;">

        <div class="row">
            <?php
            // Database connection
            require 'includes/database.php';

            // Fetching all blogs
            $sql = "SELECT * FROM blogs ORDER BY created_at DESC";
            $result = mysqli_query($conn, $sql);

            // Checking if there are any blogs
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $slug = $row['slug'];
                    $title = $row['title'];
                    $featured_image = $row['featured_image'];
                    $blog_information = $row['blog_information'];

                    // Displaying blog card
                    echo '
                    <div class="col-md-4 mb-4">

                        <div class="card" style="height: 100%; display: flex; flex-direction: column;">

                            <img src="dashboard/uploads/' . $featured_image . '" class="card-img-top" alt="' . htmlspecialchars($title) . '" style="object-fit: cover; height: 150px;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">' . htmlspecialchars($title) . '</h5>
                                <p class="card-text" style="flex-grow: 1; overflow: hidden; font-size: 0.875rem;">' . substr(strip_tags($blog_information), 0, 100) . '...</p>
                               <a href="dashboard/view_blog.php?slug=' . htmlspecialchars($slug) . '" class="btn btn-primary mt-auto read-more-btn" data-slug="' . htmlspecialchars($slug) . '">Read More</a>

                            </div>

                        </div>

                    </div>';
                }
            } else {
                echo '<p>No blogs available.</p>';
            }
            ?>
        </div>

        <!-- Bootstrap JS and dependencies -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script>
            // script to increment the blog view count.
            $(document).ready(function() {
                // Delegate the click event to dynamically added elements
                $(document).on('click', '.read-more-btn', function(event) {
                    event.preventDefault(); // Prevent the default link behavior

                    var slug = $(this).data('slug'); // Extract slug from data attribute
                    var url = $(this).attr('href'); // Get URL from href attribute

                    // Increment views count
                    $.ajax({
                        url: 'increment_views.php',
                        method: 'GET',
                        data: {
                            slug: slug
                        },
                        success: function(response) {
                            var result = JSON.parse(response);
                            if (result.status === 'success') {
                                // Redirect to the blog details page after incrementing views
                                window.location.href = url;
                            } else {
                                console.error(result.message);
                            }
                        },
                        error: function() {
                            console.error('Failed to increment views.');
                        }
                    });
                });
            });
        </script>

</body>

</html>