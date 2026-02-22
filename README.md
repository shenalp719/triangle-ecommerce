# Triangle Printing Solutions - Web-to-Print E-Commerce Platform

A modern, professional web-to-print e-commerce website built with vanilla HTML, CSS, JavaScript, and PHP. Complete customization engine using Fabric.js for designing frames, mugs, t-shirts, and caps.

## 🎨 Features

### Core E-Commerce
- **Product Catalog**: 4 product categories (Frames, Mugs, T-Shirts, Caps)
- **Shopping Cart**: Full cart management with quantity controls
- **User Authentication**: Secure login/register system
- **Order Management**: Track orders from creation to delivery
- **Payment Ready**: Order summary and checkout flow

### Advanced Customizer
- **Frame Designer**: Upload images, adjust zoom/rotation, safe print area overlay
- **Product Designer**: Customize mugs, shirts, and caps with text and images
- **Resolution Detection**: DPI warning for low-quality images
- **Design Saving**: Save designs to user account for later editing
- **HD Export**: Download designs as high-resolution PNG files

### User Features
- **User Dashboard**: View orders, saved designs, account settings
- **Design Library**: Access and manage saved designs
- **Order History**: Track order status and delivery
- **Profile Management**: Update personal and business information

### Admin Panel
- **Dashboard**: Real-time statistics and metrics
- **Order Management**: Update order status, track fulfillment
- **Customer Management**: View customer list and details
- **Designs Management**: Download and manage customer designs
- **Settings**: Configure pricing, shipping, quality standards

### Additional Features
- **AI Chatbot Widget**: Floating chatbot with quick action buttons
- **Responsive Design**: Fully mobile-friendly, works on all devices
- **Modern UI**: Professional design with Triangle brand colors
- **Clean Code**: Modular, well-commented, production-ready

## 🛠 Technical Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Canvas**: Fabric.js 5.3.0 for advanced design tools
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: XAMPP (Apache + MySQL)
- **Fonts**: Google Fonts (Inter, Poppins)

## 📦 Brand Colors

- **Primary Red**: #E31E24
- **Dark Gray/Black**: #111111
- **Medium Gray**: #777777
- **Light Gray**: #F5F5F5
- **White**: #FFFFFF

## 🚀 Installation & Setup

### Prerequisites
- XAMPP (or similar local server with PHP & MySQL)
- Browser (Chrome, Firefox, Safari, or Edge)
- Text Editor (VS Code recommended)

### Step 1: Extract Project
1. Extract the `triangle-ecommerce` folder to `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)
2. Ensure folder structure is: `htdocs/triangle-ecommerce/`

### Step 2: Start XAMPP
1. Open XAMPP Control Panel
2. Start Apache
3. Start MySQL

### Step 3: Initialize Database
1. Open browser and navigate to: `http://localhost/triangle-ecommerce/setup.php`
2. The page will create all necessary tables automatically
3. You should see success messages for each table

### Step 4: Access the Application

**Main Website:**
- Home: `http://localhost/triangle-ecommerce/`
- Products: `http://localhost/triangle-ecommerce/products.php`
- Customizer: `http://localhost/triangle-ecommerce/customizer-frame.php`
- Cart: `http://localhost/triangle-ecommerce/cart.php`
- Contact: `http://localhost/triangle-ecommerce/contact.php`

**User System:**
- Register: `http://localhost/triangle-ecommerce/register.php`
- Login: `http://localhost/triangle-ecommerce/login.php`
- Dashboard: `http://localhost/triangle-ecommerce/dashboard.php` (requires login)

**Admin Panel:**
- Admin Dashboard: `http://localhost/triangle-ecommerce/admin/`
- Orders: `http://localhost/triangle-ecommerce/admin/orders.php`
- Customers: `http://localhost/triangle-ecommerce/admin/customers.php`
- Products: `http://localhost/triangle-ecommerce/admin/products.php`
- Designs: `http://localhost/triangle-ecommerce/admin/designs.php`
- Settings: `http://localhost/triangle-ecommerce/admin/settings.php`

### Step 5: Create Demo Admin Account (Optional)
Use phpMyAdmin to insert an admin user:

```sql
INSERT INTO users (email, password, first_name, role) 
VALUES ('admin@triangleprinting.com', '$2y$10$...', 'Admin', 'admin');
```

## 📁 Project Structure

```
triangle-ecommerce/
├── index.php                      # Home page
├── products.php                   # Products listing
├── customizer-frame.php           # Frame designer
├── customizer-product.php         # Product designer (mug/shirt/cap)
├── cart.php                       # Shopping cart
├── checkout.php                   # Checkout (ready to implement)
├── login.php                      # User login
├── register.php                   # User registration
├── logout.php                     # Logout handler
├── dashboard.php                  # User dashboard
├── contact.php                    # Contact page
├── db.php                         # Database configuration
├── setup.php                      # Database initialization
├── 
├── admin/
│   ├── index.php                  # Admin dashboard
│   ├── orders.php                 # Order management
│   ├── customers.php              # Customer management
│   ├── products.php               # Product management
│   ├── designs.php                # Design management
│   └── settings.php               # Admin settings
│
├── api/
│   ├── save-design.php            # Save design API
│   └── create-order.php           # Create order API
│
├── includes/
│   ├── header.php                 # Header/navigation include
│   └── footer.php                 # Footer include
│
├── assets/
│   ├── css/
│   │   ├── style.css              # Main stylesheet (2000+ lines)
│   │   └── responsive.css         # Mobile responsive styles
│   └── js/
│       ├── app.js                 # Main app functionality
│       ├── cart.js                # Cart management
│       ├── chatbot.js             # Chatbot widget
│       └── customizer.js          # Fabric.js customizer
│
└── uploads/                       # User uploads directory
```

## 🎯 How to Use

### For Customers:

1. **Browse Products**
   - Visit `/products.php`
   - Filter by category
   - Click "Customize Now" to design

2. **Customize Products**
   - Use frame customizer at `/customizer-frame.php`
   - Or product customizer at `/customizer-product.php`
   - Upload images, add text, adjust properties
   - Click "Add to Cart" when done

3. **Manage Cart**
   - Visit `/cart.php`
   - Review items, update quantities
   - Click "Proceed to Checkout" (payment integration needed)

4. **Create Account & Save Designs**
   - Register at `/register.php`
   - Login to save designs to your account
   - Access saved designs from dashboard
   - Edit and re-order designs

### For Admins:

1. **Login to Admin Panel**
   - Navigate to `/admin/`
   - Use admin credentials

2. **Manage Orders**
   - View all orders on orders page
   - Update order status (processing → shipped → delivered)
   - View customer details and delivery info

3. **View Customer Designs**
   - Check `/admin/designs.php`
   - Download high-resolution design files
   - Export for printing

4. **Manage Products & Settings**
   - Update product pricing
   - Configure shipping rates
   - Set quality standards

## 🔐 Security Features

- **Password Hashing**: bcrypt hashing for secure password storage
- **SQL Injection Protection**: Prepared statements and sanitization
- **Session Management**: Secure PHP sessions with role-based access
- **Input Validation**: Server-side validation on all forms
- **CSRF Protection**: Can be added with tokens
- **Authentication Checks**: All protected pages verify user login and role

## 🚀 Performance Optimizations

- **Lazy Loading**: Images load on scroll
- **Cloud Canvas**: Fabric.js for efficient rendering
- **LocalStorage**: Cart saved in browser storage
- **Caching**: CSS and JS are minifiable
- **Responsive Images**: Mobile-first CSS approach

## 🔄 Database Schema

### Users Table
- id, email, password, first_name, last_name, phone, company, address, city, state, postal_code, country, role, created_at, updated_at

### Products Table
- id, name, description, category, base_price, image, specifications, available, created_at, updated_at

### Frames Table
- id, name, style, dimensions, price_multiplier, thumbnail, created_at

### Designs Table
- id, user_id, product_id, name, canvas_json, preview_image, width, height, resolution_dpi, created_at, updated_at

### Orders Table
- id, user_id, order_number, status, total_amount, delivery_address, delivery_notes, tracking_number, created_at, updated_at

### Order Items Table
- id, order_id, product_id, design_id, quantity, unit_price, print_file, created_at

## 📝 Customization Tips

### Add New Product
1. Insert into `products` table
2. Update product selector in `/customizer-product.php`
3. Add pricing to `customizer-product.php`

### Change Brand Colors
1. Edit CSS variables in `assets/css/style.css` (root section)
2. Update colors globally across entire site

### Add Payment Gateway
1. Integrate Stripe/PayPal API
2. Create `/checkout.php` with payment form
3. Handle payment response in `/api/process-payment.php`

### Add Email Notifications
1. Use PHPMailer library
2. Send confirmation emails on order creation
3. Send status updates when order changes

## 🐛 Troubleshooting

**Issue: Database connection failed**
- Check MySQL is running in XAMPP
- Verify credentials in `db.php`
- Ensure database `triangle_ecommerce` exists

**Issue: Design not uploading**
- Check file size (max 10MB)
- Verify image format (JPG/PNG)
- Check uploads folder permissions

**Issue: Customizer canvas not showing**
- Ensure Fabric.js is loaded: `<script src="...fabric.min.js"></script>`
- Check browser console for errors
- Verify JavaScript is enabled

**Issue: Admin panel won't load**
- Confirm user has `role = 'admin'` in database
- Clear browser cache and cookies
- Check PHP error logs

## 📱 Responsive Behavior

- **Desktop (1024px+)**: Full 4-column product grid, side panel customizer
- **Tablet (768px-1023px)**: 2-column product grid, stacked layouts
- **Mobile (below 768px)**: 1-column grid, full-screen canvas, bottom toolbar

## 🎓 Learning Resources

- **Fabric.js Documentation**: http://fabricjs.com
- **PHP Documentation**: https://www.php.net/docs.php
- **MySQL Documentation**: https://dev.mysql.com/doc/
- **CSS Grid Guide**: https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Grid_Layout

## 📄 License

This project is built for Triangle Printing Solutions. All rights reserved.

## 👨‍💻 Developer Notes

### Clean Code Principles Applied:
✓ Modular PHP structure with includes
✓ Semantic HTML5 markup
✓ CSS variables for maintainability
✓ JavaScript event delegation
✓ Progressive enhancement
✓ Responsive first approach
✓ Clear naming conventions
✓ Extensive comments

### Future Enhancements:
- [ ] Payment gateway integration (Stripe/PayPal)
- [ ] Email notifications
- [ ] Advanced analytics dashboard
- [ ] Image optimization/compression
- [ ] CDN integration
- [ ] API rate limiting
- [ ] Two-factor authentication
- [ ] Design templates library
- [ ] Bulk order processing
- [ ] Inventory management

## 🤝 Support

For customization or additional features, please contact the development team.

---

**Built with ❤️ for Triangle Printing Solutions**
*A modern, professional web-to-print e-commerce platform*
