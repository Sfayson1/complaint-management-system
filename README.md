# Complaint Management System

A web-based application built using PHP and MySQL that allows customers to submit complaints, track their status, and receive updates, while enabling technicians and administrators to manage and resolve those complaints.

---

## 📌 Project Overview

This application simulates a real-world customer complaint management system. It supports multiple user roles and ensures that each user can only access features relevant to their role.

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

- User registration (customers)
- Secure login system using password hashing
- Session-based authentication
- Role-based access control
- Personalized dashboard (displays user’s first name)
- Complaint submission form
- Dropdown selection for products/services and complaint categories
- View submitted complaints

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

---

## 🔮 Future Improvements

- File/image upload for complaints
- Admin dashboard for assigning complaints
- Technician dashboard for managing tasks
- Improved UI/UX design
- Input validation enhancements

---

## 👤 Author

**Sherika Fayson**

---

## 📄 License

This project is for educational purposes.
