# Complaint Management System

A web-based application built using PHP and MySQL that allows customers to submit complaints, track their status, and receive updates, while enabling technicians and administrators to manage and resolve those complaints.

---

## 📌 Project Overview

This application simulates a real-world customer complaint management system with a complete workflow:

> Customer → Administrator → Technician → Resolution

Each role interacts with the system through a secure, role-based interface, ensuring proper access control and data integrity.

### 👥 User Roles

- **Customer**
  - Register and log in
  - Submit complaints with image upload
  - View complaint status and technician notes
  - Update profile information
  - Change password
- **Technician**
  - View assigned complaints
  - Add technician notes
  - Mark complaints as resolved
  - Change password
- **Administrator**
  - View all open complaints
  - Assign unassigned complaints to technicians
  - View and edit customer profiles
  - View, add, and edit employee accounts
  - View technician workload report
  - Change password

---

## 🛠️ Technologies Used

- PHP
- MySQL
- HTML / CSS
- XAMPP (Apache + MySQL)
- phpMyAdmin

---

## 📁 Project Structure

```text
complaint_management_system/
├── app/
│   ├── controllers/              # Controller classes
│   │   ├── AuthController.php    # Handles login and registration
│   │   └── ComplaintController.php
│   ├── models/                   # Model classes (database layer)
│   │   ├── User.php
│   │   ├── Customer.php
│   │   ├── Complaint.php
│   │   ├── Technician.php
│   │   └── Admin.php
│   └── views/                    # View files (pages users visit)
│       ├── login.php
│       ├── register.php
│       ├── logout.php
│       ├── change_password.php
│       ├── customer_dashboard.php
│       ├── customer_complaint_detail.php
│       ├── submit_complaint.php
│       ├── view_complaints.php
│       ├── update_profile.php
│       ├── technician_dashboard.php
│       ├── technician_complaint_detail.php
│       ├── admin_dashboard.php
│       ├── admin_open_complaints.php
│       ├── admin_assign_complaints.php
│       ├── admin_view_customers.php
│       ├── admin_edit_customer.php
│       ├── admin_view_employees.php
│       ├── admin_add_employee.php
│       ├── admin_edit_employee.php
│       └── admin_workload_report.php
├── assets/
│   ├── css/style.css
│   └── uploads/                  # Uploaded complaint images
├── config/
│   └── database.php              # PDO database connection
├── sql/
│   └── complaint_management_system.sql
└── index.php                     # Main menu (links to views)
```

---

## 🗄️ Database Structure

The application uses a relational database with the following key tables:

- `users` – stores login credentials and roles
- `customers` – stores customer-specific information
- `employees` – stores technician/admin information
- `complaints` – stores complaint details
- `products_services` – list of available services
- `complaint_categories` – complaint types
- `technician_notes` – notes added by technicians
- `complaint_assignments` – tracks complaint assignments
- `complaint_images` – stores uploaded images

---

## ✨ Features Implemented

### 👤 Customer

- User registration with full profile details
- Secure login with password hashing
- Update profile information (PRG pattern implemented)
- Change password with complexity enforcement and reuse prevention
- Submit complaints with:
  - product/service selection
  - complaint category selection
  - description validation (max 2000 characters)
  - optional image upload
- View all submitted complaints
- View detailed complaint page including:
  - complaint status
  - technician notes
  - resolution notes

### 🛠️ Technician

- Secure login
- View assigned complaints
- View full complaint details including customer information
- Add technician notes (blocked once complaint is resolved)
- Mark complaints as resolved with required resolution notes
- Automatically stores resolution date and resolution notes
- Change password with complexity enforcement
- Prevents invalid actions:
  - cannot re-resolve a complaint
  - cannot add notes after resolution

### 🧑‍💼 Administrator

- Secure login
- View all open complaints with assignment status
- Assign unassigned complaints to technicians
- View all customer accounts
- Edit customer profiles (first name, last name)
- View all employee accounts (technicians and administrators)
- Add new employee accounts (technician or administrator role)
- Edit employee names
- View technician workload report (open, resolved, and total complaints per technician)
- Change password with complexity enforcement

### 🔐 System Features

- Role-based access control (Customer / Technician / Admin)
- Session-based authentication
- Full server-side validation aligned with database schema:
  - length validation
  - format validation (email, phone, ZIP, state)
  - password complexity enforcement (min 8 characters, uppercase, lowercase, number)
- Prepared statements for all database interactions
- PRG (Post/Redirect/Get) pattern to prevent duplicate form submissions

---

## 🚀 How to Run the Project

1. Install XAMPP
2. Start Apache and MySQL
3. Place the project folder in:
   `htdocs/`
4. Import the database:
   - Open phpMyAdmin
   - Create database: `complaint_management_system`
   - Import `sql/complaint_management_system.sql`

5. Open the application in browser:
   `http://localhost/complaint_management_system/`

6. Seed technician and administrator accounts directly in the database:
   - Open `hash.php` and replace `"Admin1234"` with the password you want to use
   - Visit `http://localhost/complaint_management_system/hash.php` in your browser
   - Copy the long hash string that appears on the page
   - In phpMyAdmin, insert a row into `users` with:
     - `email` = the account's email address
     - `password_hash` = the copied hash string
     - `role` = `technician` or `administrator`
   - Insert a matching row into `employees` with the `user_id` from the inserted user

---

## 🔐 Default Behavior

- New users are registered as **customers**
- Passwords are securely hashed using PHP's `password_hash()`
- Sessions are used to manage login state

---

## 📸 Screenshots

### Registration

![Registration Page](/assets/screenshots/register-page.png)

### Customer Dashboard

![Customer Dashboard](/assets/screenshots/customer-dashboard.png)

### Submit Complaint

![Submit Complaint Form](/assets/screenshots/submit-complaint-form.png)

### Customer Complaint Tracking

![Customer Complaints List](/assets/screenshots/customer-complaints-list.png)

### Complaint Detail View

![Customer Complaint Detail](/assets/screenshots/customer-complaint-detail.png)

### Technician Dashboard

![Technician Dashboard](/assets/screenshots/technician-dashboard.png)

### Technician Complaint Management

![Technician Complaint Detail](/assets/screenshots/technician-complaint-detail.png)

### Admin Dashboard

![Admin Dashboard](/assets/screenshots/admin-dashboard.png)

### Complaint Assignment

![Assign Complaints](/assets/screenshots/assign-complaints.png)

### Employee Management

![Admin View Employees](/assets/screenshots/admin-view-employees.png)

### Workload Reporting

![Technician Workload Report](/assets/screenshots/technician-workload-report.png)

---

## 📚 Lessons Learned

- Designing relational databases and table relationships
- Separating authentication data from user-specific data
- Implementing secure login systems with sessions
- Debugging PHP and database integration issues
- Aligning application validation rules with database schema constraints
- Implementing workflow-based validation to prevent invalid system states

---

---

## 📊 Project Summary

The Complaint Management System is a fully functional web application designed to simulate a real-world service workflow between customers, administrators, and technicians.

This project demonstrates full-stack development using PHP and MySQL, while applying key software engineering concepts such as MVC architecture, relational database design, role-based access control, and secure authentication.

Throughout development, significant emphasis was placed on:

- Data validation and security
- Workflow integrity and role enforcement
- User experience and interface consistency
- Incremental testing and debugging

The system successfully supports the complete lifecycle of a complaint, from submission to resolution, with clear separation of responsibilities across user roles.

This project reflects my ability to design, build, and debug a multi-role application while following structured development practices, making it a strong addition to my software development portfolio.

## Walk through

![demo](https://youtu.be/_hQRRqpR7x0)

## 👤 Author

Sherika Fayson

---

## 📄 License

This project is for educational purposes.
