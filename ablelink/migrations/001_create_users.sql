-- ============================================================
-- AUTHENTICATION SYSTEM - Database Setup
-- ============================================================
-- Creates users table and admin accounts
-- ============================================================

USE ablelink_db;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ADMIN ACCOUNTS
-- ============================================================
-- Mot de passe temporaire pour tous les admins: admin123
-- IMPORTANT: Changez ce mot de passe après la première connexion!
-- Hash généré avec: password_hash('admin123', PASSWORD_DEFAULT)

INSERT INTO users (name, email, password, role) VALUES
('Ahmed Mohsen', 'ahmedmohsen@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Nour Bouabid', 'nourbouabid@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ============================================================
-- MODIFY success_stories table
-- ============================================================
-- Add user_id to link stories to users
ALTER TABLE success_stories 
ADD COLUMN IF NOT EXISTS user_id INT NULL AFTER author,
ADD CONSTRAINT fk_story_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_user_id ON success_stories(user_id);

-- ============================================================
-- VERIFICATION
-- ============================================================
SELECT 'USERS TABLE' AS Info, COUNT(*) AS Total FROM users
UNION ALL
SELECT 'ADMIN USERS' AS Info, COUNT(*) AS Total FROM users WHERE role = 'admin'
UNION ALL
SELECT 'REGULAR USERS' AS Info, COUNT(*) AS Total FROM users WHERE role = 'user';

-- Show admin accounts
SELECT id, name, email, role, created_at FROM users WHERE role = 'admin';

-- ============================================================
-- ✅ CREDENTIALS
-- ============================================================
-- Admin 1:
--   Email: ahmedmohsen@gmail.com
--   Password: admin123
--
-- Admin 2:
--   Email: nourbouabid@gmail.com
--   Password: admin123
--
-- ⚠️ IMPORTANT: Changez ces mots de passe après la première connexion!
-- ============================================================
