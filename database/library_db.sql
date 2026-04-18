-- Library Management System Database
-- Create database
CREATE DATABASE IF NOT EXISTS library_db;
USE library_db;

-- Table: admins
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: books
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    category VARCHAR(100) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    quantity INT NOT NULL DEFAULT 0,
    available_quantity INT NOT NULL DEFAULT 0,
    added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: students
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: issued_books
CREATE TABLE IF NOT EXISTS issued_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    student_id INT NOT NULL,
    issue_date DATE NOT NULL,
    return_date DATE NOT NULL,
    status ENUM('issued', 'returned') DEFAULT 'issued',
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
-- The password is hashed using MD5 for simplicity (in production use password_hash)
INSERT INTO admins (name, email, password) VALUES 
('Admin User', 'admin@library.com', MD5('admin123'));

-- Insert sample books
INSERT INTO books (title, author, category, isbn, quantity, available_quantity) VALUES 
('The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', '978-0743273565', 5, 5),
('To Kill a Mockingbird', 'Harper Lee', 'Fiction', '978-0061120084', 3, 3),
('1984', 'George Orwell', 'Science Fiction', '978-0451524935', 4, 4),
('Pride and Prejudice', 'Jane Austen', 'Romance', '978-0141439518', 2, 2),
('The Catcher in the Rye', 'J.D. Salinger', 'Fiction', '978-0316769488', 3, 3);

-- Insert sample students
INSERT INTO students (name, email, phone) VALUES 
('John Doe', 'john@example.com', '1234567890'),
('Jane Smith', 'jane@example.com', '0987654321'),
('Bob Johnson', 'bob@example.com', '5555555555');
