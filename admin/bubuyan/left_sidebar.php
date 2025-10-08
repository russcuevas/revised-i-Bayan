<aside id="leftsidebar" class="sidebar">
    <div class="menu">
        <ul class="list">
            <li class="header" style="font-size: 12px">
                WELCOME <br>
                <span style="color: #B6771D; text-transform: uppercase;">
                    BARANGAY <?= htmlspecialchars($_SESSION[$barangay_name_key]); ?>
                </span><br>
                <span style="color: #B6771D; text-transform: uppercase;">
                    <?= htmlspecialchars($_SESSION[$admin_name_key]); ?>
                </span><br>
                <span style="color: #B6771D; text-transform: uppercase;">
                    <?= htmlspecialchars($_SESSION[$admin_position_key]); ?>
                </span>
            </li>
            <li class="active">
                <a href="index.php">
                    <i class="material-icons">home</i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="barangay_officials.php">
                    <i class="material-icons">admin_panel_settings</i>
                    <span>Barangay Officials</span>
                </a>
            </li>
            <li>
                <a href="manage_residents.php">
                    <i class="material-icons">groups</i>
                    <span>Manage Residents</span>
                </a>
            </li>
            <li>
                <a href="resident_verifications.php">
                    <i class="material-icons">pending_actions</i>
                    <span>Resident Verifications</span>
                </a>
            </li>
            <!-- <li>
                <a href="family_profiling.php">
                    <i class="material-icons">house</i>
                    <span>Household List</span>
                </a>
            </li> -->
            <li>
                <a href="javascript:void(0);" class="menu-toggle">
                    <i class="material-icons">library_books</i>
                    <span>Certificate Requests</span>
                </a>
                <ul class="ml-menu">
                    <li>
                        <a href="certificate_issuance.php">Certificate</a>
                    </li>
                    <li>
                        <a href="certificate_operate.php">Operate</a>
                    </li>
                    <li>
                        <a href="certificate_closure.php">Closure</a>
                    </li>
                    <li>
                        <a href="certificate_cedula.php">Cedula</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="live_chat.php">
                    <i class="material-icons">chat</i>
                    <span>Live Chat</span>
                </a>
            </li>
            <li>
                <a href="announcements.php">
                    <i class="material-icons">campaign</i>
                    <span>Announcements</span>
                </a>
            </li>
            <li>
                <a href="feedback.php">
                    <i class="material-icons">thumb_up</i>
                    <span>Feedback</span>
                </a>
            </li>
            <li>
                <a href="activity_logs.php">
                    <i class="material-icons">track_changes</i>
                    <span>Activity Logs</span>
                </a>
            </li>
            <li>
                <a href="reports.php">
                    <i class="material-icons">article</i>
                    <span>Reports</span>
                </a>
            </li>
        </ul>
    </div>
</aside>