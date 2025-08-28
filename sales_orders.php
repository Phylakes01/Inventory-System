<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

$user_id = $_SESSION['user_id'];

// Fetch user settings
$settings_query = "SELECT * FROM user_settings WHERE user_id = ?";
$stmt = $conn->prepare($settings_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();

// If no settings found, create a default entry
if (!$settings) {
    $insert_default_settings = "INSERT INTO user_settings (user_id) VALUES (?)";
    $stmt = $conn->prepare($insert_default_settings);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    // Refetch settings
    $stmt = $conn->prepare($settings_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $settings = $result->fetch_assoc();
}

// Add new sales order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_order'])) {
    $product_id = $_POST['product_id'];
    $quantity_sold = $_POST['quantity_sold'];

    // Fetch product price
    $price_query = "SELECT price FROM products WHERE id = ?";
    $stmt = $conn->prepare($price_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $total_price = $product['price'] * $quantity_sold;

    $insert_query = "INSERT INTO sales_orders (product_id, quantity_sold, total_price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("iid", $product_id, $quantity_sold, $total_price);
    $stmt->execute();

    // Update product quantity
    $update_quantity_query = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
    $stmt = $conn->prepare($update_quantity_query);
    $stmt->bind_param("ii", $quantity_sold, $product_id);
    $stmt->execute();

    header("Location: sales_orders.php");
    exit();
}

// Fetch all sales orders with product names
$sql = "SELECT so.id, p.name as product_name, so.quantity_sold, so.total_price, so.order_date 
        FROM sales_orders so
        JOIN products p ON so.product_id = p.id
        ORDER BY so.order_date DESC";
$sales_orders = $conn->query($sql);

// Fetch all products for the add order form
$products_query = "SELECT id, name FROM products";
$products_result = $conn->query($products_query);

?>
<!DOCTYPE html>
<html lang="<?php echo $settings['language'] === 'Filipino' ? 'fil' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales/Orders - Tech NIG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --font-size: <?php echo $settings['font_size'] === 'Small' ? '14px' : ($settings['font_size'] === 'Large' ? '18px' : '16px'); ?>;
            --font-family: <?php echo $settings['font_family']; ?>;
            --primary-color: <?php echo $settings['color_theme']; ?>;
        }
        body {
            font-size: var(--font-size);
            font-family: var(--font-family);
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .dark-mode {
            background-color: #212529;
            color: #fff;
        }
        .high-contrast {
            background-color: #000;
            color: #fff;
        }
    </style>
</head>
<body class="<?php echo $settings['dark_mode'] ? 'dark-mode' : ''; ?> <?php echo $settings['high_contrast_mode'] ? 'high-contrast' : ''; ?>">
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark border-right" id="sidebar-wrapper" style="<?php echo $settings['sidebar_collapsible'] ? '' : 'display: none;'; ?>">
            <div class="sidebar-heading text-white">Smart Inventory</div>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="products.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-box me-2"></i>Products</a>
                <a href="add_product.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-plus me-2"></i>Add Product</a>
                <a href="categories.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-tags me-2"></i>Categories</a>
                <a href="add_category.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-plus me-2"></i>Add Category</a>
                <a href="suppliers.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-truck me-2"></i>Suppliers</a>
                <a href="add_supplier.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-plus me-2"></i>Add Supplier</a>
                <a href="sales_orders.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-shopping-cart me-2"></i>Sales/Orders</a>
                <a href="reports_analytics.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</a>
                <a href="settings.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-cog me-2"></i>Settings</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="menu-toggle"><i class="fas fa-bars"></i></button>
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="#">Account Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <h1 class="mt-4">Sales/Orders</h1>

                <div class="card mt-4">
                    <div class="card-header">
                        Add New Order
                    </div>
                    <div class="card-body">
                        <form method="POST" action="sales_orders.php">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="product_id" class="form-label">Product</label>
                                        <select class="form-select" id="product_id" name="product_id" required>
                                            <option value="">Select Product</option>
                                            <?php while($row = $products_result->fetch_assoc()): ?>
                                                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="quantity_sold" class="form-label">Quantity Sold</label>
                                        <input type="number" class="form-control" id="quantity_sold" name="quantity_sold" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" name="add_order" class="btn btn-primary w-100">Add Order</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product Name</th>
                                    <th>Quantity Sold</th>
                                    <th>Total Price</th>
                                    <th>Order Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($sales_orders->num_rows > 0): ?>
                                    <?php while($row = $sales_orders->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['quantity_sold']); ?></td>
                                            <td>$<?php echo htmlspecialchars($row['total_price']); ?></td>
                                            <td><?php echo $row['order_date']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No sales orders found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("menu-toggle").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("wrapper").classList.toggle("toggled");
        });
    </script>
</body>
</html>