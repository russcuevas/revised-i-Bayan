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
    </style>
</head>

<body>

    <?php include 'components/navbar.php' ?>

    <div class="container px-4 py-5" id="featured-3" style="margin-top: 20px;">
        <h2 class="pb-2 mb-3" style="color: #1a49cb; font-size: 25px; font-weight: 900; text-align: center;">Resident System for Mataasnakahoy Barangays</h2>
        <h5 class="pb-2 mb-5" style="color: grey; text-align: center">ENGAGE WITH US</h5>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4 mb-5">
            <div class="col">
                <div class="feature d-flex flex-column align-items-center justify-content-center text-center p-4"
                    style="background-color: #F2F6FE; min-height: 250px; border-radius: 8px;">
                    <div class="feature-icon text-white mb-3"
                        style="background-color: #1a49cb; font-size: 3rem; width: 5rem; 
                height: 5rem; display: flex; align-items: center; 
                justify-content: center; border-radius: 50%;">
                        <i class="bi bi-collection"></i>
                    </div>
                    <h5>Document Request</h5>
                </div>
            </div>

            <div class="col">
                <div class="feature d-flex flex-column align-items-center justify-content-center text-center p-4"
                    style="background-color: #F2F6FE; min-height: 250px; border-radius: 8px;">
                    <div class="feature-icon text-white mb-3"
                        style="background-color: #1a49cb; font-size: 3rem; width: 5rem; 
            height: 5rem; display: flex; align-items: center; 
            justify-content: center; border-radius: 50%;">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <h5>Secured Payment</h5>
                </div>
            </div>

            <div class="col">
                <div class="feature d-flex flex-column align-items-center justify-content-center text-center p-4"
                    style="background-color: #F2F6FE; min-height: 250px; border-radius: 8px;">
                    <div class="feature-icon text-white mb-3"
                        style="background-color: #1a49cb; font-size: 3rem; width: 5rem; 
                height: 5rem; display: flex; align-items: center; 
                justify-content: center; border-radius: 50%;">
                        <i class="bi bi-megaphone"></i>
                    </div>
                    <h5>Barangay Announcement</h5>
                </div>
            </div>
        </div>

        <!-- Centered Start Button -->
        <div class="text-center">
            <button type="button" onclick="window.location.href = 'login.php'" class="btn btn-primary px-4 py-2" style="background-color: #1a49cb; border: none; font-weight: 900;">
                CLICK HERE TO START
            </button>
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