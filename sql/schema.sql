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

CREATE TABLE IF NOT EXISTS `notifications` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT NOT NULL,
    `type`       VARCHAR(50) NOT NULL,
    `title`      VARCHAR(255) NOT NULL,
    `message`    TEXT NOT NULL,
    `link`       VARCHAR(500),
    `is_read`    TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `attachments` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `ref_type`    ENUM('blotter','financial') NOT NULL,
    `ref_id`      INT NOT NULL,
    `file_name`   VARCHAR(255) NOT NULL,
    `file_path`   VARCHAR(500) NOT NULL,
    `file_size`   INT NOT NULL DEFAULT 0,
    `uploaded_by` INT NOT NULL,
    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX (`ref_type`, `ref_id`)
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

INSERT INTO `announcements` (`title`,`content`,`image_url`,`posted_by`,`status`) VALUES
('Fire Prevention Month 2026 – Training Activity','Barangay Buck Estate joins the celebration of Fire Prevention Month 2026. A training activity on the proper methods of fire suppression and appropriate response in case of emergencies, particularly residential fires within the barangay, was conducted under the Committee on Rescue and Disaster with the direct supervision of our Punong Barangay. Our heartfelt gratitude is extended to the personnel of the Bureau of Fire Protection – Alfonso Fire Station for their invaluable assistance and unwavering commitment to public safety. Let us all work together in promoting and practicing fire safety as we observe the month of March as Fire Prevention Month.','/assets/img/announcements/fire-prevention-2026.jpg',1,'published'),
('Buck Estate Fire Auxiliary Group Wins at 3rd Barangay Fire Olympics','Congratulations to the Buck Estate Fire Auxiliary Group! Most Organized Barangay 2026, Champion in Bucket Relay Category, and 1st Placer at the 3rd Barangay Fire Olympics. Throughout the entire Olympics, the team demonstrated exceptional skill, agility, teamwork, and cooperation. Their outstanding performance and unity truly brought pride and honor to our community. Their dedication and commitment serve as an inspiration to everyone.','/assets/img/announcements/fire-olympics-2026.jpg',1,'published'),
('Aran Nuestro – Back-to-Back Champion U23 BMX Racing','Congratulations to Aran Nuestro for being the Back-to-Back Champion in the U23 Category at the 2024 Philippine National Championships BMX Racing! The entire Barangay Buck Estate is truly proud of this achievement. Keep making our barangay proud!','/assets/img/announcements/bmx-champion-2024.jpg',1,'published'),
('Mobile Registration of Local Civil Registrar','The Local Civil Registrar will conduct a Mobile Registration at the Barangay Hall on March 11, 2026, starting at 9:00 AM. For inquiries, contact 0917-125-9237 or visit the official Brgy. Buck Estate Facebook page.','/assets/img/announcements/mobile-registration-2026.jpg',1,'published'),
('Nutrition Month Celebration 2024','The Barangay Council for the Protection of Children and Barangay Nutrition Council (BCPC and BNC) held a Nutrition Month Celebration on July 26, 2024 at the Covered Court, Buck Estate, Alfonso, Cavite. The event promoted the Philippine Plan of Action for Nutrition (PPAN) 2023-2028 under the theme \"Sa PPAN, Sama-sama sa Nutrisyong Sapat Para sa Lahat!\"','/assets/img/announcements/nutrition-month-2024.jpg',1,'published');

INSERT INTO `officials` (`name`,`position`,`photo_url`,`order_num`) VALUES
-- Barangay Officials
('Aimy Casabuena','Punong Barangay','/assets/img/officials/aimy-casabuena.jpg',1),
('Diana Igaya','Barangay Secretary','/assets/img/officials/diana-igaya.jpg',2),
('Maricel Salandanan','Barangay Treasurer','/assets/img/officials/maricel-salandanan.jpg',3),
('Antonio Bersamina','Kagawad | Committee Chairman on Agriculture and Environment Protection','/assets/img/officials/antonio-bersamina.jpg',4),
('Joselito Caimoy','Kagawad | Committee Chairman on Ways and Means','/assets/img/officials/joselito-caimoy.jpg',5),
('Jessa Mae Constante','Kagawad | Committee Chairwoman on Women & Family Welfare & Education','/assets/img/officials/jessa-mae-constante.jpg',6),
('Jesus Teano','Kagawad | Committee Chairman on Peace and Order, Anti-Drugs, Public Safety and Human Rights','/assets/img/officials/jesus-teano.jpg',7),
('Danilo Bersamina','Kagawad | Committee on Cooperative Development, Livelihood, and Employment','/assets/img/officials/danilo-bersamina.jpg',8),
('Larry Catapang','Kagawad | Committee Chairman on Appropriation and Public Works and Infrastructure','/assets/img/officials/larry-catapang.jpg',9),
('Michael Villalobos','Kagawad | Committee Chairman on Health and BDRRMC','/assets/img/officials/michael-villalobos.jpg',10),
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
