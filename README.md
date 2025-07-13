# FreshMart Grocery Store - CSY2088 Group Project

## Project Overview
FreshMart is a comprehensive web-based grocery store application designed to address the challenges of traditional grocery shopping by providing an online platform for customers to browse, select, and purchase groceries from the comfort of their homes.

## Team Members
- **24814107** - Utsab Thami Magar
- **24812606** - Anuskar Sigdel  
- **24812931** - Ayush Karanjit (aayush.2024105@nami.edu.np)
- **24812945** - Sandhaya Kumari
- **24812923** - Jesina Bastola

## Features
- User Registration and Authentication
- Product Catalog with Categories
- Shopping Cart and Checkout
- Order Management
- Admin Panel for Product Management
- User Profile Management
- Responsive Design

## Technology Stack
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap
- **Backend**: PHP 8.0+
- **Database**: MySQL 8.0+
- **Server**: Apache (XAMPP/WAMP recommended)

## Installation Guide

### Prerequisites
- XAMPP/WAMP/MAMP server
- Web browser (Chrome, Firefox, Safari)
- Text editor (VS Code recommended)

### Setup Instructions
1. Clone/download the project to your local machine
2. Copy the project folder to your web server directory (htdocs for XAMPP)
3. Start Apache and MySQL services
4. Import the database:
   - Open phpMyAdmin
   - Create a new database named `freshmart_db`
   - Import the `database/freshmart_db.sql` file
5. Configure database connection in `config/database.php`
6. Access the application at `http://localhost/freshmart/`

## Project Structure
```
freshmart/
├── index.php                 # Homepage
├── config/                   # Configuration files
├── includes/                 # Common includes (header, footer, etc.)
├── pages/                    # Main application pages
├── admin/                    # Admin panel
├── assets/                   # CSS, JS, images
├── database/                 # Database schema and setup
├── uploads/                  # Product images
├── documentation/            # Project documentation
└── tests/                    # Testing files
```

## Default Login Credentials
### Admin
- Email: admin@freshmart.com
- Password: admin123

### Customer (Test Account)
- Email: customer@test.com
- Password: customer123

## Features Overview

### Customer Features
- Browse products by categories
- Search functionality
- Add/remove items from cart
- User registration and login
- View order history
- Update profile information

### Admin Features
- Manage products (Add, Edit, Delete)
- Manage categories
- View and manage orders
- View customer information
- Dashboard with analytics

## Testing
The application includes comprehensive testing:
- Unit tests for individual functions
- Integration tests for system components
- User acceptance testing scenarios

## Documentation
Complete project documentation is available in the `documentation/` folder, including:
- Technical requirements
- System design documents
- API documentation
- User manual

## License
This project is developed for educational purposes as part of CSY2088 Group Project coursework.
