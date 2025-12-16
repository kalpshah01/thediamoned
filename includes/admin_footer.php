<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
</div>
<footer class="admin-footer-wrap mt-auto">
    <div class="admin-footer-inner">

        <div class="admin-footer-grid">

            
            <div class="admin-footer-col">
                <h5 class="admin-footer-title">DelishBite Admin</h5>
                <p class="admin-footer-text">
                    Restaurant management dashboard.<br>
                    Control Menu, Bookings, Orders & Staff.
                </p>
            </div>

            
            <div class="admin-footer-col c2">
                <h6 class="admin-footer-title">Quick Actions</h6>
                <ul class="admin-footer-links">
                    <li><a href="/admin/manage_menu.php">Manage Menu</a></li>
                    <li><a href="/admin/table_bookings.php">Table Bookings</a></li>
                    <li><a href="/admin/orders.php">Food Orders</a></li>
                </ul>
            </div>

            
            <div class="admin-footer-col admin-footer-right" style="margin-right:26px">
                <h6 class="admin-footer-title">System Status</h6>
                <p class="admin-footer-text">
                    Logged in as:<br>
                    <strong>
                        <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Administrator'); ?>
                    </strong>
                </p>
                <span class="admin-footer-secure">Secure Connection</span>
            </div>

        </div>

        <div class="admin-footer-bottom">
            © <?= date('Y'); ?> DelishBite Admin Panel. All rights reserved. Kalp Shah
        </div>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
