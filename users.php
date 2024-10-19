<?php
session_start();
require_once 'config.php';
require_once 'functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || get_user_role($_SESSION['user_id']) != ROLE_ADMIN) {
    header("Location: login.php");
    exit();
}

$user = get_user_by_id($_SESSION['user_id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = intval($_POST['role']);

    if (!empty($username) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $username, $hashed_password, $role);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch users
$result = $conn->query("SELECT * FROM users ORDER BY username");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - <?php echo COMPANY_NAME; ?> Inventory & POS</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="<?php echo COMPANY_LOGO; ?>" alt="<?php echo COMPANY_NAME; ?> Logo" class="company-logo">
        <h1><?php echo COMPANY_NAME; ?> Inventory & POS</h1>
        <p>Welcome, <?php echo htmlspecialchars($user['username']); ?> | <a href="logout.php">Logout</a></p>
    </header>
    
    <nav>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="pos.php">Point of Sale</a></li>
            <li><a href="categories.php">Categories</a></li>
            <li><a href="brands.php">Brands</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="reports.php">Reports</a></li>
        </ul>
    </nav>
    
    <main>
        <section id="users">
            <h2>Users</h2>
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role" required>
                    <option value="<?php echo ROLE_ADMIN; ?>">Admin</option>
                    <option value="<?php echo ROLE_STAFF; ?>">Staff</option>
                </select>
                <button type="submit" name="add_user">Add User</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo $u['role'] == ROLE_ADMIN ? 'Admin' : 'Staff'; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="button">Edit</a>
                            <a href="delete_user.php?id=<?php echo $u['id']; ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?> | Designed by <?php echo DESIGNER; ?></p>
    </footer>
</body>
</html>