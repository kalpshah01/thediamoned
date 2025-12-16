<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
 <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <img src="../assets/images/logo.png" alt="no logo" id="logo">
            <a class="navbar-brand" href="/admin/index.php">Admin Panel</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav ms-auto">
                
                <li class="nav-item"><a class="nav-link" href="/admin/users.php">View All User</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/manage_menu.php">Manage Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/table_bookings.php">Table Bookings</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/coupons.php">Coupens</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/employees.php">Employees</a></li>
                
                <li class="nav-item"><a class="nav-link" href="/admin/logout.php">Logout</a></li>
                
            </ul>
        </div>
        
    </div>
</nav>

</body>
</html>
