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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch categories
$result = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - <?php echo COMPANY_NAME; ?> Inventory & POS</title>
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
        <section id="categories">
            <h2>Categories</h2>
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="text" name="category_name" placeholder="New Category Name" required>
                <button type="submit" name="add_category">Add Category</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['id']; ?></td>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td>
                            <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="button">Edit</a>
                            <a href="delete_category.php?id=<?php echo $category['id']; ?>" class="button delete" onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
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