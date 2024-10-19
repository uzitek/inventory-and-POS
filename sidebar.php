<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/index.php') ? 'active' : ''; ?>" href="index.php">
                    <span data-feather="home"></span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/orders.php') ? 'active' : ''; ?>" href="orders.php">
                    <span data-feather="file"></span>
                    Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/inventory.php') ? 'active' : ''; ?>" href="inventory.php">
                    <span data-feather="box"></span>
                    Inventory
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/customers.php') ? 'active' : ''; ?>" href="customers.php">
                    <span data-feather="users"></span>
                    Customers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/pos.php') ? 'active' : ''; ?>" href="pos.php">
                    <span data-feather="shopping-cart"></span>
                    Point of Sale
                </a>
            </li>
            <?php if ($user['role'] == ROLE_ADMIN): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/categories.php') ? 'active' : ''; ?>" href="categories.php">
                    <span data-feather="list"></span>
                    Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/brands.php') ? 'active' : ''; ?>" href="brands.php">
                    <span data-feather="tag"></span>
                    Brands
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/users.php') ? 'active' : ''; ?>" href="users.php">
                    <span data-feather="user"></span>
                    Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/reports.php') ? 'active' : ''; ?>" href="reports.php">
                    <span data-feather="bar-chart-2"></span>
                    Reports
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>