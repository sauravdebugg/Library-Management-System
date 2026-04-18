# 📚 Library Management System

A complete **Full-Stack Library Management System** built using **Core PHP, MySQL, HTML, CSS, and Bootstrap 5**.
This project is designed for **admin (librarian) use** to manage books, students, and book transactions efficiently.

---

## 🚀 Features

### 🔐 Authentication

* Admin Login & Logout
* Session-based authentication
* Protected dashboard

### 📊 Dashboard

* Total Books
* Total Students
* Issued Books Count
* Returned Books Count
* Available Books

### 📚 Book Management

* Add new books
* Edit book details
* Delete books
* View all books
* Search books
* Update quantity

### 👨‍🎓 Student Management

* Add student
* Edit student
* Delete student
* View student list

### 🔄 Book Issue System

* Issue books to students
* Select book & student from dropdown
* Set return date
* Auto decrease available quantity

### 🔁 Book Return System

* Mark books as returned
* Auto increase available quantity
* Update status

### 📈 Reports

* Issued books list
* Returned books list
* Overdue books tracking

---

## 🛠️ Tech Stack

| Layer    | Technology             |
| -------- | ---------------------- |
| Frontend | HTML, CSS, Bootstrap 5 |
| Backend  | PHP (Core PHP)         |
| Database | MySQL                  |
| Server   | XAMPP                  |

---

## 📁 Project Structure

```
library-management/
│
├── admin/
├── includes/
├── assets/
│   └── css/
├── database/
│
└── README.md
```

---

## 🗄️ Database Structure

### Tables:

* **admins**
* **books**
* **students**
* **issued_books**

---

## ⚙️ Installation & Setup

### 1️⃣ Install XAMPP

Download and install XAMPP.

### 2️⃣ Move Project

Place project folder inside:

```
C:\xampp\htdocs\
```

### 3️⃣ Start Server

Open XAMPP and start:

* Apache
* MySQL

### 4️⃣ Import Database

* Open browser: `http://localhost/phpmyadmin`
* Create database: `library_db`
* Import file:

```
database/library.sql
```

### 5️⃣ Run Project

Open:

```
http://localhost/library-management/admin/login.php
```

---

## 🔑 Default Login

```
Email: admin@gmail.com
Password: admin123
```

---

## 📸 Screenshots

*(Add screenshots here for better presentation)*

* Login Page
* Dashboard
* Book Management
* Student Management
* Issue/Return System

---

## 📌 Future Improvements

* Password encryption using bcrypt
* Email notifications
* Fine calculation system
* Advanced search filters
* REST API integration
* Mobile app support

---

## 🤝 Contributing

Feel free to fork this repository and contribute.

---

## 📄 License

This project is for educational purposes.

---

## 👨‍💻 Author

**Saurav Kumar**

---

⭐ If you like this project, give it a star!
