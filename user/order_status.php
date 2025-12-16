<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /user/login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header("Location: menu.php");
    exit;
}


$stmt = $conn->prepare("
    SELECT o.*, e.name AS emp_name
    FROM orders o
    LEFT JOIN employees e ON e.id=o.employee_id
    WHERE o.id=? AND o.user_id=?
");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "Order not found";
    exit;
}


if (
    $order['status'] === 'Out for Delivery' &&
    !empty($order['start_time']) &&
    !empty($order['delivery_estimate_minutes'])
) {
    $startTs = strtotime($order['start_time']);
    $endTs   = $startTs + ($order['delivery_estimate_minutes'] * 60);

    if (time() >= $endTs) {
        $up = $conn->prepare("
            UPDATE orders SET status='Delivered'
            WHERE id=? AND user_id=?
        ");
        $up->bind_param("ii", $id, $_SESSION['user_id']);
        $up->execute();
        $order['status'] = 'Delivered';
    }
}


$noRider = empty($order['employee_id']) ||
           empty($order['delivery_estimate_minutes']) ||
           empty($order['start_time']);

include_once __DIR__ . '/../includes/header.php';
?>

<h2>Order Tracking</h2>

<div class="row g-3">
    <div class="col-md-7">
        <div class="card p-3">

        <?php if ($noRider): ?>

            <div class="text-center py-5">
                <h4 class="text-muted">Order Received</h4>
                <p class="text-muted">
                    Waiting for a delivery partner to be assigned…
                </p>
            </div>

        <?php else: ?>

            
            <div id="fake-map" class="fake-map" style="height:360px;">

                <svg id="map-svg"
                     style="position:absolute;top:0;left:0;width:100%;height:100%"></svg>

             
                <div id="restaurant-pin"
                     style="position:absolute;left:12%;top:50%">
                    <img src="/assets/icons/restaurant.png" width="40">
                </div>

                <div id="rider-pin"
                     style="position:absolute;left:80%;top:48%">
                    <img src="/assets/icons/rider.png" width="40">
                </div>

              
                <div id="rider-initial"
                     style="position:absolute;left:80%;top:48%;
                            background:#000;color:#fff;
                            width:26px;height:26px;
                            border-radius:50%;
                            display:flex;align-items:center;
                            justify-content:center;font-size:12px">
                    <?= strtoupper(substr($order['emp_name'] ?? 'R', 0, 1)); ?>
                </div>
            </div>

        <?php endif; ?>

        </div>
    </div>

    <div class="col-md-5">
        <div class="card p-3">
            <h4>Order #<?= $order['id']; ?></h4>

            <p><strong>Delivery Partner:</strong>
                <?= htmlspecialchars($order['emp_name'] ?? 'Not Assigned'); ?>
            </p>

            <p><strong>Status:</strong>
                <span id="status-text">
                    <?= $noRider ? 'Preparing' : $order['status']; ?>
                </span>
            </p>

            <hr>

            <div><strong>Distance:</strong>
                <span id="distance-text"><?= $noRider ? '--' : 'calculating…'; ?></span>
            </div>

            <div><strong>ETA:</strong>
                <span id="eta-text">--</span>
            </div>

            <div class="mt-3">
                <div class="progress">
                    <div id="prog" class="progress-bar" style="width:10%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!$noRider && $order['status'] !== 'Delivered'): ?>
<script>

const startTime = new Date("<?= $order['start_time']; ?>");
const totalMs  = <?= intval($order['delivery_estimate_minutes']); ?> * 60000;


const initialDistance = 8 + Math.random() * 6;

const distEl = document.getElementById("distance-text");
const etaEl  = document.getElementById("eta-text");
const statEl = document.getElementById("status-text");
const progEl = document.getElementById("prog");

const rider = document.getElementById("rider-pin");
const initial = document.getElementById("rider-initial");
const svg = document.getElementById("map-svg");
const map = document.getElementById("fake-map");

function remainingMs() {
    return Math.max(0, totalMs - (new Date() - startTime));
}

function remainingDistance() {
    return Math.max(0, initialDistance * (remainingMs() / totalMs));
}

function drawPath() {
    svg.innerHTML = "";
    const w = map.offsetWidth;
    const h = map.offsetHeight;

    const d = `M ${w*0.12} ${h*0.5}
               C ${w*0.4} ${h*0.2},
                 ${w*0.6} ${h*0.2},
                 ${w*0.8} ${h*0.48}`;

    const p = document.createElementNS("http://www.w3.org/2000/svg","path");
    p.setAttribute("d", d);
    p.setAttribute("stroke", "#2a9d8f");
    p.setAttribute("stroke-width", "4");
    p.setAttribute("fill", "none");
    svg.appendChild(p);
    return p;
}

const path = drawPath();

function moveRider() {
    const dist = remainingDistance();
    const ms = remainingMs();

    distEl.textContent = dist.toFixed(1) + " km";
    etaEl.textContent = Math.ceil(ms/60000) + " mins";

    if (dist <= 0) {
        statEl.textContent = "Delivered";
        progEl.style.width = "100%";
        return;
    }

    statEl.textContent = dist < 0.5 ? "Arriving" : "On the Way";
    progEl.style.width = (100 - (ms/totalMs)*100) + "%";

    const len = path.getTotalLength();
    const pt = path.getPointAtLength(len * (1 - dist/initialDistance));

    rider.style.left = (pt.x / map.offsetWidth * 100) + "%";
    rider.style.top  = (pt.y / map.offsetHeight * 100) + "%";
    initial.style.left = rider.style.left;
    initial.style.top  = rider.style.top;
}

setInterval(moveRider, 5000);
moveRider();
</script>
<?php endif; ?>
</div></div>
<?php include_once __DIR__ . '/../includes/footer.php'; ?>
