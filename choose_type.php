<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iBayan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/home.css">
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

        .bordered-section {
            position: relative;
            border: 5px solid #B6771D;
            padding: 60px 20px 40px 20px;
            border-radius: 15px;
            margin-top: 40px;
        }

        .logo-circle {
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            background-color: white;
            border: 5px solid #B6771D;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .logo-circle img {
            max-width: 100px;
            max-height: 100px;
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .card-hover:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border-color: #B6771D;
        }
    </style>
</head>

<body>

    <?php include 'components/navbar.php' ?>

    <div class="container px-4 py-5" id="featured-3">
        <h2 class="pb-2 mb-3 text-center" style="color: #B6771D; font-size: 25px; font-weight: 900;">
            Resident System for Mataasnakahoy Barangays
        </h2>
        <h5 class="pb-2 mb-5 text-center" style="color: grey;">CHOOSE TYPE</h5>

        <div class="bordered-section">
            <div class="logo-circle">
                <img src="images/logo.png" alt="Logo">
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4 text-center">
                <div class="col" onclick="window.location.href = 'login.php'">
                    <div class="p-4 card-hover text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-circle" style="font-size: 4rem; color: #B6771D;"></i>
                        </div>
                        <h5 class="mb-0">Resident</h5>
                    </div>
                </div>

                <div class="col" onclick="window.location.href = 'admin/login.php'">
                    <div class="p-4 card-hover text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-badge" style="font-size: 4rem; color: #B6771D;"></i>
                        </div>
                        <h5>Brgy. Official</h5>
                    </div>
                </div>

                <div class="col" onclick="window.location.href = 'superadmin/login.php'">
                    <div class="p-4 card-hover text-center">
                        <div class="mb-3">
                            <i class="bi bi-shield-lock" style="font-size: 4rem; color: #B6771D;"></i>
                        </div>
                        <h5>Admin</h5>
                    </div>
                </div>

            </div>
        </div>
    </div>




    <div class="relative flex items-center justify-center d-md-none">
        <img class="mt-0 img-fluid" src="images/city-mobile.png" alt="" style="max-width: 100%; height: auto; color: transparent;">
    </div>
    <div class="relative flex items-center justify-center d-none d-md-block">
        <img class="mt-0 img-fluid" src="images/city-desktop.png" alt="" style="max-width: 100%; height: auto; color: transparent;">
    </div>

    <?php include 'components/footer.php' ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="assets/js/time.js"></script>

</body>

</html>