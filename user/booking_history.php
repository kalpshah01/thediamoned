<?php 
session_start(); 
require_once __DIR__.'/../config/db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include_once __DIR__.'/../includes/header.php';

$tb = $conn->prepare("
    SELECT * FROM table_bookings 
    WHERE user_id=? 
    ORDER BY booking_date DESC
");
$tb->bind_param("i", $_SESSION['user_id']);
$tb->execute();
$tableBookings = $tb->get_result();
$od = $conn->prepare("
    SELECT * FROM orders 
    WHERE user_id=? 
    ORDER  BY created_at DESC
");
$od->bind_param("i", $_SESSION['user_id']);
$od->execute();
$foodOrders = $od->get_result();
?>

<style>
.section-title {
    font-size: 1.6rem;
    font-weight: 700;
    margin-bottom: 15px;
}

.section-icon {
    width: 28px;
    margin-right: 8px;
    vertical-align: middle;
}

.history-card {
    border-radius: 12px;
    padding: 18px 22px;
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.badge-status {
    font-size: 0.85rem;
    padding: 6px 12px;
    border-radius: 20px;
}

.badge-confirmed { background:#28a745; color:white; }
.badge-cancelled { background:#dc3545; color:white; }
.badge-pending   { background:#ffc107; color:#000; }

.badge-delivered { background:#6f42c1; color:white; }
.badge-progress  { background:#17a2b8; color:white; }
</style>

<h2 class="mb-4 fw-bold">My History</h2>

<div class="history-card">
    <h4 class="section-title">
        <img src="../assets/images/table.png" class="section-icon"> 
        Table Bookings
    </h4>

    <?php if ($tableBookings->num_rows > 0): ?>
        <?php $i = 1; while ($r = $tableBookings->fetch_assoc()): ?>
            <?php 
                $status = strtolower($r['status']);

           
                if ($status === "confirmed") {
                    $badgeClass = "badge-confirmed";
                } elseif ($status === "cancelled") {
                    $badgeClass = "badge-cancelled";
                } else {
                    $badgeClass = "badge-pending";
                }
            ?>

            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fw-bold">Booking #<?= $i++; ?></div>
                        <div class="text-muted small">
                            <?= htmlspecialchars($r['booking_date']); ?> • 
                            <?= htmlspecialchars($r['booking_time']); ?>
                        </div>
                        <div class="mt-1">
                            Guests: <strong><?= (int)$r['guests']; ?></strong>
                        </div>
                    </div>

                    <div>
                        <span class="badge badge-status <?= $badgeClass; ?>">
                            <?= ucfirst($r['status']); ?>
                        </span>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center text-muted py-4">No table bookings found</div>
    <?php endif; ?>
</div>


<div class="history-card">
    <h4 class="section-title">
        <img src="../assets/images/food.png" class="section-icon"> 
        Food Orders
    </h4>

    <?php if ($foodOrders->num_rows > 0): ?>
        <?php $j = 1; while ($o = $foodOrders->fetch_assoc()): ?>
            <?php 
                $status = strtolower($o['status']);

               
                if ($status === "delivered") {
                    $badgeClass = "badge-delivered";
                } elseif ($status === "out for delivery" || 
                          $status === "on the way" || 
                          $status === "arriving") {
                    $badgeClass = "badge-progress";
                } elseif ($status === "cancelled") {
                    $badgeClass = "badge-cancelled";
                } else {
                    $badgeClass = "badge-pending";
                }
            ?>

            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between">

                
                    <div>
                        <div class="fw-bold">Order #<?= $o['id']; ?></div>

                        <div class="text-muted small">
                            <?= htmlspecialchars($o['created_at']); ?>
                        </div>

                        <div class="mt-1">
                            Total: <strong>₹<?= number_format($o['total_amount'], 2); ?></strong>
                        </div>

                        <div>
                            Payment: <strong><?= strtoupper($o['payment_method']); ?></strong>
                        </div>
                    </div>

                
                    <div class="text-end">

                        <span class="badge badge-status <?= $badgeClass; ?>">
                            <?= ucfirst($o['status']); ?>
                        </span>

                        <div class="mt-2">
                            <a href="/user/order_status.php?id=<?= $o['id']; ?>" 
                               class="btn btn-sm btn-outline-primary">
                                Track Order
                            </a>
                        </div>

                    </div>

                </div>
            </div>

        <?php endwhile; ?>

    <?php else: ?>
        <div class="text-center text-muted py-4">No food orders found</div>
    <?php endif; ?>
</div>
</div></div>
<?php include_once __DIR__.'/../includes/footer.php'; ?>
