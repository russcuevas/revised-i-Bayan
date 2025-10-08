<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside id="leftsidebar" class="sidebar">
    <div class="menu">
        <ul class="list">
            <li class="header" style="font-size: 12px">
                WELCOME <br>
                <span style="color: #B6771D; text-transform: uppercase;">
                    <?= htmlspecialchars($_SESSION['superadmin_name']) ?>
                </span> <br>
                <span style="color: #B6771D;">MATAASNAKAHOY - SUPERADMIN</span>
            </li>

            <li class="<?= $current_page == 'index.php' ? 'active' : '' ?>">
                <a href="index.php">
                    <i class="material-icons">home</i>
                    <span>Dashboard</span>
                </a>
            </li>

            <?php
            $barangay_pages = ['barangay_management.php', 'update_barangay.php'];
            ?>
            <li class="<?= in_array($current_page, $barangay_pages) ? 'active' : '' ?>">
                <a href="barangay_management.php">
                    <i class="material-icons">apartment</i>
                    <span>Barangay Management</span>
                </a>
            </li>

            <?php
            $business_pages = ['business_clearance.php', 'update_business_clearance.php'];
            ?>
            <li class="<?= in_array($current_page, $business_pages) ? 'active' : '' ?>">
                <a href="business_clearance.php">
                    <i class="material-icons">business</i>
                    <span>Business List</span>
                </a>
            </li>

            <?php
            $admin_pages = ['admin_management.php', 'add_admin.php', 'update_admin.php'];
            ?>
            <li class="<?= in_array($current_page, $admin_pages) ? 'active' : '' ?>">
                <a href="admin_management.php">
                    <i class="material-icons">admin_panel_settings</i>
                    <span>Admin Management</span>
                </a>
            </li>

            <?php
            $certificate_pages = [
                // Main
                'certificate_issuance.php',
                'certificate_operate.php',
                'certificate_closure.php',
                'certificate_cedula.php',
            ];

            $certificate_active = in_array($current_page, $certificate_pages);
            ?>

            <li class="<?= $certificate_active ? 'active' : '' ?>">
                <a href="javascript:void(0);" class="menu-toggle <?= $certificate_active ? 'toggled' : '' ?>">
                    <i class="material-icons">library_books</i>
                    <span>Certificate Issuance</span>
                </a>
                <ul class="ml-menu" style="<?= $certificate_active ? 'display: block;' : '' ?>">

                    <li class="<?= in_array($current_page, ['certificate_issuance.php']) ? 'active' : '' ?>">
                        <a href="certificate_issuance.php">Certificate</a>
                    </li>

                    <li class="<?= in_array($current_page, ['certificate_operate.php']) ? 'active' : '' ?>">
                        <a href="certificate_operate.php">Operate</a>
                    </li>

                    <li class="<?= in_array($current_page, ['certificate_closure.php']) ? 'active' : '' ?>">
                        <a href="certificate_closure.php">Closure</a>
                    </li>

                    <li class="<?= in_array($current_page, ['certificate_cedula.php']) ? 'active' : '' ?>">
                        <a href="certificate_cedula.php">Cedula</a>
                    </li>

                </ul>
            </li>



            <li class="<?= $current_page == 'emergency_update.php' ? 'active' : '' ?>">
                <a href="emergency_update.php">
                    <i class="material-icons">emergency</i>
                    <span>Emergency Updates</span>
                </a>
            </li>

            <li class="<?= $current_page == 'system_settings.php' ? 'active' : '' ?>">
                <a href="system_settings.php">
                    <i class="material-icons">settings</i>
                    <span>System Settings</span>
                </a>
            </li>

            <li style="display: none;" class="<?= $current_page == 'system_preference.php' ? 'active' : '' ?>">
                <a href="system_preference.php">
                    <i class="material-icons">settings</i>
                    <span>System Settings</span>
                </a>
            </li>

            <li style="display: none;" class="<?= $current_page == 'system_permissions.php' ? 'active' : '' ?>">
                <a href="system_permissions.php">
                    <i class="material-icons">settings</i>
                    <span>System Settings</span>
                </a>
            </li>

            <li style="display: none;" class="<?= $current_page == 'system_logs.php' ? 'active' : '' ?>">
                <a href="system_logs.php">
                    <i class="material-icons">settings</i>
                    <span>System Settings</span>
                </a>
            </li>

            <li style="display: none;" class="<?= $current_page == 'edit_profile.php' ? 'active' : '' ?>">
                <a href="edit_profile.php">
                    <i class="material-icons">settings</i>
                    <span>System Settings</span>
                </a>
            </li>
        </ul>
    </div>
</aside>