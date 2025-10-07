<?php
// session
session_start();

// database connection
include 'database/connection.php';


// register function
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $suffix = $_POST['suffix'] ?? null;
    $gender = $_POST['gender'] ?? '';
    $civil_status = $_POST['civil_status'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $birthplace = $_POST['birthplace'] ?? '';
    $is_working = $_POST['is_working'] ?? 3;
    $school = $_POST['school'] ?? null;
    $occupation = $_POST['occupation'] ?? null;
    $barangay_address = $_POST['barangay_address'] ?? null;
    $street = $_POST['street'] ?? '';
    $purok = $_POST['purok'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = sha1($_POST['password']);
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $created_at = $updated_at = date('Y-m-d H:i:s');

    $valid_id_path = null;
    if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['valid_id']['tmp_name'];
        $fileName = basename($_FILES['valid_id']['name']);

        // Remove leading numbers and underscores before the actual name
        $realName = preg_replace('/^[0-9_]+/', '', $fileName);

        $targetDir = 'public/valid_id/';
        $finalFileName = time() . '_' . $realName;
        $targetFile = $targetDir . $finalFileName;

        if (move_uploaded_file($fileTmp, $targetFile)) {
            $valid_id_path = $finalFileName;  // Save only: timestamp_real_name.jpg
        } else {
            $_SESSION['error'] = "Failed to upload Valid ID.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $_SESSION['error'] = "Valid ID file is required.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }


    $stmt = $conn->prepare("INSERT INTO tbl_residents (
        first_name, middle_name, last_name, suffix, gender, civil_status, date_of_birth,
        birthplace, is_working, school, occupation, barangay_address, street, purok,
        username, password, email, valid_id, phone_number, is_approved,
        is_online, created_at, updated_at
    ) VALUES (
        :first_name, :middle_name, :last_name, :suffix, :gender, :civil_status, :date_of_birth,
        :birthplace, :is_working, :school, :occupation, :barangay_address, :street, :purok,
        :username, :password, :email, :valid_id, :phone_number, 0,
        'offline', :created_at, :updated_at
    )");

    $stmt->execute([
        ':first_name' => $first_name,
        ':middle_name' => $middle_name,
        ':last_name' => $last_name,
        ':suffix' => $suffix,
        ':gender' => $gender,
        ':civil_status' => $civil_status,
        ':date_of_birth' => $date_of_birth,
        ':birthplace' => $birthplace,
        ':is_working' => $is_working,
        ':school' => $school,
        ':occupation' => $occupation,
        ':barangay_address' => $barangay_address,
        ':street' => $street,
        ':purok' => $purok,
        ':username' => $username,
        ':password' => $password,
        ':email' => $email,
        ':valid_id' => $valid_id_path,
        ':phone_number' => $phone_number,
        ':created_at' => $created_at,
        ':updated_at' => $updated_at,
    ]);

    $resident_id = $conn->lastInsertId();
    $dob = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $dob->diff($today)->y;

    $stmtFamily = $conn->prepare("INSERT INTO tbl_residents_family_members (
    resident_id, barangay_address, street, first_name, middle_name, last_name, suffix, purok, relationship, gender, civil_status,
    date_of_birth, birthplace, age, is_working, is_approved,
    is_barangay_voted, years_in_barangay, phone_number, philhealth_number, school, occupation
) VALUES (
    :resident_id, :barangay_address, :street, :first_name, :middle_name, :last_name, :suffix, :purok, :relationship, :gender, :civil_status,
    :date_of_birth, :birthplace, :age, :is_working, 0,
    :is_barangay_voted, :years_in_barangay, :phone_number, NULL, :school, :occupation
)");


    //
    $stmtFamily->execute([
        ':resident_id' => $resident_id,
        ':barangay_address' => $barangay_address,
        ':street' => $street,
        ':first_name' => $first_name,
        ':middle_name' => $middle_name,
        ':last_name' => $last_name,
        ':suffix' => $suffix,
        ':purok' => $purok,
        ':relationship' => 'Account Owner',
        ':gender' => $gender,
        ':civil_status' => $civil_status,
        ':date_of_birth' => $date_of_birth,
        ':birthplace' => $birthplace,
        ':age' => $age,
        ':is_working' => $is_working,
        ':is_barangay_voted' => 0,
        ':years_in_barangay' => 0,
        ':phone_number' => $phone_number,
        ':school' => $school,
        ':occupation' => $occupation
    ]);



    $_SESSION['success'] = "Registration successfully you can now logged in to proceed in the next step";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// get barangay list
$stmt = $conn->query("SELECT * FROM tbl_barangay ORDER BY id DESC");
$barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iBayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon-->
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/login.css">
    <!-- Select -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Sweetalert -->
    <link rel="stylesheet" href="assets/css/sweetalert.css">

    <style>
        .lgu-logo-wrapper {
            background-origin: content-box;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            background-image: url(images/logo.png);
            width: 70px;
            height: 70px;
            margin-top: 5px;
            float: left;
            margin-right: 20px;
        }
    </style>
</head>

<body>

    <?php include 'components/navbar.php' ?>

    <div class="user-form" style="padding: 20px;">
        <form id="w0" action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="container-fluid">
                <div class="row">
                    <div class="">
                        <h3 class="bold mb-3 mt-3" style="font-weight: 900;">
                            <i class="fas fa-user"></i> REGISTER ACCOUNT
                        </h3>
                        <div class="row">
                            <!-- Registrant Profile  -->
                            <div class="col-md-4 mb-3">
                                <div class="card card-primary" style="border-top-width: 5px; border-color: #0036C5;">
                                    <div class="card-body">
                                        <h6 class="card-title bold text-primary mb-3" style="color: #0036C5 !important; font-weight: 900;">
                                            <i class="fas fa-id-card-alt"></i> REGISTRANT PROFILE
                                        </h6>
                                        <div class="mb-3 field-sysuser-sys_firstname required">
                                            <label class="form-label" for="sysuser-sys_firstname">First Name <span style="color: red;">*</span></label>
                                            <input type="text" id="sysuser-sys_firstname" class="form-control text-input" style="font-weight: 900" name="first_name" maxlength="500" placeholder="First name" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3 field-sysuser-sys_middlename">
                                            <label class="form-label" for="sysuser-sys_middlename">Middle Name</label>
                                            <input type="text" id="sysuser-sys_middlename" class="form-control text-input" style="font-weight: 900" name="middle_name" maxlength="500" placeholder="Middle name" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3 field-sysuser-sys_lastname required">
                                            <label class="form-label" for="sysuser-sys_lastname">Last Name <span style="color: red;">*</span></label>
                                            <input type="text" id="sysuser-sys_lastname" class="form-control text-input" style="font-weight: 900" name="last_name" maxlength="500" placeholder="Last name" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3 field-sysuser-sys_ext_name">
                                            <label class="form-label" for="sysuser-sys_ext_name">Extension Name</label>
                                            <input type="text" id="sysuser-sys_ext_name" class="form-control text-input" style="font-weight: 900" name="suffix" maxlength="50" placeholder="e.g. Jr, III">
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="row" style="margin-bottom: 20px;">
                                            <div class="col-md-12">
                                                <label><strong>Gender</strong> <span style="color: red;">*</span></label><br>
                                                <input type="radio" name="gender" value="Male" id="male" checked>
                                                <label for="male">Male</label>

                                                <input type="radio" name="gender" value="Female" id="female" style="margin-left: 15px;">
                                                <label for="female">Female</label>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-bottom: 20px;">
                                            <div class="col-md-6">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <label class="form-label">Date of birth <span style="color: red;">*</span></label>
                                                        <input type="date" style="font-weight: 900" class="form-control" name="date_of_birth" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <label class="form-label">Birthplace <span style="color: red;">*</span></label>
                                                        <input type="text" style="font-weight: 900" class="form-control" name="birthplace" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <div class="form-group form-float">
                                                <label class="form-label">Civil status <span style="color: red;">*</span></label>
                                                <select class="form-control select-form" name="civil_status" required>
                                                    <option value="" disabled selected>CHOOSE CIVIL STATUS</option>
                                                    <option value="single">Single</option>
                                                    <option value="married">Married</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-bottom: 20px;">
                                            <div class="col-md-12">
                                                <label><strong>Current Status</strong> <span style="color: red;">*</span></label><br>
                                                <input type="radio" name="is_working" value="1" id="working">
                                                <label for="working">Working</label>

                                                <input type="radio" name="is_working" value="2" id="student" style="margin-left: 15px;">
                                                <label for="student">Student</label>

                                                <input type="radio" name="is_working" value="3" id="none" style="margin-left: 15px;" checked>
                                                <label for="none">None</label>
                                            </div>
                                        </div>




                                        <!-- Occupation Input -->
                                        <div class="row" id="occupationDiv" style="display: none; margin-top: 10px;">
                                            <div class="col-md-12">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <label class="form-label">Occupation <span style="color: red;">*</span></label>
                                                        <input type="text" style="font-weight: 900" class="form-control" name="occupation">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- School Input -->
                                        <div class="row" id="schoolDiv" style="display: none; margin-top: 10px;">
                                            <div class="col-md-12">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <label class="form-label">School <span style="color: red;">*</span></label>
                                                        <input type="text" style="font-weight: 900" class="form-control" name="school">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3 field-sysuser-sys_barangay">
                                            <label class="form-label" for="sysuser-sys_barangay">Address <span style="color: red;">*</span></label>
                                            <select class="form-control select-form select2" name="barangay_address" required>
                                                <option value="" disabled selected>SELECT BARANGAY</option>
                                                <?php foreach ($barangays as $barangay): ?>
                                                    <option value="<?= $barangay['id']; ?>">
                                                        <?= strtoupper(htmlspecialchars($barangay['barangay_name'])) ?>
                                                    </option>
                                                <?php endforeach; ?>

                                            </select>
                                            <div class="mb-3 mt-3 field-sysuser-sys_street required">
                                                <div class="input-group">
                                                    <input type="text" id="sysuser-sys_street" style="font-weight: 900" class="form-control" name="street" maxlength="500" placeholder="Street" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="mb-3 mt-3 field-sysuser-sys_purok required">
                                                <div class="input-group">
                                                    <input type="text" id="sysuser-sys_purok" style="font-weight: 900" class="form-control" name="purok" maxlength="500" placeholder="Purok" required>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Details -->
                            <div class="col-md-4 mb-3">
                                <div class="card card-primary" style="border-top-width: 5px; border-color: #0036C5;">
                                    <div class="card-body">
                                        <h6 class="card-title bold text-primary mb-3" style="color: #0036C5 !important; font-weight: 900;">
                                            <i class="fas fa-user-cog"></i> ACCOUNT DETAILS
                                        </h6>
                                        <div class="mb-3 field-sysuser-sys_username required">
                                            <label class="form-label" for="sysuser-sys_username">Account <span style="color: red;">*</span></label>
                                            <input type="text" id="username" style="font-weight: 900" class="form-control" name="username" maxlength="500" placeholder="Username" required>
                                            <small id="username-feedback" class="text-danger"></small>

                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3 field-sysuser-sys_password required">

                                            <div class="input-group">
                                                <input type="password" id="sysuser-sys_password" style="font-weight: 900" class="form-control" name="password" maxlength="500" placeholder="Password" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="mb-3 field-sysuser-sys_email_address required">
                                            <label class="form-label" for="sysuser-sys_email_address">Email Address <span style="color: red;">*</span></label>
                                            <input type="text" id="email" style="font-weight: 900" class="form-control" name="email" maxlength="500" placeholder="Email address" required>
                                            <small id="email-feedback" class="text-danger"></small>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="mb-3 field-sysuser-sys_valid_id required">
                                            <label class="form-label" for="sysuser-sys_valid_id">Valid ID <span style="color: red;">*</span></label>
                                            <input type="file" id="sysuser-sys_valid_id" style="font-weight: 900" class="form-control" name="valid_id" maxlength="500" placeholder="Email address" required>

                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3 field-sysuser-sys_contact_number">
                                            <label class="form-label" for="sysuser-sys_contact_number">Phone number <span style="color: red;">*</span></label>
                                            <input type="text" id="sysuser-sys_contact_number" class="form-control text-input" style="font-weight: 900" name="phone_number" maxlength="500" placeholder="Phone number">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and conditions  -->
                            <div class="col-md-4 mb-3">
                                <div class="card card-primary" style="border-top-width: 5px; border-color: #0036C5;">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary bold mb-3" style="color: #0036C5 !important; font-weight: 900;">
                                            <i class="fas fa-user-check"></i> TERMS AND CONDITIONS
                                        </h6>
                                        <strong>Registration</strong>
                                        <ol>
                                            <li> The information provided is certified as true and correct. </li>
                                            <li> Registrant should validate their account by clicking the verification link sent
                                                to the supplied email address. </li>
                                            <li> Registrant should not create multiple false accounts. </li>
                                            <li> Registrant should keep their account credentials and will not share to anyone.
                                            </li>
                                        </ol>
                                        <hr>
                                        <strong>Privacy Notice</strong>
                                        <ol>
                                            <li>In compliance with Republic Act No. 10173, otherwise known as the Data Privacy Act of 2012, we are committed to protecting the personal information you provide. All data collected through this system will be used solely for legitimate and authorized purposes related to the services offered.</li>
                                            <li>Your personal information will be stored securely and accessed only by authorized personnel. We implement appropriate organizational, physical, and technical measures to safeguard your data against unauthorized access, alteration, disclosure, or destruction.</li>
                                            <li>We do not share or disclose any personal information to third parties without your consent, except when required by law or legal process.</li>
                                            <li>By submitting your information, you acknowledge that you have read and understood this Privacy Notice and consent to the collection and processing of your personal data as described.</li>
                                        </ol>
                                        <hr>
                                        <label for="isChecked">
                                            <input type="checkbox" id="isChecked" name="isChecked" value="check" required>
                                            <span style="margin: 0 auto"> <strong> I Accept the Terms and Conditions </strong></span>
                                        </label>
                                    </div>
                                </div>
                                <br>
                                <div class="float-end">
                                    <a class="btn bold" href="login.php" style="background-color: #D9D9D9; font-weight: 900;">BACK</a>
                                    <button type="submit" class="btn btn-primary bold"><strong>REGISTER</strong>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <br>
    </div>
    </div>
    <div>
        <a href="#" id="scroll-to-top" class="scroll-to-top"><i class="fas fa-angle-up"></i></a>
    </div>


    <div class="relative flex items-center justify-center d-md-none">
        <img class="mt-0 img-fluid" src="images/city-mobile.png" alt="" style="max-width: 100%; height: auto; color: transparent;">
    </div>
    <div class="relative flex items-center justify-center d-none d-md-block">
        <img class="mt-0 img-fluid" src="images/city-desktop.png" alt="" style="max-width: 100%; height: auto; color: transparent;">
    </div>

    <?php include 'components/footer.php' ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="assets/js/time.js"></script>
    <!-- Select Plugin Js -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Sweetalert -->
    <script src="assets/js/sweetalert.js"></script>
    <script>
        <?php if (isset($_SESSION['success'])): ?>
            swal({
                type: 'success',
                title: 'Success!',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            swal({
                type: 'error',
                title: 'Oops...',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>

    <script>
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>

    <script>
        $(document).ready(function() {
            let usernameValid = false;
            let emailValid = false;

            // Username Check
            $('#username').on('blur', function() {
                const username = $(this).val();
                const feedback = $('#username-feedback');
                if (username.length > 0) {
                    $.post('validation/check_useremail.php', {
                        username: username
                    }, function(response) {
                        if (response.status === 'exists') {
                            feedback.text(response.message).css('color', 'red');
                            usernameValid = false;
                            $('#username').addClass('is-invalid').removeClass('is-valid');
                        } else if (response.status === 'available') {
                            feedback.text('');
                            usernameValid = true;
                            $('#username').removeClass('is-invalid').addClass('is-valid');
                        } else {
                            feedback.text('');
                            usernameValid = false;
                            $('#username').removeClass('is-invalid is-valid');
                        }
                    }, 'json');
                } else {
                    feedback.text('');
                    usernameValid = false;
                    $('#username').removeClass('is-invalid is-valid');
                }
            });

            // Email Check
            $('#email').on('blur', function() {
                const email = $(this).val();
                const feedback = $('#email-feedback');
                if (email.length > 0) {
                    $.post('validation/check_useremail.php', {
                        email: email
                    }, function(response) {
                        if (response.status === 'exists') {
                            feedback.text(response.message).css('color', 'red');
                            emailValid = false;
                            $('#email').addClass('is-invalid').removeClass('is-valid');
                        } else if (response.status === 'available') {
                            feedback.text('');
                            emailValid = true;
                            $('#email').removeClass('is-invalid').addClass('is-valid');
                        } else {
                            feedback.text('');
                            emailValid = false;
                            $('#email').removeClass('is-invalid is-valid');
                        }
                    }, 'json');
                } else {
                    feedback.text('');
                    emailValid = false;
                    $('#email').removeClass('is-invalid is-valid');
                }
            });

            $('form.needs-validation').on('submit', function(e) {
                if (!usernameValid || !emailValid) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (!usernameValid) {
                        $('#username-feedback').text('Please provide a valid, available username.').css('color', 'red');
                        $('#username').addClass('is-invalid');
                    }

                    if (!emailValid) {
                        $('#email-feedback').text('Please provide a valid, available email.').css('color', 'red');
                        $('#email').addClass('is-invalid');
                    }
                }
            });
        });
    </script>


    <!-- CURRENT STATUS SCRIPT -->
    <script>
        document.querySelectorAll('input[name="is_working"]').forEach((elem) => {
            elem.addEventListener('change', function() {
                const occupationDiv = document.getElementById('occupationDiv');
                const schoolDiv = document.getElementById('schoolDiv');

                if (this.value === "1") {
                    occupationDiv.style.display = 'block';
                    schoolDiv.style.display = 'none';
                } else if (this.value === "2") {
                    occupationDiv.style.display = 'none';
                    schoolDiv.style.display = 'block';
                } else {
                    occupationDiv.style.display = 'none';
                    schoolDiv.style.display = 'none';
                }
            });
        });
    </script>

    <!-- SELECT SCRIPT -->
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                allowClear: true
            });
        });
    </script>

</body>

</html>