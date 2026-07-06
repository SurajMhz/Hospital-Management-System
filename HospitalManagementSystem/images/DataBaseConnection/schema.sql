-- CityMed Hospital Management System — database setup
-- Import this once in phpMyAdmin (or `mysql -u root < schema.sql`) before running the project.
-- Import this once in phpMyAdmin before running the project.

CREATE DATABASE IF NOT EXISTS user_system;
USE user_system;

-- Users table (doctors and patients)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  role VARCHAR(20) NOT NULL
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT NOT NULL,
  patient_name VARCHAR(100) NOT NULL,
  age INT,
  gender VARCHAR(10),
  phone VARCHAR(20),
  date DATE,
  department VARCHAR(100),
  reason VARCHAR(255),
  source VARCHAR(20) DEFAULT 'Manual',
  status VARCHAR(20) NOT NULL DEFAULT 'Scheduled',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (doctor_id) REFERENCES users(id)
);

-- Prescriptions table
CREATE TABLE IF NOT EXISTS prescriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT NOT NULL,
  appointment_id INT,
  patient_name VARCHAR(100) NOT NULL,
  diagnosis VARCHAR(255),
  medicine TEXT,
  dosage VARCHAR(50),
  duration VARCHAR(50),
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (doctor_id) REFERENCES users(id)
);

-- Doctor profile table
CREATE TABLE IF NOT EXISTS doctor_profile (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  specialization VARCHAR(100),
  FOREIGN KEY (user_id) REFERENCES users(id)
);