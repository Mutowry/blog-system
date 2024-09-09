<?php
require_once 'includes/sidebar.php';
?>

<?php
require '../includes/database.php';

if (!isset($_SESSION['sessionUser'])) {
    header("Location: ../signin.php");
    exit();
}

$username = $_SESSION['sessionUser'];

// Fetch the user_id for the logged-in user
$sql_user = "SELECT user_id FROM users WHERE username = ?";
$stmt_user = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt_user, $sql_user)) {
    echo "SQL error.";
} else {
    mysqli_stmt_bind_param($stmt_user, "s", $username);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);

    if ($row_user = mysqli_fetch_assoc($result_user)) {
        $user_id = $row_user['user_id'];
    } else {
        echo "User not found.";
        exit();
    }
}

// Counting the number of blogs by the logged-in user
$sql = "SELECT COUNT(*) as blog_count FROM blogs WHERE user_id = ?";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo "SQL error.";
} else {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $blogCount = $row['blog_count'];
}

// Calculating total views
$sql_views = "SELECT SUM(views) as total_views FROM blogs WHERE user_id = ?";
$stmt_views = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt_views, $sql_views)) {
    echo "SQL error.";
} else {
    mysqli_stmt_bind_param($stmt_views, "i", $user_id);
    mysqli_stmt_execute($stmt_views);
    $result_views = mysqli_stmt_get_result($stmt_views);
    $row_views = mysqli_fetch_assoc($result_views);
    $totalViews = $row_views['total_views'];
}
?>

<!-------- Main Content/Container ----------->
<div class="main-content">

    <div class="top-bar">

        <div class="page-title">
            <h4 id="page-title">Dashboard</h4>
        </div>
        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#logoutModal">
            Logout
        </button>

    </div>

    <!---- Number of Blogs & Views ---->
    <div class="row">

        <div class="col-md-6 mb-4">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Blogs</h5>
                    <p class="card-text" id="blogs-count"><?php echo $blogCount; ?></p>
                </div>
            </div>

        </div>

        <div class="col-md-6 mb-4">

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Views</h5>
                    <p class="card-text" id="views-count"><?php echo $totalViews; ?></p>
                </div>
            </div>

        </div>

    </div>

    <!---- Latest Blogs Display Section ---->
    <div class="latest-blogs">

        <h3 class="latest-blogs-heading">My Latest Blogs</h3>
        <ul class="list-unstyled" id="latest-blogs-list">
            <?php
            // Fetching the top 5 blog titles
            $sql = "SELECT title, slug FROM blogs WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
            $stmt = mysqli_stmt_init($conn);

            if (!mysqli_stmt_prepare($stmt, $sql)) {
                echo "<li class='blog-item'>Error fetching blogs</li>";
            } else {
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $title = htmlspecialchars($row['title']);
                        $slug = htmlspecialchars($row['slug']);
                        echo "<li class='blog-item'>
                                <a href='view_blog.php?slug=" . urlencode($slug) . "'>$title</a>
                            </li>";
                    }
                } else {
                    echo "<li class='blog-item'>No blogs found</li>";
                }
            }
            ?>
        </ul>

    </div>

    <!---- Showing Full Blog Details Modal ---->
    <div class="modal fade" id="viewBlogModal" tabindex="-1" aria-labelledby="viewBlogModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header text-center">
                    <h5 class="modal-title" id="viewBlogModalLabel">Detailed Blog</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>

                <div class="modal-body text-center">
                    <h5 id="modalTitle" class="modal-title"></h5>
                    <img id="modalImage" src="" class="img-fluid mb-3" alt="Featured Image">
                    <div id="modalContent"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>

            </div>
        </div>

    </div>

    <script>
        // Showing Full Blog Details Modal Script
        document.addEventListener('DOMContentLoaded', function() {
            $('#viewBlogModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button that triggers the modal
                var slug = button.data('slug'); // Extracting info from data-* attributes

                // Fetching blog details using AJAX
                $.ajax({
                    url: 'fetch_blog.php',
                    method: 'GET',
                    data: {
                        slug: slug
                    },
                    success: function(response) {
                        var data = JSON.parse(response);
                        var modal = $('#viewBlogModal');
                        modal.find('#modalTitle').text(data.title);

                        var imagePath = 'uploads/' + data.featured_image;
                        modal.find('#modalImage').attr('src', imagePath);

                        modal.find('#modalContent').html(data.blog_information); // Render HTML
                    },
                    error: function() {
                        alert('Failed to fetch blog details.');
                    }
                });
            });
        });
    </script>

    <!---- Logout Confirmation Modal ---->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    Are you sure you want to log out?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="../index.php" class="btn btn-danger">Logout</a>
                </div>

            </div>

        </div>

    </div>

</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>