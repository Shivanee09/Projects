<?php
include("session.php");
$exp_fetched = mysqli_query($con, "SELECT * FROM expenses WHERE user_id = '$userid'");

// Fetch the profile path from the database
$result = mysqli_query($con, "SELECT profile_path FROM users WHERE user_id = '$userid'");
$row = mysqli_fetch_assoc($result);
$profile_path = $row['profile_path'];

if (isset($_POST['save'])) {
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];

    $sql = "UPDATE users SET firstname = '$fname', lastname='$lname' WHERE user_id='$userid'";
    if (mysqli_query($con, $sql)) {
        echo "Records were updated successfully.";
    } else {
        echo "ERROR: Could not execute $sql. " . mysqli_error($con);
    }
    header('location: profile.php');
}

// Handling profile picture upload
if (isset($_POST['but_upload'])) {

    $name = $_FILES['file']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);

    // Select file type
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Valid file extensions
    $extensions_arr = array("jpg", "jpeg", "png", "gif");

    // Check extension
    if (in_array($imageFileType, $extensions_arr)) {

        // Insert record for profile picture
        $query = "UPDATE users SET profile_path = '$name' WHERE user_id='$userid'";
        mysqli_query($con, $query);

        // Upload file to server
        move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $name);

        // Update session profile path immediately after upload
        $_SESSION['profile_path'] = $name;

        // Refresh the page to reflect changes
        header("Refresh: 0");
    } else {
        echo "Invalid file extension.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Expense Manager - Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">

    <!-- Feather JS for Icons -->
    <script src="js/feather.min.js"></script>

    <style>
        .try {
            font-size: 28px;
            color: #333;
            padding: 15px 65px 5px 0px;
        }
    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <div class="border-right" id="sidebar-wrapper">
            <div class="user">
                <!-- Display user's uploaded profile picture -->
                <img class="img img-fluid rounded-circle"
                    src="uploads/<?php echo isset($_SESSION['profile_path']) ? $_SESSION['profile_path'] : 'default_profile.png'; ?>"
                    width="120">
                <h5><?php echo $username; ?></h5>
                <p><?php echo $useremail; ?></p>
            </div>
            <div class="sidebar-heading">Management</div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action">
                    <span data-feather="home"></span> Dashboard</a>
                <a href="add_expense.php" class="list-group-item list-group-item-action">
                    <span data-feather="plus-square"></span> Add Expenses</a>
                <a href="manage_expense.php" class="list-group-item list-group-item-action">
                    <span data-feather="dollar-sign"></span> Manage Expenses</a>
                <a href="expensereport.php" class="list-group-item list-group-item-action">
                    <span data-feather="file-text"></span> Expense Report</a>
            </div>
            <div class="sidebar-heading">Settings </div>
            <div class="list-group list-group-flush">
                <a href="profile.php" class="list-group-item list-group-item-action sidebar-active">
                    <span data-feather="user"></span> Profile</a>
                <a href="logout.php" class="list-group-item list-group-item-action">
                    <span data-feather="power"></span> Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light border-bottom">
                <button class="toggler" type="button" id="menu-toggle" aria-expanded="false">
                    <span data-feather="menu"></span>
                </button>
                <div class="col-md-12 text-center">
                    <h3 class="try">Update Profile</h3>
                </div>
            </nav>

            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <form class="form" method="post" action="" enctype='multipart/form-data'>
                            <div class="text-center mt-3">
                                <!-- Display the current profile picture -->
                                <img src="uploads/<?php echo isset($_SESSION['profile_path']) ? $_SESSION['profile_path'] : 'default_profile.png'; ?>"
                                    class="img img-fluid rounded-circle avatar" width="120" alt="Profile Picture">
                            </div>

                            <!-- Profile picture upload field -->
                            <div class="form-group text-center mt-3">
                                <input type="file" name="file" class="file-upload">
                                <button class="btn btn-primary mt-2" name="but_upload" type="submit">Upload Profile
                                    Picture</button>
                            </div>
                        </form>

                        <form class="form" action="" method="post" id="registrationForm" autocomplete="off">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="first_name">First name</label>
                                        <input type="text" class="form-control" name="first_name" id="first_name"
                                            placeholder="First Name" value="<?php echo $firstname; ?>">
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="last_name">Last name</label>
                                        <input type="text" class="form-control" name="last_name" id="last_name"
                                            value="<?php echo $lastname; ?>" placeholder="Last Name">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email"
                                    value="<?php echo $useremail; ?>" disabled>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-block btn-md btn-success" style="border-radius:0%;" name="save"
                                    type="submit">Save Changes</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Bootstrap core JavaScript -->
    <script src="js/jquery.slim.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/feather.min.js"></script>
    <script>
        $("#menu-toggle").click(function (e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
        });
        feather.replace();
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            var readURL = function (input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('.avatar').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }
            $(".file-upload").on('change', function () {
                readURL(this);
            });
        });
    </script>

</body>

</html>