<?php
// session
session_start();

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

// if not logged in
if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

// details welcome
$barangay_name_key = "barangay_name_$barangay";
$admin_name_key = "admin_name_$barangay";
$admin_position_key = "admin_position_$barangay";

include '../../database/connection.php';

// Get barangay_id linked to admin_id
$admin_id = $_SESSION[$session_key];
$admin_stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$admin_stmt->execute([$admin_id]);
$admin_barangay_id = $admin_stmt->fetchColumn();

// If barangay_id not found, block
if (!$admin_barangay_id) {
    die('Barangay not found for this admin.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $announcement_title = $_POST['announcement_title'] ?? '';
    $announcement_content = $_POST['announcement_content'] ?? '';
    $announcement_venue = $_POST['announcement_venue'] ?? null;

    // Handle image upload if provided
    $announcement_image = null;
    if (isset($_FILES['announcement_image']) && $_FILES['announcement_image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['announcement_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['announcement_image']['name']);
        $destination = '../../public/announcement/' . $file_name;

        // Create folder if doesn't exist
        if (!is_dir('../../public/announcement')) {
            mkdir('../../public/announcement', 0755, true);
        }

        if (move_uploaded_file($file_tmp, $destination)) {
            $announcement_image = $file_name;
        }
    }

    // Insert announcement
    $stmt = $conn->prepare("INSERT INTO tbl_announcement 
        (announcement_title, announcement_content, announcement_venue, announcement_image, barangay, status, created_at)
        VALUES (?, ?, ?, ?, ?, 'active', NOW())");
    $stmt->execute([
        $announcement_title,
        $announcement_content,
        $announcement_venue,
        $announcement_image,
        $admin_barangay_id
    ]);

    // Redirect or show success message
    $_SESSION['success'] = "Announcement added successfully.";
    header("Location: announcements.php");
    exit();
}

// fetch announcements for this barangay
$stmt = $conn->prepare("SELECT * FROM tbl_announcement WHERE barangay = ? ORDER BY created_at DESC");
$stmt->execute([$admin_barangay_id]);
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>iBayan</title>
    <!-- Favicon-->
    <link rel="icon" href="../img/logo.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet"
        type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap Core Css -->
    <link href="../plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="../plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- JQuery DataTable Css -->
    <link href="../plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">

    <!-- Animation Css -->
    <link href="../plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="../plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">

    <link href="../css/themes/all-themes.css" rel="stylesheet" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

        body {
            font-family: 'Poppins', sans-serif !important;
        }

        .select-form {
            display: block !important;
            width: 100% !important;
            height: 34px !important;
            padding: 6px 12px !important;
            font-size: 14px !important;
            line-height: 1.42857143 !important;
            color: #555 !important;
            background-color: #fff !important;
            background-image: none !important;
            border: 1px solid #ccc !important;
            border-radius: 4px !important;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075) !important;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075) !important;
            -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s !important;
            -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s !important;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s !important;
        }

        /* HOMEPAGE */
        .thumbnail {
            background-color: #ffffff;
            border: 2px solid #B6771D;
            border-radius: 10px;
            padding: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .thumbnail:hover {
            background-color: #B6771D;
            color: #ffffff;
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .thumbnail:hover .icon-style,
        .thumbnail:hover h3 {
            color: #ffffff;
            transition: color 0.3s ease;
        }

        .icon-style {
            transition: color 0.3s ease;
        }

        .swal-wide {
            width: 600px !important;
            font-family: 'Poppins', sans-serif;
            padding: 30px;
        }

        .swal-title {
            font-size: 28px !important;
            font-weight: 600;
            color: #B6771D;
        }

        .swal-input {
            font-size: 18px;
            padding: 12px;
            border-radius: 6px;
        }

        .swal-confirm-btn {
            background-color: #B6771D !important;
            font-size: 16px;
            padding: 10px 24px;
            border-radius: 6px;
        }

        .swal-cancel-btn {
            background-color: #aaa !important;
            font-size: 16px;
            padding: 10px 24px;
            border-radius: 6px;
        }
    </style>
</head>

<body class="theme-teal">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-teal">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a id="app-title" style="display:flex;align-items:center;" class="navbar-brand" href="index.php">
                    <img id="bcas-logo" style="width:45px;display:inline;margin-right:10px;" src="../img/logo.png" />
                    <div>
                        <div style="color: white;">iBayan</div>
                    </div>
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <!-- #END# Tasks -->
                    <li class="pull-right"><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i
                                class="material-icons">account_circle</i></a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <?php include('left_sidebar.php') ?>
        <?php include('right_sidebar.php') ?>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <ol style="font-size: 15px;" class="breadcrumb breadcrumb-col-red">
                    <li><a href="index.php"><i style="font-size: 20px;" class="material-icons">home</i>
                            Dashboard</a></li>
                    <li class="active"><i style="font-size: 20px;" class="material-icons">description</i> Announcements
                    </li>
                </ol>
            </div>
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>ADD ANNOUNCEMENT</h2>
                        </div>
                        <div class="body">
                            <form id="form_validation" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <!-- LEFT COLUMN -->
                                    <div class="col-md-12 pr-4">
                                        <!-- hidden input -->
                                        <input type="hidden" name="barangay" value="">
                                        <input type="hidden" name="zip" value="4223">
                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="announcement_title" required>
                                                <label class="form-label">Announcement Title <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <textarea style="padding: 5px;" name="announcement_content" cols="30" rows="5" class="form-control" required></textarea>
                                                <label class="form-label">Content <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="announcement_venue">
                                                <label class="form-label">Venue</label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="file" class="form-control" name="announcement_image">
                                                <label class="form-label">Picture</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div style="display: flex; justify-content: end; gap: 5px; margin-top: 10px;">
                                    <button class="btn bg-teal waves-effect" type="submit"> + Save</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <!-- RIGHT CARD -->
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>ANNOUNCEMENT LIST</h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                    <thead>
                                        <tr>
                                            <th>Picture</th>
                                            <th>Title</th>
                                            <th>Content</th>
                                            <th>Venue</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($announcements as $announcement): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($announcement['announcement_image']): ?>
                                                        <img src="../../public/announcement/<?= htmlspecialchars($announcement['announcement_image']) ?>" style="width: 100px; height: auto;">
                                                    <?php else: ?>
                                                        <img src="../img/no_image.png" style="width: 100px; height: auto;">
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($announcement['announcement_title']) ?></td>
                                                <td><?= nl2br(htmlspecialchars($announcement['announcement_content'])) ?></td>
                                                <td><?= htmlspecialchars($announcement['announcement_venue']) ?></td>
                                                <td><?= nl2br(htmlspecialchars($announcement['status'])) ?></td>
                                                <td>
                                                    <a href="send_sms.php?id=<?= $announcement['id'] ?>" class="btn bg-teal waves-effect" style="margin-bottom: 5px;" id="sendSmsBtn" data-announcement-title="<?= htmlspecialchars($announcement['announcement_title']) ?>">
                                                        <i class="fa-solid fa-comment-sms"></i> SEND SMS
                                                    </a>

                                                    <a href="edit_announcement.php?id=<?= $announcement['id'] ?>" class="btn bg-teal waves-effect" style="margin-bottom: 5px;">
                                                        <i class="fa-solid fa-pen-to-square"></i> EDIT
                                                    </a>
                                                    <a href="delete_announcement.php?id=<?= $announcement['id'] ?>" onclick="return confirm('Are you sure you want to delete this announcement?')" class="btn bg-teal waves-effect" style="margin-bottom: 5px;">
                                                        <i class="fa-solid fa-trash"></i> DELETE
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->
        </div>
        </div>
    </section>

    <!-- Jquery Core Js -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Jquery Validation Plugin Css -->
    <script src="../plugins/jquery-validation/jquery.validate.js"></script>
    <script src="../js/pages/forms/form-validation.js"></script>
    <!-- Bootstrap Core Js -->
    <script src="../plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="../plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="../plugins/node-waves/waves.js"></script>

    <!-- Jquery DataTable Plugin Js -->
    <script src="../plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="../plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="../plugins/jquery-countto/jquery.countTo.js"></script>

    <!-- Morris Plugin Js -->
    <script src="../plugins/raphael/raphael.min.js"></script>
    <script src="../plugins/morrisjs/morris.js"></script>

    <!-- ChartJs -->
    <script src="../plugins/chartjs/Chart.bundle.js"></script>

    <!-- Flot Charts Plugin Js -->
    <script src="../plugins/flot-charts/jquery.flot.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.resize.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.pie.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.categories.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.time.js"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="../plugins/jquery-sparkline/jquery.sparkline.js"></script>

    <!-- Custom Js -->
    <script src="../plugins/chartjs/Chart.bundle.js"></script>

    <!-- Custom Js -->
    <script src="../js/admin.js"></script>
    <script src="../js/pages/tables/jquery-datatable.js"></script>
    <script src="../js/pages/charts/chartjs.js"></script>
    <script src="../js/pages/index.js"></script>

    <!-- Demo Js -->
    <script src="../js/demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('sendSmsBtn').addEventListener('click', function(e) {
            e.preventDefault();

            var announcementTitle = this.getAttribute('data-announcement-title');

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to send SMS with this announcement: \"" + announcementTitle + "\"?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                dangerMode: true,
                customClass: {
                    popup: 'swal-wide',
                    title: 'swal-title',
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = this.href;
                }
            });
        });

        <?php if (isset($_SESSION['success'])): ?>
            Swal.fire({
                title: 'Success!',
                text: '<?php echo $_SESSION['success']; ?>',
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal-wide',
                    title: 'swal-title',
                    confirmButton: 'swal-confirm-btn'
                }
            });
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            Swal.fire({
                title: 'Error!',
                text: '<?php echo $_SESSION['error']; ?>',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal-wide',
                    title: 'swal-title',
                    confirmButton: 'swal-confirm-btn'
                }
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>


</body>

</html>