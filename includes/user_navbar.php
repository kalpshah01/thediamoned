<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
   <link rel="stylesheet" href="../asstes/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
    <img src="../assets/images/logo.png" alt="no logo" id="logo">
        <a class="navbar-brand" href="/user/menu.php">The Diamond</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="userNav">
            <ul class="navbar-nav ms-auto"> 

                <li class="nav-item"><a class="nav-link" href="/user/menu.php">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="/user/book_table.php">Book Table</a></li>
                <li class="nav-item"><a class="nav-link" href="/user/booking_history.php">My Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="/user/cart.php">Cart</a></li>
                <li class="nav-item"><a class="nav-link" href="/user/update_profile.php">Update Profile</a></li>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="/user/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/user/login.php">Login</a></li>
                <?php endif; ?>

            </ul>
        </div>

    </div>
</nav>
    
</body>
</html>
