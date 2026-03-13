-- ============================================================
-- BUCK ESTATE BARANGAY PORTAL - MYSQL SCHEMA
-- Run this in InfinityFree phpMyAdmin
-- ============================================================

SET NAMES utf8mb4;
SET time_zone = '+08:00';

CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `fname`      VARCHAR(100) NOT NULL,
    `lname`      VARCHAR(100) NOT NULL,
    `email`      VARCHAR(150) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `address`    TEXT NOT NULL,
    `contact`    VARCHAR(20) NOT NULL,
    `role`       ENUM('resident','admin') NOT NULL DEFAULT 'resident',
    `status`     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `announcements` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `title`      VARCHAR(255) NOT NULL,
    `content`    TEXT NOT NULL,
    `image_url`  VARCHAR(500),
    `posted_by`  INT,
    `status`     ENUM('published','draft','archived') DEFAULT 'published',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`posted_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `document_requests` (
    `id`            INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`       INT NOT NULL,
    `doc_type`      ENUM('Barangay Clearance','Certificate of Residency','Certificate of Indigency') NOT NULL,
    `purpose`       TEXT NOT NULL,
    `status`        ENUM('Pending','Processing','Approved','Denied') DEFAULT 'Pending',
    `admin_remarks` TEXT,
    `processed_by`  INT,
    `requested_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `processed_at`  TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`processed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `blotter_reports` (
    `id`                INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`           INT NOT NULL,
    `incident_type`     VARCHAR(100) NOT NULL,
    `complainant_name`  VARCHAR(200) NOT NULL,
    `respondent_name`   VARCHAR(200) NOT NULL,
    `incident_date`     DATE NOT NULL,
    `incident_location` VARCHAR(255) NOT NULL,
    `description`       TEXT NOT NULL,
    `case_number`       VARCHAR(50),
    `status`            ENUM('Filed','Under Review','Resolved','Dismissed') DEFAULT 'Filed',
    `admin_remarks`     TEXT,
    `processed_by`      INT,
    `filed_at`          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`processed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `financial_requests` (
    `id`                 INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`            INT NOT NULL,
    `assistance_type`    ENUM('Medical','Burial','Calamity','Others') NOT NULL,
    `description`        TEXT NOT NULL,
    `amount_requested`   DECIMAL(10,2) NOT NULL,
    `supporting_details` TEXT,
    `status`             ENUM('Pending','Approved','Denied') DEFAULT 'Pending',
    `admin_remarks`      TEXT,
    `processed_by`       INT,
    `filed_at`           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `processed_at`       TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`processed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `officials` (
    `id`        INT AUTO_INCREMENT PRIMARY KEY,
    `name`      VARCHAR(200) NOT NULL,
    `position`  VARCHAR(100) NOT NULL,
    `photo_url` VARCHAR(500),
    `order_num` INT DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT IGNORE INTO `users` (`fname`,`lname`,`email`,`password`,`address`,`contact`,`role`) VALUES
('Barangay','Admin','admin@buckestate.gov.ph','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Barangay Hall, Buck Estate, Alfonso, Cavite','09XX-XXX-XXXX','admin');

INSERT INTO `announcements` (`title`,`content`,`posted_by`,`status`) VALUES
('Barangay Assembly Meeting','All residents of Barangay Buck Estate are cordially invited to attend the quarterly Barangay Assembly Meeting. Attendance is highly encouraged. Please bring a valid ID.',1,'published'),
('Barangay Clearance Processing Update','Barangay Clearance requests submitted through this portal will be processed within 1-3 business days. Residents will be notified once their document is ready for pickup at the Barangay Hall.',1,'published'),
('Free Medical Mission','A free medical mission will be conducted at the Barangay Hall. Bring a valid ID. No appointment needed. Services include free consultation, medicine, and laboratory tests for qualified residents.',1,'published');

INSERT INTO `officials` (`name`,`position`,`order_num`) VALUES
-- Barangay Officials
('Aimy Casabuena','Punong Barangay',1),
('Diana Igaya','Barangay Secretary',2),
('Maricel Salandanan','Barangay Treasurer',3),
('Antonio Bersamina','Kagawad | Committee Chairman on Agriculture and Environment Protection',4),
('Joselito Caimoy','Kagawad | Committee Chairman on Ways and Means',5),
('Jessa Mae Constante','Kagawad | Committee Chairwoman on Women & Family Welfare & Education',6),
('Jesus Teano','Kagawad | Committee Chairman on Peace and Order, Anti-Drugs, Public Safety and Human Rights',7),
('Danilo Bersamina','Kagawad | Committee on Cooperative Development, Livelihood, and Employment',8),
('Larry Catapang','Kagawad | Committee Chairman on Appropriation and Public Works and Infrastructure',9),
('Michael Villalobos','Kagawad | Committee Chairman on Health and BDRRMC',10),
-- SK Officials
('Mickaellah P. Constante','SK Chairperson | Committee Chairwoman on Youth & Sports Development and Tourism',11),
('Lylane Marie D. Caimoy','SK Secretary',12),
('Loyd Gabrielle C. Leonor','SK Treasurer',13),
('John Carlo Alcaraz','SK Member',14),
('Judy Ann L. Bislig','SK Member',15),
('Ma. Lourdes R. Noveno','SK Member',16),
('Julianne Patrice D. Tabor','SK Member',17),
('Ronalyn C. Nuestro','SK Member',18),
('Zedrick G. Bautista','SK Member',19),
('Ken Cedrix N. Zaldivar','SK Member',20);
