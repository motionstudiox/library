-- Library Management System Database Schema
-- Execute this SQL script to create the database

-- Create Database
CREATE DATABASE IF NOT EXISTS library_db;
USE library_db;

-- ============================================
-- Users Table
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- ============================================
-- Books Table
-- ============================================
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(200) NOT NULL,
    description LONGTEXT,
    publisher VARCHAR(150),
    publication_year INT,
    genre VARCHAR(100),
    total_copies INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_author (author),
    INDEX idx_genre (genre)
);

-- ============================================
-- Lending Table
-- ============================================
CREATE TABLE IF NOT EXISTS lending (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    borrow_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    due_date DATETIME NOT NULL,
    return_date DATETIME,
    is_returned BOOLEAN DEFAULT FALSE,
    fine_amount DECIMAL(10, 2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_book (book_id),
    INDEX idx_is_returned (is_returned)
);

-- ============================================
-- Reservation Table
-- ============================================
CREATE TABLE IF NOT EXISTS reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    reservation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    pickup_date DATETIME,
    is_active BOOLEAN DEFAULT TRUE,
    is_fulfilled BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_book (book_id),
    INDEX idx_active (is_active)
);

-- ============================================
-- Review Table
-- ============================================
CREATE TABLE IF NOT EXISTS review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    rating DECIMAL(2, 1) NOT NULL,
    comment LONGTEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_book (book_id),
    UNIQUE KEY unique_user_book_review (user_id, book_id)
);

-- ============================================
-- Sample Data
-- ============================================

-- Insert Sample Users
INSERT INTO users (username, email, password_hash, full_name, phone) VALUES
('john_doe', 'john@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMye4IfStlCSKrjNuUvfYn5K5vVnGaPz7jG', 'John Doe', '555-0001'),
('jane_smith', 'jane@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMye4IfStlCSKrjNuUvfYn5K5vVnGaPz7jG', 'Jane Smith', '555-0002'),
('bob_johnson', 'bob@example.com', '$2y$10$N9qo8uLOickgx2ZMRZoMye4IfStlCSKrjNuUvfYn5K5vVnGaPz7jG', 'Bob Johnson', '555-0003');

-- Insert Sample Books
INSERT INTO books (isbn, title, author, description, publisher, publication_year, genre, total_copies, available_copies) VALUES
('978-0-13-468599-1', 'Clean Code', 'Robert C. Martin', 'A guide to writing better code', 'Prentice Hall', 2008, 'Programming', 3, 2),
('978-0-13-235088-4', 'The Pragmatic Programmer', 'David Thomas', 'Tips and tricks for effective programming', 'Addison-Wesley', 1999, 'Programming', 2, 2),
('978-0-596-00712-6', 'Design Patterns', 'Gang of Four', 'Reusable solutions to common problems', 'Addison-Wesley', 1994, 'Programming', 2, 1),
('978-0-14-018870-8', 'To Kill a Mockingbird', 'Harper Lee', 'A classic American novel', 'J.B. Lippincott', 1960, 'Fiction', 4, 2),
('978-0-451-52493-2', '1984', 'George Orwell', 'A dystopian novel', 'Signet Classic', 1949, 'Fiction', 3, 1);

-- ============================================
-- Indexes for Performance
-- ============================================
CREATE INDEX idx_lending_user_book ON lending(user_id, book_id);
CREATE INDEX idx_lending_due_date ON lending(due_date);
CREATE INDEX idx_reservation_user_book ON reservation(user_id, book_id);
CREATE INDEX idx_review_rating ON review(rating);
