# 🎫 Professional Discount Management System

A modern, professional discount management system built with PHP and SQLite. Features a beautiful responsive interface with advanced discount stacking capabilities.

## ✨ Features

- **💳 Auto Card Lookup** - Simply enter card number (001, 002, etc.)
- **📊 Dropdown Percentages** - Choose from 10%, 20%, 30%, 40%, 50% discounts
- **🎯 Stacking Discounts** - Apply multiple discounts to the same card
- **📱 Responsive Design** - Works on desktop, tablet, and mobile
- **🎨 Modern UI** - Beautiful gradient design with Bootstrap 5
- **📈 Real-time Tracking** - Live date/time display
- **🗃️ Scrollable Tables** - Clean data presentation with max 6 rows
- **🗑️ Database Management** - Easy clear/reset functionality

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: SQLite (lightweight, no setup required)
- **Frontend**: Bootstrap 5.3.0, Custom CSS with gradients
- **Architecture**: Modular MVC-like structure

## 📁 Project Structure

```
Discount/
├── public/                 # Web accessible files
│   ├── index.php          # Main application entry point
│   └── assets/
│       └── css/
│           └── style.css  # Custom styling
├── src/                   # Core application classes
│   ├── DatabaseManager.php
│   └── DiscountController.php
├── config/                # Configuration files
│   ├── app.php
│   └── database.php
├── storage/               # Database storage
│   └── discount_system.db
└── README.md
```

## 🚀 Quick Start

1. **Download/Clone the project**
   ```bash
   # If using Git
   git clone [your-github-repo-url]
   cd discount-management-system
   
   # Or download ZIP and extract
   ```

2. **Start PHP development server**
   ```bash
   cd public
   php -S localhost:8000
   ```

3. **Open in browser**
   ```
   http://localhost:8000
   ```

That's it! The database will be automatically created and seeded with sample data.

## 🎮 How to Use

### **Apply Discounts**
1. Enter a card number (001, 002, 003, or 004)
2. Select discount percentage from dropdown
3. Click "⚡ Apply" button

### **Stacking Discounts**
1. Apply a discount to any card
2. Apply another discount to the same card
3. View the stacked total in the "Stacked Discounts Summary"

### **Available Sample Cards**
- **Card 001** → Ticket #1 • Electronics Store
- **Card 002** → Ticket #2 • Fashion Boutique  
- **Card 003** → Ticket #3 • Electronics Store
- **Card 004** → Ticket #4 • Grocery Mart

## 🎯 Key Features Explained

### **Auto Card Lookup**
- Type just `001` and system finds `CARD001`
- Works with both short (001) and full (CARD001) formats

### **Discount Stacking**
- Apply multiple discounts to same card
- Automatically calculates total percentage
- Maximum 100% total protection
- Real-time summary display

### **Professional Design**
- Gradient form backgrounds
- Smooth hover animations
- Badge-based data display
- Color-coded sections
- Scrollable tables (max 6 rows visible)

### **Database Management**
- **Clear Discounts Only**: Removes discount history, keeps cards
- **Reset Everything**: Complete fresh start with sample data

## ⚙️ Configuration

Edit `config/app.php` to customize:

```php
'discount' => [
    'allowed_percentages' => [10, 20, 30, 40, 50],  // Available discount options
    'max_total_percentage' => 100                    // Maximum stacked total
],
'display' => [
    'recent_discounts_limit' => 20                   // Number of recent discounts to show
]
```

## 🔧 Requirements

- **PHP 7.4 or higher**
- **SQLite extension** (usually included with PHP)
- **Web server** (Apache, Nginx, or PHP dev server)

## 📱 Screenshots

### Main Interface
- Beautiful gradient form for applying discounts
- Real-time transaction table with live date/time
- Professional badge-based data display

### Features Demo
- Auto card lookup functionality
- Stacking discounts with live totals
- Collapsible sections for clean interface
- Database management tools

## 🏗️ Architecture

### **Modular Design**
- **DatabaseManager**: Handles all database operations
- **DiscountController**: Business logic and validation
- **Configuration**: Centralized settings management
- **Professional Structure**: Organized file hierarchy

### **Security Features**
- SQL injection protection with prepared statements
- Input validation and sanitization
- Session-based message handling
- POST-redirect-GET pattern implementation

## 📝 Author

**JosephK** - Professional Discount Management System

## 🔄 Version History

- **v1.0.0** - Initial release with full functionality
  - Auto card lookup system
  - Dropdown percentage selection
  - Advanced stacking discounts
  - Modern responsive design
  - Database management tools
  - Real-time date/time display
  - Scrollable table interface

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 📧 Support

For questions or issues:
1. Check the FAQ above
2. Review the configuration options
3. Open an issue on GitHub

---

*Built with ❤️ by JosephK - Professional Discount Management Solution*
