# LuxeRent - Premium Rental Marketplace

A futuristic luxury rental marketplace website built with PHP, MySQL, HTML5, CSS3, Bootstrap 5, JavaScript, GSAP animations, and modern UI design principles.

![LuxeRent](https://img.shields.io/badge/LuxeRent-Premium%20Rentals-667eea?style=for-the-badge)

## Features

### Frontend
- **Cinematic Hero Section** with fullscreen autoplay video background
- **Glassmorphism Navigation** with transparent glass effect and smooth scroll transitions
- **GSAP Animations** - fade-up, parallax, stagger, scroll-triggered animations
- **AOS (Animate on Scroll)** for section reveal animations
- **Swiper.js** testimonial carousel with auto-play
- **Dark Mode** toggle with localStorage persistence
- **Live Search** with AJAX-powered instant results
- **Animated Counters** with intersection observer
- **Cursor Glow Effect** following mouse movement
- **Floating Cards & Hover Effects** - lift, glow, scale animations
- **Responsive Design** - fully mobile-optimized
- **Loading Screen** with progress bar animation
- **Back to Top** button with smooth scroll
- **Infinite Scroll** on products page

### Backend
- **User Authentication** - registration, login, logout with password hashing
- **Product Management** - CRUD operations, categories, filters, sorting
- **Booking System** - date picker, price calculator, booking management
- **Wishlist** - AJAX-powered add/remove with heart toggle
- **Recently Viewed** - tracking and display
- **Notifications** - system notifications for bookings
- **Contact Form** - message storage and admin viewing
- **Admin Dashboard** - full management panel with analytics

### Admin Panel
- **Dashboard** with stats cards and revenue chart
- **Products Management** - view, delete products
- **Bookings Management** - view, update status
- **User Management** - view, change roles
- **Messages** - view contact form submissions
- **Analytics** - Revenue trends, booking status, category distribution charts
- **Chart.js** powered visualizations

## Tech Stack

| Technology | Purpose |
|---|---|
| PHP | Backend logic & API |
| MySQL | Database (XAMPP) |
| HTML5 | Page structure |
| CSS3 | Styling & animations |
| Bootstrap 5 | Responsive grid & components |
| JavaScript / jQuery | Interactivity |
| GSAP | Premium animations |
| AOS | Scroll animations |
| Swiper.js | Carousels |
| Chart.js | Admin analytics |
| Font Awesome | Icons |

## Installation

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Web browser

### Setup

1. **Clone or copy** this project into your XAMPP `htdocs` folder:
   ```
   C:\xampp\htdocs\premium-rental-website
   ```

2. **Start XAMPP** - Start Apache and MySQL services

3. **Create Database** - Open phpMyAdmin (`http://localhost/phpmyadmin`) and:
   - Import `database/schema.sql`
   - Or run the SQL file manually

4. **Configure Database** - Edit `includes/db.php` if your MySQL credentials differ:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'premium_rental');
   ```

5. **Access the website**:
   ```
   http://localhost/premium-rental-website
   ```

### Default Admin Login
- **Email:** admin@rental.com
- **Password:** password

## Folder Structure

```
premium-rental-website/
├── assets/
│   ├── css/
│   │   ├── style.css          # Main styles
│   │   └── animations.css     # Animation styles
│   ├── js/
│   │   ├── main.js            # Core JavaScript
│   │   └── animations.js      # GSAP animations
│   ├── images/
│   ├── videos/
│   └── uploads/
├── admin/
├── includes/
│   ├── ajax/
│   │   ├── search.php         # Live search endpoint
│   │   ├── wishlist.php       # Wishlist toggle
│   │   └── load-products.php  # Infinite scroll
│   ├── db.php                 # Database connection
│   ├── auth.php               # Authentication functions
│   ├── header.php             # Site header
│   └── footer.php             # Site footer
├── database/
│   └── schema.sql             # Database schema + seed data
├── index.php                  # Homepage
├── login.php                  # Sign in page
├── register.php               # Registration page
├── products.php               # Products listing
├── product-details.php        # Single product view
├── booking.php                # Booking confirmation
├── dashboard.php              # User dashboard
├── admin-panel.php            # Admin panel
├── wishlist.php               # User wishlist
├── profile.php                # User profile
├── contact.php                # Contact page
├── logout.php                 # Logout handler
└── README.md
```

## Pages

| Page | Description |
|---|---|
| `index.php` | Homepage with hero video, trending products, categories, showcase, reviews, stats |
| `login.php` | Glassmorphism sign-in with video background |
| `register.php` | Account creation with glass UI |
| `products.php` | Product listing with filters, sorting, infinite scroll |
| `product-details.php` | Product detail with gallery, booking calculator, reviews |
| `booking.php` | Booking confirmation page |
| `dashboard.php` | User dashboard with stats and booking history |
| `wishlist.php` | Saved products |
| `profile.php` | Account settings and password change |
| `contact.php` | Contact form with info cards |
| `admin-panel.php` | Full admin dashboard with analytics |

## Design Inspiration

- Apple product websites (clean, minimal)
- Airbnb premium UI (card layouts, smooth interactions)
- Tesla cinematic scrolling (video backgrounds, parallax)

## Categories

Electronics | Cameras | Bikes | Gaming | Furniture | Fashion | Projectors | Speakers | Event Items | Daily Use

## License

This project is for educational purposes.
