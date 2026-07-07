CREATE DATABASE IF NOT EXISTS user_system;
USE user_system;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  role VARCHAR(20) NOT NULL,
  age INT,
  gender VARCHAR(10)
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
  appointment_id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  doctor_id INT NOT NULL,
  doctor_name VARCHAR(100) NOT NULL,
  appointment_date DATE,
  appointment_time TIME,
  department VARCHAR(100),
  reason VARCHAR(255),
  source VARCHAR(20) DEFAULT 'Manual',
  status VARCHAR(20) DEFAULT 'Scheduled',
  phone VARCHAR(20),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (patient_id) REFERENCES users(id),
  FOREIGN KEY (doctor_id) REFERENCES users(id)
);

-- Prescriptions table
CREATE TABLE IF NOT EXISTS prescriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT NOT NULL,
  appointment_id INT,
  diagnosis VARCHAR(255),
  medicine TEXT,
  dosage VARCHAR(50),
  duration VARCHAR(50),
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  FOREIGN KEY (doctor_id) REFERENCES users(id),
  FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id)
);

-- Doctor profile table
CREATE TABLE IF NOT EXISTS doctor_profile (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  specialization VARCHAR(100),

  FOREIGN KEY (user_id) REFERENCES users(id)
);