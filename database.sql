-- Create database
CREATE DATABASE IF NOT EXISTS quiz404;
USE quiz404;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Questions table
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    question TEXT NOT NULL,
    option1 VARCHAR(255) NOT NULL,
    option2 VARCHAR(255) NOT NULL,
    option3 VARCHAR(255) NOT NULL,
    option4 VARCHAR(255) NOT NULL,
    correct_answer VARCHAR(255) NOT NULL,
    difficulty ENUM('easy', 'medium', 'hard') NOT NULL
);

-- Scores table
CREATE TABLE IF NOT EXISTS scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    difficulty VARCHAR(10) NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    time_taken INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Leaderboard view
CREATE OR REPLACE VIEW leaderboard AS
SELECT 
    u.username,
    s.category,
    s.difficulty,
    s.score,
    s.total_questions,
    s.time_taken,
    s.created_at,
    RANK() OVER (PARTITION BY s.category, s.difficulty ORDER BY s.score DESC, s.time_taken ASC) as rank
FROM scores s
JOIN users u ON s.user_id = u.id; 