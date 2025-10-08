<?php
$barangay = basename(__DIR__);
$barangay_name = $_SESSION["barangay_name_$barangay"] ?? 'Barangay';
$resident_name = $_SESSION["resident_name_$barangay"] ?? 'Resident';
$is_approved = $_SESSION["is_approved_$barangay"] ?? 0;

$current_page = basename($_SERVER['PHP_SELF']);

// All pages related to Certificate Issuance (main, view, and request)
$certificate_pages = [
    // Main
    'certificate_issuance.php',
    'certificate_operate.php',
    'certificate_closure.php',
    'certificate_cedula.php',

    // View
    'certificates_view_information.php',
    'operate_view_information.php',
    'closure_view_information.php',
    'cedula_view_information.php',

    // Request
    'request_certificate.php',
    'request_operate.php',
    'request_closure.php',
    'request_cedula.php'
];

$certificate_active = in_array($current_page, $certificate_pages);
?>

<aside id="leftsidebar" class="sidebar">
    <div class="menu">
        <ul class="list">
            <li class="header" style="font-size: 12px">
                WELCOME TO BARANGAY <?= htmlspecialchars(strtoupper($barangay_name)) ?><br>
                <span style="color: #B6771D;"><?= htmlspecialchars($resident_name) ?></span><br>
                <span style="color: #B6771D;">RESIDENT</span>
            </li>

            <li class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                <a href="index.php">
                    <i class="material-icons">home</i>
                    <span>Home</span>
                </a>
            </li>


            <li style="display: none;" class="<?= $current_page == 'edit_profile.php' ? 'active' : '' ?>">
                <a href="edit_profile.php">
                    <i class="material-icons">groups</i>
                    <span>Edit profile</span>
                </a>
            </li>

            <li style="display: none;" class="<?= $current_page == 'family_profiling.php' ? 'active' : '' ?>">
                <a href="family_profiling.php">
                    <i class="material-icons">groups</i>
                    <span>Family Profiling</span>
                </a>
            </li>


            <li style="display: none;" class="<?= $current_page == 'help_desk.php' ? 'active' : '' ?>">
                <a href="help_desk.php">
                    <i class="material-icons">groups</i>
                    <span>Help Desk</span>
                </a>
            </li>

            <?php if ($is_approved): ?>
                <li class="<?= $current_page == 'family_profiling.php' ? 'active' : '' ?>">
                    <a href="family_profiling.php">
                        <i class="material-icons">groups</i>
                        <span>Family Profiling</span>
                    </a>
                </li>

                <li class="<?= $certificate_active ? 'active' : '' ?>">
                    <a href="javascript:void(0);" class="menu-toggle <?= $certificate_active ? 'toggled' : '' ?>">
                        <i class="material-icons">library_books</i>
                        <span>Certificate Issuance</span>
                    </a>
                    <ul class="ml-menu" style="<?= $certificate_active ? 'display: block;' : '' ?>">
                        <li class="<?= in_array($current_page, ['certificate_issuance.php', 'certificates_view_information.php', 'request_certificate.php']) ? 'active' : '' ?>">
                            <a href="certificate_issuance.php">Certificate</a>
                        </li>
                        <li class="<?= in_array($current_page, ['certificate_operate.php', 'operate_view_information.php', 'request_operate.php']) ? 'active' : '' ?>">
                            <a href="certificate_operate.php">Operate</a>
                        </li>
                        <li class="<?= in_array($current_page, ['certificate_closure.php', 'closure_view_information.php', 'request_closure.php']) ? 'active' : '' ?>">
                            <a href="certificate_closure.php">Closure</a>
                        </li>
                        <li class="<?= in_array($current_page, ['certificate_cedula.php', 'cedula_view_information.php', 'request_cedula.php']) ? 'active' : '' ?>">
                            <a href="certificate_cedula.php">Cedula</a>
                        </li>
                    </ul>
                </li>

                <li class="<?= $current_page == 'live_chat.php' ? 'active' : '' ?>">
                    <a href="live_chat.php">
                        <i class="material-icons">chat</i>
                        <span>Live Chat</span>
                    </a>
                </li>

                <li class="<?= $current_page == 'feedback.php' ? 'active' : '' ?>">
                    <a href="feedback.php">
                        <i class="material-icons">thumb_up</i>
                        <span>Feedback</span>
                    </a>
                </li>

                <li class="<?= $current_page == 'about_us.php' ? 'active' : '' ?>">
                    <a href="about_us.php">
                        <i class="material-icons">menu_book</i>
                        <span>About Us</span>
                    </a>
                </li>

                <li style="display: none;" class="<?= $current_page == 'edit_profile.php' ? 'active' : '' ?>">
                    <a href="edit_profile.php">
                        <i class="material-icons">edit</i>
                        <span>Edit Profile</span>
                    </a>
                </li>

                <li style="display: none;" class="<?= $current_page == 'help_desk.php' ? 'active' : '' ?>">
                    <a href="help_desk.php">
                        <i class="material-icons">help</i>
                        <span>Help Desk</span>
                    </a>
                </li>

            <?php endif; ?>
        </ul>
    </div>
</aside>