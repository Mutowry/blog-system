<?php
require_once 'includes/sidebar.php';
require '../includes/database.php';

if (!isset($_SESSION['sessionUser'])) {
    header("Location: ../signin.php");
    exit();
}

// Getting the username of the logged-in user
$username = $_SESSION['sessionUser'];

// Fetching the user_id based on username
$sql_user_id = "SELECT user_id FROM users WHERE username = ?";
$stmt_user_id = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt_user_id, $sql_user_id)) {
    echo "SQL error.";
} else {
    mysqli_stmt_bind_param($stmt_user_id, "s", $username);
    mysqli_stmt_execute($stmt_user_id);
    $result_user_id = mysqli_stmt_get_result($stmt_user_id);
    $user_data = mysqli_fetch_assoc($result_user_id);
    $user_id = $user_data['user_id'];
}

// Fetching the blogs for the logged-in user
$sql = "SELECT * FROM blogs WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo "SQL error.";
} else {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Retrieve message and type from session
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';

// Clear message from session
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
    <!-- Include SweetAlert2 CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <!---------- Main Content ----------->
    <div class="main-content">

        <div class="top-bar">

            <div class="page-title">
                <h4 id="page-title">Blogs</h4> <!-- Default title for page -->
            </div>
            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#logoutModal"> <!-- Logout Button -->
                Logout
            </button>

        </div>


        <div class="text-right mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBlogModal"> <!-- Add Blog Button -->
                Add Blog
            </button>
        </div>

        <!------ Blog Cards Display------->
        <div class="container">

            <div class="row">

                <?php foreach ($blogs as $blog): ?>

                    <div class="col-md-4 mb-4">

                        <div class="card" style="height: 100%; display: flex; flex-direction: column;">

                            <img src="./uploads/<?php echo htmlspecialchars($blog['featured_image']); ?>" class="card-img-top" alt="Blog Image" style="object-fit: cover; height: 150px;">

                            <div class="card-body d-flex flex-column">

                                <h5 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                                <p class="card-text" style="flex-grow: 1; overflow: hidden; font-size: 0.875rem;">
                                    <?php echo htmlspecialchars(strip_tags(substr($blog['blog_information'], 0, 100))); ?>...
                                </p>

                                <div class="d-flex justify-content-between mt-2">

                                    <a href="view_blog.php?slug=<?php echo urlencode($blog['slug']); ?>" class="btn btn-primary btn-sm">
                                        Read More
                                    </a>


                                    <div>
                                        <a href="#" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#editBlogModal"
                                            data-slug="<?php echo htmlspecialchars($blog['slug']); ?>"
                                            data-title="<?php echo htmlspecialchars($blog['title']); ?>"
                                            data-image="./uploads/<?php echo htmlspecialchars($blog['featured_image']); ?>"
                                            data-content="<?php echo htmlspecialchars($blog['blog_information']); ?>">Edit</a>



                                        <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteBlogModal"
                                            data-slug="<?php echo htmlspecialchars($blog['slug'], ENT_QUOTES, 'UTF-8'); ?>">Delete</a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

    </div>

    <!------- Add Blog Modal -------->
    <div class="modal fade" id="addBlogModal" tabindex="-1" aria-labelledby="addBlogModalLabel" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <form action="add_blog.php" method="POST" id="addBlogForm" enctype="multipart/form-data">

                    <div class="modal-header">
                        <h5 class="modal-title" id="addBlogModalLabel">New Blog</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label for="blogTitle">Title</label>
                            <input type="text" class="form-control" id="blogTitle" name="title" placeholder="Title" required>
                        </div>

                        <div class="form-group">
                            <label for="featuredImage">Featured Image</label>
                            <input type="file" class="form-control-file" id="featuredImage" name="featured_image" required>
                        </div>

                        <div class="form-group">
                            <label for="blogContent">Story</label>
                            <textarea name="blog_information" id="editor" class="form-control" rows="6"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-primary">Save Blog</button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- Edit Blog Modal -->
    <div class="modal fade" id="editBlogModal" tabindex="-1" aria-labelledby="editBlogModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <form action="edit_blog.php" method="POST" id="editBlogForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editBlogModalLabel">Edit Blog</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="blog_slug" id="editBlogSlug"> <!-- Ensure correct name attribute -->
                        <div class="form-group">
                            <label for="editTitle">Blog Title</label>
                            <input type="text" class="form-control" id="editTitle" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="editImage">Featured Image</label><br>
                            <img id="currentImage" src="" alt="Featured Image" style="max-width: 100px;"><br><br>
                            <input type="file" class="form-control-file" id="editImage" name="featured_image">
                            <input type="hidden" name="current_image" id="currentImageHidden">
                        </div>
                        <div class="form-group">
                            <label for="editContent">Blog Content</label>
                            <textarea class="form-control" id="editContent" name="blog_information"></textarea> <!-- Ensure correct name attribute -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submit" class="btn btn-primary">Update Blog</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Delete Blog Modal -->
    <div class="modal fade" id="deleteBlogModal" tabindex="-1" role="dialog" aria-labelledby="deleteBlogModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="delete_blog.php"> <!-- Form starts here -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteBlogModalLabel">Confirm Deletion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this blog?
                        <input type="hidden" name="blog_slug" id="deleteBlogSlug" value=""> <!-- Hidden input to hold the slug -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">Delete</button> <!-- Submit button -->
                    </div>
                </form> <!-- Form ends here -->
            </div>
        </div>
    </div>

    <!------- Logout Confirmation Modal ------>
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

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!----- CKEditor Script for Add Blog Modal------>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            ClassicEditor
                .create(document.querySelector('#editor'))
                .then(editor => {
                    const form = document.getElementById('addBlogForm');
                    form.addEventListener('submit', function(event) {
                        // Get the data from the editor
                        const editorData = editor.getData();

                        // If the editor content is empty, prevent form submission
                        if (editorData.trim() === '') {
                            event.preventDefault();
                            alert('The blog content cannot be empty.');
                        } else {
                            // Update the textarea with the editor content
                            document.querySelector('#editor').value = editorData;
                        }
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        });
    </script>

    <script>
        //edit blog script
        document.addEventListener('DOMContentLoaded', function() {
            let editEditorInstance; // Keeping track of the editor instance

            $('#editBlogModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var slug = button.data('slug');
                var title = button.data('title');
                var image = button.data('image');
                var content = button.data('content');

                var modal = $(this);
                modal.find('#editBlogSlug').val(slug); // Ensure this matches the name attribute
                modal.find('#editTitle').val(title);
                modal.find('#currentImage').attr('src', image);

                // Destroying the editor instance if it already exists
                if (editEditorInstance) {
                    editEditorInstance.destroy();
                }

                // Initializing CKEditor for the edit modal
                ClassicEditor
                    .create(document.querySelector('#editContent'))
                    .then(editor => {
                        editEditorInstance = editor; // Storing the editor instance
                        editor.setData(content); // Setting the content in the editor

                        const form = document.getElementById('editBlogForm');
                        form.addEventListener('submit', function(event) {
                            const editorData = editEditorInstance.getData();
                            if (editorData.trim() === '') {
                                event.preventDefault();
                                alert('The blog content cannot be empty.');
                            } else {
                                document.querySelector('#editContent').value = editorData;
                            }
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });

            $('#editBlogModal').on('hide.bs.modal', function() {
                if (editEditorInstance) {
                    editEditorInstance.destroy();
                    editEditorInstance = null;
                }
            });
        });
    </script>

    <script>
        //delete blog script
        document.addEventListener('DOMContentLoaded', function() {
            // Handle the modal show event to pass the blog slug to the modal input
            $('#deleteBlogModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var blogSlug = button.data('slug');
                var modal = $(this);
                modal.find('#deleteBlogSlug').val(blogSlug);
            });

            // Handle the delete confirmation button click event
            document.querySelector('#confirmDeleteBtn').addEventListener('click', function() {
                var blogSlug = document.querySelector('#deleteBlogSlug').value;

                // Redirect to the delete_blog.php with the blog_slug in POST
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete_blog.php';

                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'blog_slug';
                input.value = blogSlug;
                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('error')) {
                const error = urlParams.get('error');
                let title, text;

                switch (error) {
                    case 'nouser':
                        title = 'Error!';
                        text = 'You must be signed in.';
                        break;
                    case 'sqlerror':
                        title = 'SQL Error!';
                        text = 'There was an issue with the database.';
                        break;
                    case 'fileerror':
                        title = 'File Upload Error!';
                        text = 'There was an issue with the file upload.';
                        break;
                    case 'emptyfields':
                        title = 'Oops!';
                        text = 'Please fill in all fields!';
                        break;
                    case 'noblogfound':
                        title = 'Error!';
                        text = 'No blog found or you are not authorized to delete this blog.';
                        break;
                    case 'noslugprovided':
                        title = 'Error!';
                        text = 'No blog slug provided.';
                        break;
                    default:
                        title = 'Error!';
                        text = 'An unknown error occurred.';
                }

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'error',
                    confirmButtonText: 'Okay'
                }).then(() => {
                    const newUrl = window.location.href.split('?')[0];
                    window.history.replaceState({}, document.title, newUrl);
                });
            } else if (urlParams.has('success')) {
                const success = urlParams.get('success');
                let title, text;

                switch (success) {
                    case 'blogadded':
                        title = 'Success!';
                        text = 'Blog added successfully.';
                        break;
                    case 'blogupdated':
                        title = 'Success!';
                        text = 'Blog updated successfully.';
                        break;
                    case 'blogdeleted':
                        title = 'Deleted!';
                        text = 'Your blog has been deleted successfully.';
                        break;
                    default:
                        title = 'Success!';
                        text = 'Operation completed successfully.';
                }

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'success',
                    confirmButtonText: 'Okay'
                }).then(() => {
                    const newUrl = window.location.href.split('?')[0];
                    window.history.replaceState({}, document.title, newUrl);
                });
            }
        });
    </script>

</body>

</html>