<?php
session_start();
include 'db.php';
include 'apply_settings.php';

// Update settings
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dark_mode = isset($_POST['dark_mode']) ? 1 : 0;
    $font_size = $_POST['font_size'];
    $font_family = $_POST['font_family'];
    $color_theme = $_POST['color_theme'];
    $layout = $_POST['layout'];
    $sidebar_collapsible = isset($_POST['sidebar_collapsible']) ? 1 : 0;
    $language = $_POST['language'];
    $date_format = $_POST['date_format'];
    $desktop_notifications = isset($_POST['desktop_notifications']) ? 1 : 0;
    $sound_alerts = isset($_POST['sound_alerts']) ? 1 : 0;
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $high_contrast_mode = isset($_POST['high_contrast_mode']) ? 1 : 0;
    $screen_reader = isset($_POST['screen_reader']) ? 1 : 0;

    $update_query = "UPDATE user_settings SET 
        dark_mode = ?,
        font_size = ?,
        font_family = ?,
        color_theme = ?,
        layout = ?,
        sidebar_collapsible = ?,
        language = ?,
        date_format = ?,
        desktop_notifications = ?,
        sound_alerts = ?,
        email_notifications = ?,
        high_contrast_mode = ?,
        screen_reader = ?
    WHERE user_id = ?";

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("issssisssiiiii", 
        $dark_mode, 
        $font_size, 
        $font_family, 
        $color_theme, 
        $layout, 
        $sidebar_collapsible, 
        $language, 
        $date_format, 
        $desktop_notifications, 
        $sound_alerts, 
        $email_notifications, 
        $high_contrast_mode, 
        $screen_reader, 
        $user_id
    );

    if ($stmt->execute()) {
        // Refresh settings
        $stmt = $conn->prepare($settings_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $settings = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Tech NIG</title>
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
                                <li><a class="dropdown-item" href="account_settings.php">Account Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid px-4">
                <h1 class="mt-4">Settings</h1>
                <form method="POST" action="settings.php">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    User Interface & Personalization Settings
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Dark Mode / Light Mode Toggle 🌙☀️</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="darkModeSwitch" name="dark_mode" <?php echo $settings['dark_mode'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="darkModeSwitch">Enable Dark Mode</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fontSizeSelect" class="form-label">Font Size & Style 🔤</label>
                                        <select class="form-select" id="fontSizeSelect" name="font_size">
                                            <option <?php echo $settings['font_size'] === 'Small' ? 'selected' : ''; ?>>Small</option>
                                            <option <?php echo $settings['font_size'] === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                            <option <?php echo $settings['font_size'] === 'Large' ? 'selected' : ''; ?>>Large</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <select class="form-select" id="fontFamilySelect" name="font_family">
                                            <option <?php echo $settings['font_family'] === 'System Default' ? 'selected' : ''; ?>>System Default</option>
                                            <option <?php echo $settings['font_family'] === 'Sans-Serif' ? 'selected' : ''; ?>>Sans-Serif</option>
                                            <option <?php echo $settings['font_family'] === 'Serif' ? 'selected' : ''; ?>>Serif</option>
                                            <option <?php echo $settings['font_family'] === 'Monospace' ? 'selected' : ''; ?>>Monospace</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="sidebarToggle" name="sidebar_collapsible" <?php echo $settings['sidebar_collapsible'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="sidebarToggle">Collapsible Sidebar</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    Language & Localization 🌍
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="languageSelect" class="form-label">Language</label>
                                        <select class="form-select" id="languageSelect" name="language">
                                            <option <?php echo $settings['language'] === 'English' ? 'selected' : ''; ?>>English</option>
                                            <option <?php echo $settings['language'] === 'Filipino' ? 'selected' : ''; ?>>Filipino</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="dateFormatSelect" class="form-label">Time & Date Format</label>
                                        <select class="form-select" id="dateFormatSelect" name="date_format">
                                            <option <?php echo $settings['date_format'] === 'MM/DD/YYYY' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                                            <option <?php echo $settings['date_format'] === 'DD/MM/YYYY' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    Notification Preferences 🔔
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="desktopNotifications" name="desktop_notifications" <?php echo $settings['desktop_notifications'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="desktopNotifications">Enable Desktop Notifications</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="soundAlerts" name="sound_alerts" <?php echo $settings['sound_alerts'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="soundAlerts">Sound Alerts ON/OFF</label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications" <?php echo $settings['email_notifications'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="emailNotifications">Email Notification Toggle</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    Accessibility Options ♿
                                </div>
                                <div class="card-body">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="highContrastMode" name="high_contrast_mode" <?php echo $settings['high_contrast_mode'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="highContrastMode">High Contrast Mode</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="screenReader" name="screen_reader" <?php echo $settings['screen_reader'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="screenReader">Screen Reader Compatibility</label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Keyboard Shortcuts</label>
                                        <p><a href="#">Customize keyboard shortcuts</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mb-4">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById("menu-toggle").addEventListener("click", function(e) {
            e.preventDefault();
            document.getElementById("wrapper").classList.toggle("toggled");
        });

        // Apply settings dynamically
        const darkModeSwitch = document.getElementById('darkModeSwitch');
        const highContrastModeSwitch = document.getElementById('highContrastMode');
        const fontSizeSelect = document.getElementById('fontSizeSelect');
        const fontFamilySelect = document.getElementById('fontFamilySelect');
        const colorThemeSelect = document.getElementById('colorThemeSelect');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar-wrapper');

        darkModeSwitch.addEventListener('change', function() {
            document.body.classList.toggle('dark-mode', this.checked);
        });

        highContrastModeSwitch.addEventListener('change', function() {
            document.body.classList.toggle('high-contrast', this.checked);
        });

        fontSizeSelect.addEventListener('change', function() {
            let fontSize = '16px';
            if (this.value === 'Small') {
                fontSize = '14px';
            } else if (this.value === 'Large') {
                fontSize = '18px';
            }
            document.documentElement.style.setProperty('--font-size', fontSize);
        });

        fontFamilySelect.addEventListener('change', function() {
            document.documentElement.style.setProperty('--font-family', this.value);
        });

        colorThemeSelect.addEventListener('change', function() {
            document.documentElement.style.setProperty('--primary-color', this.value);
            selectedColorCircle.style.backgroundColor = this.value;
        });

        sidebarToggle.addEventListener('change', function() {
            sidebar.style.display = this.checked ? '' : 'none';
        });
    </script>
</body>
</html>
