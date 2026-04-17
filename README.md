# Complaint Management System

A web-based application built using PHP and MySQL that allows customers to submit complaints, track their status, and receive updates, while enabling technicians and administrators to manage and resolve those complaints.

---

## 📌 Project Overview

This application simulates a real-world customer complaint management system with a complete workflow:

Customer → Administrator → Technician → Resolution

Each role interacts with the system through a secure, role-based interface, ensuring proper access control and data integrity.

### 👥 User Roles
- **Customer**
  - Register and log in
  - Submit complaints
  - View complaint status
- **Technician**
  - View assigned complaints
  - Add notes
  - Mark complaints as resolved
- **Administrator**
  - Manage users
  - Assign complaints to technicians
  - Monitor system activity

---

## 🛠️ Technologies Used

- PHP
- MySQL
- HTML
- XAMPP (Apache + MySQL)
- phpMyAdmin

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
- Secure login system with password hashing
- Update profile information (PRG pattern implemented)
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
- Add technician notes (blocked if complaint is resolved)
- Mark complaints as resolved
- Automatically store:
  - resolution date
  - resolution notes
- Prevent invalid actions:
  - cannot re-resolve a complaint
  - cannot add notes after resolution

### 🧑‍💼 Administrator
- Secure login
- View open complaints
- Assign complaints to technicians
- Input validation for assignment actions

### 🔐 System Features
- Role-based access control (Customer / Technician / Admin)
- Session-based authentication
- Full server-side validation aligned with database schema:
  - length validation
  - format validation (email, phone, ZIP, state)
  - password complexity enforcement
- Prepared statements for all database interactions
- PRG (Post/Redirect/Get) pattern to prevent duplicate form submissions

---

## 🚀 How to Run the Project

1. Install XAMPP
2. Start Apache and MySQL
3. Place the project folder in:
   htdocs/
4. Import the database:
- Open phpMyAdmin
- Create database: `complaint_management_system`
- Import SQL file (if provided)

5. Open the application in browser:
   http://localhost/complaint_management_system/public/

---

## 🔐 Default Behavior

- New users are registered as **customers**
- Passwords are securely hashed using PHP's `password_hash()`
- Sessions are used to manage login state

---

## 📸 Screenshots



---

## 📚 Lessons Learned

- Designing relational databases and table relationships
- Separating authentication data from user-specific data
- Implementing secure login systems with sessions
- Debugging PHP and database integration issues
- Aligning application validation rules with database schema constraints
- Implementing workflow-based validation to prevent invalid system states

---

## 🔮 Future Improvements

- Change password functionality for all user roles
- Admin management pages:
  - view/update customers
  - add/update employees
- Admin reporting:
  - unassigned complaints view
  - technician workload tracking
- Optional: switch login from email to user ID for strict requirement alignment
- UI/UX enhancements and responsive design improvements

---

## 👤 Author

**Sherika Fayson**

---

## 📄 License

This project is for educational purposes.
