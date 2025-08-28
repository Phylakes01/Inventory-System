<?php
session_start();
include 'db.php';
include 'apply_settings.php';

// Low Stock Items
$low_stock_query = "SELECT COUNT(*) as low_stock_count FROM products WHERE quantity < 10"; // Assuming low stock is less than 10
$low_stock_result = $conn->query($low_stock_query);
$low_stock_items = $low_stock_result->fetch_assoc()['low_stock_count'];

$low_stock_alerts_query = "SELECT name, quantity FROM products WHERE quantity < 10 ORDER BY quantity ASC";
$low_stock_alerts_result = $conn->query($low_stock_alerts_query);

?>
<!DOCTYPE html>
<html lang="<?php echo $settings['language'] === 'Filipino' ? 'fil' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
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

                    <form class="d-flex ms-auto">
                        <input class="form-control me-2" type="search" placeholder="Search for products..." aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>

                    <ul class="navbar-nav ms-3">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <?php if ($low_stock_items > 0): ?>
                                    <span class="badge bg-danger"><?php echo $low_stock_items; ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <?php
                                if ($low_stock_alerts_result->num_rows > 0) {
                                    mysqli_data_seek($low_stock_alerts_result, 0);
                                    while($row = $low_stock_alerts_result->fetch_assoc()) {
                                        echo '<li><a class="dropdown-item" href="#">' . htmlspecialchars($row['name']) . ' is below minimum stock.</a></li>';
                                    }
                                } else {
                                    echo '<li><a class="dropdown-item" href="#">No new notifications</a></li>';
                                }
                                ?>
                            </ul>
                        </li>
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
                <h1 class="mt-4">Dashboard</h1>
                <?php
                // Total Products in Stock
                $total_products_query = "SELECT SUM(quantity) as total_quantity FROM products";
                $total_products_result = $conn->query($total_products_query);
                $total_products = $total_products_result->fetch_assoc()['total_quantity'];

                // Total Inventory Value
                $inventory_value_query = "SELECT SUM(quantity * price) as total_value FROM products";
                $inventory_value_result = $conn->query($inventory_value_query);
                $inventory_value = $inventory_value_result->fetch_assoc()['total_value'];

                // Total Sales This Month
                $sales_this_month_query = "SELECT SUM(total_price) as total_sales FROM sales_orders WHERE MONTH(order_date) = MONTH(CURRENT_DATE()) AND YEAR(order_date) = YEAR(CURRENT_DATE())";
                $sales_this_month_result = $conn->query($sales_this_month_query);
                $sales_this_month = $sales_this_month_result->fetch_assoc()['total_sales'];
                ?>
                <div class="row g-4 my-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Total Products in Stock</h5>
                                        <p class="card-text fs-4"><?php echo number_format($total_products); ?></p>
                                    </div>
                                    <i class="fas fa-box fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Total Inventory Value</h5>
                                        <p class="card-text fs-4">$<?php echo number_format($inventory_value, 2); ?></p>
                                    </div>
                                    <i class="fas fa-dollar-sign fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Total Sales This Month</h5>
                                        <p class="card-text fs-4">$<?php echo number_format($sales_this_month, 2); ?></p>
                                    </div>
                                    <i class="fas fa-shopping-cart fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Low Stock Items</h5>
                                        <p class="card-text fs-4"><?php echo $low_stock_items; ?></p>
                                    </div>
                                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 my-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <i class="fas fa-chart-line me-1"></i>
                                Sales Trends Over Time
                            </div>
                            <div class="card-body"><canvas id="salesChart" width="100%" height="40"></canvas></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <i class="fas fa-chart-bar me-1"></i>
                                Stock Levels by Category
                            </div>
                            <div class="card-body"><canvas id="stockChart" width="100%" height="40"></canvas></div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 my-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Low Stock Items
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Remaining Quantity</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $low_stock_products_query = "SELECT name, quantity FROM products WHERE quantity < 10 ORDER BY quantity ASC";
                                        $low_stock_products_result = $conn->query($low_stock_products_query);
                                        if ($low_stock_products_result->num_rows > 0) {
                                            while($row = $low_stock_products_result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                                                echo "<td><button class=\"btn btn-warning btn-sm\">Reorder</button></td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan=\"3\" class=\"text-center\">No low stock items</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                         <div class="card h-100">
                            <div class="card-header">
                                <i class="fas fa-bell me-1"></i>
                                Alerts & Notifications
                            </div>
                            <div class="list-group list-group-flush">
                                <?php
                                $low_stock_alerts_query = "SELECT name, quantity FROM products WHERE quantity < 10 ORDER BY quantity ASC";
                                $low_stock_alerts_result = $conn->query($low_stock_alerts_query);
                                if ($low_stock_alerts_result->num_rows > 0) {
                                    while($row = $low_stock_alerts_result->fetch_assoc()) {
                                        echo '<a href="#" class="list-group-item list-group-item-action">';
                                        echo '<div class="d-flex w-100 justify-content-between">';
                                        echo '<h6 class="mb-1">Low Stock Alert</h6>';
                                        echo '</div>';
                                        echo '<p class="mb-1">' . htmlspecialchars($row['name']) . ' is below minimum stock.</p>';
                                        echo '</a>';
                                    }
                                } else {
                                    echo '<a href="#" class="list-group-item list-group-item-action">No new notifications</a>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>
    <!-- /#wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Menu Toggle Script
        document.getElementById("menu-toggle").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("wrapper").classList.toggle("toggled");
        });

        <?php
        // Sales Trends Over Time
        $sales_trends_query = "SELECT DATE_FORMAT(order_date, '%Y-%m-%d') as order_day, SUM(total_price) as daily_sales FROM sales_orders WHERE order_date >= CURDATE() - INTERVAL 7 DAY GROUP BY order_day ORDER BY order_day";
        $sales_trends_result = $conn->query($sales_trends_query);
        $sales_labels = [];
        $sales_data = [];
        while($row = $sales_trends_result->fetch_assoc()) {
            $sales_labels[] = $row['order_day'];
            $sales_data[] = $row['daily_sales'];
        }

        // Stock Levels by Category
        $stock_levels_query = "SELECT c.name as category_name, SUM(p.quantity) as total_quantity FROM products p JOIN categories c ON p.category_id = c.id GROUP BY c.name";
        $stock_levels_result = $conn->query($stock_levels_query);
        $stock_labels = [];
        $stock_data = [];
        while($row = $stock_levels_result->fetch_assoc()) {
            $stock_labels[] = $row['category_name'];
            $stock_data[] = $row['total_quantity'];
        }
        ?>

        // Chart.js Initialization
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($sales_labels); ?>,
                datasets: [{
                    label: 'Daily Sales',
                    data: <?php echo json_encode($sales_data); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    tension: 0.1
                }]
            }
        });

        const stockCtx = document.getElementById('stockChart').getContext('2d');
        const stockChart = new Chart(stockCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($stock_labels); ?>,
                datasets: [{
                    label: 'Stock Quantity',
                    data: <?php echo json_encode($stock_data); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    </script>
</body>
</html>