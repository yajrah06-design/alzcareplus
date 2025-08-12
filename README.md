# AlzCare+ Admin Dashboard

A comprehensive healthcare management system designed for monitoring Alzheimer's patients with real-time health tracking, medication management, and GPS monitoring capabilities.

## 🏥 Features

### 🔐 Authentication & Security
- **Role-based Access Control**: Admin and Caregiver roles with different permissions
- **Secure Login System**: Session-based authentication with password protection
- **Demo Credentials**: Pre-configured accounts for testing

### 📊 Dashboard Overview
- **Real-time Metrics**: Active patients, alerts, and pending medications
- **Critical Alerts**: Prominent display of urgent notifications
- **Recent Activity**: Latest patient alerts and status updates
- **Interactive Cards**: Clickable summary cards with hover effects

### 👥 Patient Management
- **Patient Profiles**: Complete patient information with guardian details
- **Alzheimer's Stages**: Track patient progression (Stage 1, 2, 3)
- **Search & Filter**: Find patients by name, ID, or stage
- **CRUD Operations**: Add, edit, view, and delete patient records

### 💊 Health Monitoring
- **Vital Signs Tracking**: Heart rate, blood pressure, blood oxygen, temperature
- **Real-time Updates**: Simulated live data updates
- **Status Indicators**: Normal, Elevated, Critical status badges
- **Interactive Charts**: Visual representation of health trends
- **Detailed Views**: Click on vital signs for detailed history

### 💊 Medication Management
- **Medication Records**: Track prescriptions, dosages, and schedules
- **Adherence Monitoring**: Visual donut chart showing medication compliance
- **Status Tracking**: Pending, Missed, Taken status indicators
- **Reminder System**: Next dose scheduling and notifications

### 🚨 Alerts & Notifications
- **Alert Configuration**: Customizable alert rules and thresholds
- **Multiple Types**: Heart rate, medication, GPS boundary, activity alerts
- **Notification Methods**: SMS, App, Email, Phone call options
- **Alert History**: Complete log of all triggered alerts
- **Status Tracking**: Critical, Warning status management

### 👤 User Management (Admin Only)
- **User Accounts**: Manage admin, nurse, caregiver, and family accounts
- **Role Assignment**: Assign specific roles and permissions
- **Activity Monitoring**: Track login frequency and user activity
- **Account Status**: Active/Inactive status management

### 📈 Analytics & Reports (Admin Only)
- **Patient Distribution**: Visual breakdown by Alzheimer's stage
- **Alert Analysis**: Chart showing alert types and frequencies
- **Trend Analysis**: Monthly patient activity trends
- **Performance Metrics**: Response times and adherence rates

### ⚙️ System Settings (Admin Only)
- **General Configuration**: System name, thresholds, date formats
- **Notification Settings**: Enable/disable various notification methods
- **Data Retention**: Configurable data retention periods
- **System Preferences**: Customizable system parameters

### 📍 GPS Tracking (Placeholder)
- **Location Monitoring**: Patient GPS coordinates and addresses
- **Boundary Alerts**: Safe zone and boundary violation tracking
- **Status Indicators**: Safe Zone, Near Boundary, Outside Zone
- **Mobile App Integration**: Ready for real-time GPS data from mobile app

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- Web server (Apache/Nginx)
- Modern web browser

### Setup Instructions

1. **Clone or Download**
   ```bash
   # If using git
   git clone [repository-url]
   cd AlzCare-Plus-Admin
   
   # Or download and extract the ZIP file
   ```

2. **Web Server Configuration**
   - Place all files in your web server's document root (e.g., `htdocs/` for XAMPP)
   - Ensure PHP is enabled on your web server

3. **Access the Application**
   - Open your web browser
   - Navigate to `http://localhost/AlzCare-Plus-Admin/`
   - You should see the login page

4. **Login Credentials**
   ```
   Admin Account:
   Email: admin@alzcare.com
   Password: admin123
   
   Caregiver Account:
   Email: caregiver@alzcare.com
   Password: caregiver123
   ```

## 📁 File Structure

```
AlzCare-Plus-Admin/
├── index.php                 # Login page
├── dashboard.php             # Main dashboard
├── patient-profiles.php      # Patient management
├── health-monitoring.php     # Health tracking
├── medication-tracker.php    # Medication management
├── alerts.php               # Alerts & notifications
├── user-accounts.php        # User management (admin)
├── analytics.php            # Analytics & reports (admin)
├── system-settings.php      # System configuration (admin)
├── gps-tracking.php         # GPS monitoring (placeholder)
├── logout.php               # Logout functionality
├── includes/
│   └── sidebar.php          # Navigation sidebar
├── assets/
│   ├── css/
│   │   └── style.css        # Main stylesheet
│   └── js/
│       ├── login.js         # Login functionality
│       ├── dashboard.js     # Dashboard interactions
│       ├── patient-profiles.js
│       ├── health-monitoring.js
│       ├── medication-tracker.js
│       ├── alerts.js
│       ├── user-accounts.js
│       ├── analytics.js
│       ├── system-settings.js
│       └── gps-tracking.js
└── README.md                # This file
```

## 🎨 Design Features

### Modern UI/UX
- **Responsive Design**: Works on desktop, tablet, and mobile devices
- **Clean Interface**: Modern, professional healthcare dashboard design
- **Color-coded Status**: Intuitive color system for different statuses
- **Interactive Elements**: Hover effects, animations, and smooth transitions

### Navigation
- **Sidebar Navigation**: Collapsible sidebar with role-based menu items
- **Active Page Highlighting**: Clear indication of current page
- **Breadcrumb Navigation**: Easy navigation between sections

### Data Visualization
- **Charts & Graphs**: Interactive charts using Chart.js
- **Status Badges**: Color-coded status indicators
- **Progress Indicators**: Visual progress tracking
- **Real-time Updates**: Simulated live data updates

## 🔧 Customization

### Adding New Features
1. Create new PHP files for additional pages
2. Add navigation items to `includes/sidebar.php`
3. Create corresponding JavaScript files in `assets/js/`
4. Update CSS styles in `assets/css/style.css`

### Database Integration
The current version uses sample data arrays. To integrate with a database:

1. **Create Database Tables**:
   ```sql
   -- Users table
   CREATE TABLE users (
       id INT PRIMARY KEY AUTO_INCREMENT,
       name VARCHAR(255),
       email VARCHAR(255) UNIQUE,
       password VARCHAR(255),
       role ENUM('admin', 'caregiver', 'nurse', 'family'),
       status ENUM('active', 'inactive') DEFAULT 'active',
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   -- Patients table
   CREATE TABLE patients (
       id INT PRIMARY KEY AUTO_INCREMENT,
       patient_id VARCHAR(50) UNIQUE,
       name VARCHAR(255),
       guardian VARCHAR(255),
       stage ENUM('Stage 1', 'Stage 2', 'Stage 3'),
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   -- Health records table
   CREATE TABLE health_records (
       id INT PRIMARY KEY AUTO_INCREMENT,
       patient_id VARCHAR(50),
       heart_rate INT,
       blood_pressure VARCHAR(20),
       blood_oxygen INT,
       temperature DECIMAL(4,1),
       status ENUM('Normal', 'Elevated', 'Critical'),
       recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

2. **Update PHP Files**: Replace sample data arrays with database queries
3. **Add Connection File**: Create `includes/database.php` for database connection

### Mobile App Integration
For GPS tracking and real-time data:

1. **API Endpoints**: Create REST API endpoints for mobile app communication
2. **Real-time Updates**: Implement WebSocket or Server-Sent Events
3. **Data Synchronization**: Set up data sync between mobile app and web dashboard

## 🛡️ Security Considerations

### Current Implementation
- Session-based authentication
- Input validation and sanitization
- Role-based access control
- XSS protection with `htmlspecialchars()`

### Recommended Enhancements
- **Password Hashing**: Use `password_hash()` and `password_verify()`
- **HTTPS**: Enable SSL/TLS encryption
- **SQL Injection Protection**: Use prepared statements
- **CSRF Protection**: Implement CSRF tokens
- **Rate Limiting**: Add login attempt limitations
- **Input Validation**: Enhanced server-side validation

## 📱 Mobile Responsiveness

The dashboard is fully responsive and works on:
- **Desktop**: Full-featured experience
- **Tablet**: Optimized layout with touch-friendly elements
- **Mobile**: Collapsible sidebar and mobile-optimized tables

## 🔄 Future Enhancements

### Planned Features
- **Real-time Chat**: Communication between caregivers and family
- **Video Calls**: Integrated video consultation feature
- **AI-powered Alerts**: Machine learning for predictive alerts
- **Advanced Analytics**: More detailed reporting and insights
- **Multi-language Support**: Internationalization support
- **API Documentation**: Complete API documentation for mobile integration

### Technical Improvements
- **Database Integration**: Full database implementation
- **Real-time Updates**: WebSocket implementation
- **Push Notifications**: Browser push notifications
- **Offline Support**: Progressive Web App features
- **Performance Optimization**: Caching and optimization

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation

## 🙏 Acknowledgments

- **Chart.js** for data visualization
- **Font Awesome** for icons
- **Inter Font** for typography
- **Tailwind CSS** inspiration for design system

---

**AlzCare+ Admin Dashboard** - Empowering healthcare professionals to provide better care for Alzheimer's patients through comprehensive monitoring and management tools. 