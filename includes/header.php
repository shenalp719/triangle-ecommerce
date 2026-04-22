<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - Triangle Printing Solutions' : 'Triangle Printing Solutions - Web-to-Print E-commerce'; ?></title>
    
    <link rel="stylesheet" href="/triangle-ecommerce/assets/css/style.css">
    <link rel="stylesheet" href="/triangle-ecommerce/assets/css/responsive.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Dropdown Container */
        .nav-dropdown {
            position: relative;
            display: inline-block;
        }

        /* The actual dropdown menu */
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #222; /* Matches your dark theme */
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.4);
            z-index: 1000;
            border-radius: 4px;
            top: 100%;
            left: 0;
            padding: 0.5rem 0;
        }

        /* Links inside the dropdown */
        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background 0.2s;
            font-size: 0.9rem;
        }

        /* Hover effects */
        .dropdown-content a:hover {
            background-color: #E31E24; /* Triangle Primary Red */
            color: white;
        }

        /* Show the dropdown menu on hover */
        .nav-dropdown:hover .dropdown-content {
            display: block;
        }
        
        /* Mobile styling adjustment */
        .mobile-dropdown-group {
            background: rgba(255,255,255,0.05);
            padding-left: 1rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo-section">
                <a href="/triangle-ecommerce/" class="logo">
                    <span class="logo-triangle">△</span>
                    <span class="logo-text">TRIANGLE</span>
                </a>
            </div>
            
            <nav class="nav-menu">
                <a href="/triangle-ecommerce/" class="nav-link">Home</a>
                <a href="/triangle-ecommerce/products.php" class="nav-link">Products</a>
                
                <div class="nav-dropdown">
                    <a href="#" class="nav-link" style="cursor: default;">Customize ▾</a>
                    <div class="dropdown-content">
                        <a href="/triangle-ecommerce/customizer-frame.php">🖼️ Frame Posters</a>
                        <a href="/triangle-ecommerce/customizer-mug.php">☕ Custom Mugs</a>
                        <a href="/triangle-ecommerce/customizer-shirt.php">👕 T-Shirts</a>
                        <a href="/triangle-ecommerce/customizer-cap.php">🧢 Custom Caps</a>
                    </div>
                </div>

                <a href="/triangle-ecommerce/contact.php" class="nav-link">Contact</a>
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role'])): ?>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="/triangle-ecommerce/admin/" class="nav-link admin-link">Admin</a>
                    <?php endif; ?>
                    <a href="/triangle-ecommerce/dashboard.php" class="nav-link">Dashboard</a>
                    <a href="/triangle-ecommerce/logout.php" class="nav-link logout">Logout</a>
                <?php else: ?>
                    <a href="/triangle-ecommerce/login.php" class="nav-link">Login</a>
                <?php endif; ?>
            </nav>
            
            <div class="header-actions">
                <a href="/triangle-ecommerce/cart.php" class="cart-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <span class="cart-count" id="cart-count">0</span>
                </a>
                <button class="menu-toggle" id="menu-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <nav class="mobile-nav" id="mobile-nav">
        <a href="/triangle-ecommerce/" class="mobile-nav-link">Home</a>
        <a href="/triangle-ecommerce/products.php" class="mobile-nav-link">Products</a>
        
        <div style="color: white; padding: 1rem 2rem; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; color: #E31E24;">
            Customize Products:
        </div>
        <div class="mobile-dropdown-group">
            <a href="/triangle-ecommerce/customizer-frame.php" class="mobile-nav-link" style="font-size: 0.95rem; padding: 0.5rem 2rem;">- Frame Posters</a>
            <a href="/triangle-ecommerce/customizer-mug.php" class="mobile-nav-link" style="font-size: 0.95rem; padding: 0.5rem 2rem;">- Custom Mugs</a>
            <a href="/triangle-ecommerce/customizer-shirt.php" class="mobile-nav-link" style="font-size: 0.95rem; padding: 0.5rem 2rem;">- T-Shirts</a>
            <a href="/triangle-ecommerce/customizer-cap.php" class="mobile-nav-link" style="font-size: 0.95rem; padding: 0.5rem 2rem;">- Custom Caps</a>
        </div>

        <a href="/triangle-ecommerce/contact.php" class="mobile-nav-link">Contact</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/triangle-ecommerce/dashboard.php" class="mobile-nav-link">Dashboard</a>
            <a href="/triangle-ecommerce/logout.php" class="mobile-nav-link">Logout</a>
        <?php else: ?>
            <a href="/triangle-ecommerce/login.php" class="mobile-nav-link">Login</a>
        <?php endif; ?>
    </nav>

    <main class="main-content">