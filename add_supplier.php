<?php
include 'db.php';
session_start();
include 'apply_settings.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $contact_person = $_POST['contact_person'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    if (empty($name)) {
        $error_message = "Supplier name is required.";
    } else {
        $sql = "INSERT INTO suppliers (name, contact_person, email, phone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssss", $name, $contact_person, $email, $phone);
            if ($stmt->execute()) {
                $success_message = "Supplier added successfully!";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "An error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $settings['language'] === 'Filipino' ? 'fil' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier - Tech NIG</title>
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
                <h1 class="mt-4">Add New Supplier</h1>
                <div class="card mt-4">
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                        <form method="POST" action="add_supplier.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Supplier Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Supplier</button>
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