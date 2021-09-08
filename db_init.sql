-- Create the database
CREATE DATABASE IF NOT EXISTS rad_scheduler_db;

USE rad_scheduler_db;
-- Create the tables ...

-- Contains users (doctors and radiologists)
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    category ENUM ('doctor', 'radiologist') NOT NULL
);

-- Contains the available exam types
CREATE TABLE IF NOT EXISTS exam (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    duration INT UNSIGNED NOT NULL CHECK (0 < duration)
);

CREATE TABLE IF NOT EXISTS patient (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    father_name VARCHAR(255),
    mother_name VARCHAR(255),
    ssn VARCHAR(255) UNIQUE,
    address VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS order_2 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    order_date DATE NOT NULL DEFAULT NOW() ,
    reason TEXT,
    recommended_date DATE,
    priority ENUM ('routine', 'urgent') NOT NULL,

    FOREIGN KEY (patient_id)
        REFERENCES patient (id)
);

CREATE TABLE IF NOT EXISTS order_exam (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    exam_id INT NOT NULL,
    radiologist_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,

    FOREIGN KEY (order_id)
        REFERENCES order_2(id),
    FOREIGN KEY (exam_id)
        REFERENCES exam(id),
    FOREIGN KEY (radiologist_id)
        REFERENCES user(id),
    
    UNIQUE schedule_index (order_id, exam_id, radiologist_id)
);


-- Insert the available examinations
INSERT INTO exam (name, duration)
VALUES
    ("Computed Tomography (CT)", 20),
    ("DEXA (Bone Density Scan)", 20),
    ("Magnetic Resonance Imaging (MRI)", 90),
    ("Positron Emission Tomography (PET)", 45),
    ("Ultrasound", 60),
    ("X-ray", 15);

-- Insert a doctor user and a radiologist user
INSERT INTO user (username, password, category)
VALUES
	('akis', '1234', 'doctor'),
    ('flora', '4321', 'radiologist'),
    ('christina', '5678', 'radiologist');