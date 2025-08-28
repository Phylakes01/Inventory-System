<?php
include 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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

$error_message = '';
$success_message = '';

// Fetch categories and suppliers for dropdowns
$categories = $conn->query("SELECT * FROM categories");
$suppliers = $conn->query("SELECT * FROM suppliers");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $supplier_id = $_POST['supplier_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // Image upload handling
    $target_dir = "product_image/";
    $image_name = basename($_FILES["product_image"]["name"]);
    $target_file = $target_dir . $image_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Basic validation
    if (empty($product_name) || empty($category_id) || empty($supplier_id) || empty($quantity) || empty($price)) {
        $error_message = "All fields except image are required.";
    } elseif (file_exists($target_file)) {
        $error_message = "Sorry, image file already exists.";
    } elseif ($_FILES["product_image"]["size"] > 500000) { // 500kb limit
        $error_message = "Sorry, your file is too large.";
    } elseif (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    } else {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO products (name, category_id, supplier_id, quantity, price, image_path) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("siidis", $product_name, $category_id, $supplier_id, $quantity, $price, $target_file);
                if ($stmt->execute()) {
                    $success_message = "Product added successfully!";
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "An error occurred. Please try again later.";
            }
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $settings['language'] === 'Filipino' ? 'fil' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Tech NIG</title>
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
        .sidebar-themed {
            background-color: var(--primary-color);
            color: #fff; /* Default to white text */
        }
        .sidebar-themed .sidebar-heading {
            color: #fff;
        }
        .sidebar-themed-item {
            background-color: var(--primary-color);
            color: #fff;
        }
        .sidebar-themed-item:hover {
            background-color: var(--primary-color); /* Keep same background on hover for now */
            filter: brightness(85%); /* Slightly darken on hover */
            color: #fff;
        }
    </style>
</head>
<body class="<?php echo $settings['dark_mode'] ? 'dark-mode' : ''; ?> <?php echo $settings['high_contrast_mode'] ? 'high-contrast' : ''; ?>">
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="border-right sidebar-themed" id="sidebar-wrapper" style="<?php echo $settings['sidebar_collapsible'] ? '' : 'display: none;'; ?>">
            <div class="sidebar-heading">Smart Inventory</div>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action sidebar-themed-item"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                <a href="products.php" class="list-group-item list-group-item-action sidebar-themed-item"><i class="fas fa-box me-2"></i>Products</a>
                <a href="add_product.php" class="list-group-item list-group-item-action sidebar-themed-item"><i class="fas fa-plus me-2"></i>Add Product</a>
                <a href="categories.php" class="list-group-item list-group-item-action sidebar-themed-item"><i class="fas fa-tags me-2"></i>Categories</a>
                <a href="add_category.php" class="list-group-item list-group-item-action sidebar-themed-item"><i class="fas fa-plus me-2"></i>Add Category</a>
                <a href="suppliers.php" class="list-group-item list-group-item-action sidebar-themed-item"><i class="fas fa-truck me-2"></i>Suppliers</a>
                <a href="add_supplier.php" class="list-group-item list-group-item-action sidebar-themed-item"><i class="fas fa-plus me-2"></i>Add Supplier</a>
                <a href="sales_orders.php" class="list-group-item list-group-item-action sidebar-themed-item"><i class="fas fa-shopping-cart me-2"></i>Sales/Orders</a>
                <a href="reports_analytics.php" class="list-group-item list-group-item-action sidebar-themed-item"><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</a>
                <a href="settings.php" class="list-group-item list-group-item-action sidebar-themed-item"><i class="fas fa-cog me-2"></i>Settings</a>
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
                                <li><a class="dropdown-item" href="account_settings.php">Account Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <h1 class="mt-4">Add New Product</h1>

                <div class="card mt-4">
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                        <form method="POST" action="add_product.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php while($row = $categories->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select class="form-select" id="supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    <?php while($row = $suppliers->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" required>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="text" class="form-control" id="price" name="price" required>
                            </div>
                            <div class="mb-3">
                                <label for="product_image" class="form-label">Product Image</label>
                                <input class="form-control" type="file" id="product_image" name="product_image">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </form>
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