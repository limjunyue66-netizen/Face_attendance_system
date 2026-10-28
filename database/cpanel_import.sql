-- ==============================================================================
-- Facial Recognition Student Attendance Management System
-- Dedicated cPanel / Remote MySQL phpMyAdmin Import Script
--
-- IMPORTANT CPANEL IMPORT INSTRUCTIONS:
-- 1. In your cPanel, create a new MySQL Database (e.g. youruser_attendance)
-- 2. Create a MySQL User and assign ALL PRIVILEGES to that database
-- 3. Open phpMyAdmin in cPanel, SELECT your newly created database on the left
-- 4. Click the "Import" tab at the top, choose this file, and click "Import"
--
-- NOTE: This script does NOT contain CREATE DATABASE or USE statements,
-- which prevents "#1044 - Access denied" permission errors in shared cPanel.
-- ==============================================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. Drop existing tables safely in reverse dependency order
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `student_leave_applications`;
DROP TABLE IF EXISTS `rest_attendance`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `face_descriptors`;
DROP TABLE IF EXISTS `face_registrations`;
DROP TABLE IF EXISTS `teacher_assignments`;
DROP TABLE IF EXISTS `class_subjects`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `teachers`;
DROP TABLE IF EXISTS `subjects`;
DROP TABLE IF EXISTS `classes`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `users`;

-- ------------------------------------------------------------------------------
-- 2. Table: users
-- ------------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_role` (`role`),
  INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Table: classes
-- ------------------------------------------------------------------------------
CREATE TABLE `classes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `class_code` VARCHAR(50) NOT NULL UNIQUE,
  `class_name` VARCHAR(100) NOT NULL,
  `start_time` TIME NOT NULL DEFAULT '08:00:00',
  `late_grace_minutes` INT NOT NULL DEFAULT 15,
  `rest_start_time` TIME NOT NULL DEFAULT '12:00:00',
  `rest_end_time` TIME NOT NULL DEFAULT '13:00:00',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. Table: subjects
-- ------------------------------------------------------------------------------
CREATE TABLE `subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_code` VARCHAR(50) NOT NULL UNIQUE,
  `subject_name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. Table: teachers
-- ------------------------------------------------------------------------------
CREATE TABLE `teachers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `teacher_code` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `department` VARCHAR(100) NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_teacher_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_teachers_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 6. Table: students
-- ------------------------------------------------------------------------------
CREATE TABLE `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `class_id` INT NULL,
  `student_code` VARCHAR(50) NOT NULL UNIQUE,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NULL,
  `gender` ENUM('male', 'female', 'other') NULL,
  `date_of_birth` DATE NULL,
  `address` TEXT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `face_registered` TINYINT(1) NOT NULL DEFAULT 0,
  `registration_version` INT NOT NULL DEFAULT 0,
  `preview_photo` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_student_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_student_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_students_class` (`class_id`),
  INDEX `idx_students_status` (`status`),
  INDEX `idx_students_face` (`face_registered`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 7. Table: class_subjects (pivot)
-- ------------------------------------------------------------------------------
CREATE TABLE `class_subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `class_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_class_subject` (`class_id`, `subject_id`),
  CONSTRAINT `fk_cs_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cs_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 8. Table: teacher_assignments
-- ------------------------------------------------------------------------------
CREATE TABLE `teacher_assignments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `teacher_id` INT NOT NULL,
  `class_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_assignment` (`teacher_id`, `class_id`, `subject_id`),
  CONSTRAINT `fk_ta_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ta_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ta_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 9. Table: face_registrations
-- ------------------------------------------------------------------------------
CREATE TABLE `face_registrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `version` INT NOT NULL DEFAULT 1,
  `sample_count` INT NOT NULL DEFAULT 3,
  `preview_image` VARCHAR(255) NULL,
  `registered_by_user_id` INT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_fr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_fr_user` FOREIGN KEY (`registered_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_fr_student_active` (`student_id`, `is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 10. Table: face_descriptors (individual sample vectors)
-- ------------------------------------------------------------------------------
CREATE TABLE `face_descriptors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `registration_id` INT NOT NULL,
  `sample_index` INT NOT NULL DEFAULT 1,
  `descriptor` LONGTEXT NOT NULL,
  `brightness` FLOAT NULL,
  `sharpness` FLOAT NULL,
  `face_size` FLOAT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_fd_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_fd_registration` FOREIGN KEY (`registration_id`) REFERENCES `face_registrations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_fd_student` (`student_id`),
  INDEX `idx_fd_registration` (`registration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 11. Table: attendance
-- ------------------------------------------------------------------------------
CREATE TABLE `attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `class_id` INT NULL,
  `attendance_date` DATE NOT NULL,
  `check_in_time` TIME NULL,
  `check_in_photo` VARCHAR(255) NULL,
  `check_in_confidence` FLOAT NULL,
  `check_out_time` TIME NULL,
  `check_out_photo` VARCHAR(255) NULL,
  `check_out_confidence` FLOAT NULL,
  `status` ENUM('checked_in', 'checked_out', 'late', 'absent', 'on_leave') NOT NULL DEFAULT 'checked_in',
  `remarks` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_attendance_student_date` (`student_id`, `attendance_date`),
  CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_att_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_att_date` (`attendance_date`),
  INDEX `idx_att_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 12. Table: rest_attendance (breaks / lunch)
-- ------------------------------------------------------------------------------
CREATE TABLE `rest_attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `class_id` INT NULL,
  `attendance_date` DATE NOT NULL,
  `rest_check_in_time` TIME NULL,
  `rest_check_in_photo` VARCHAR(255) NULL,
  `rest_check_in_confidence` FLOAT NULL,
  `rest_check_out_time` TIME NULL,
  `rest_check_out_photo` VARCHAR(255) NULL,
  `rest_check_out_confidence` FLOAT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_rest_att_student_date` (`student_id`, `attendance_date`),
  CONSTRAINT `fk_rest_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rest_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 13. Table: student_leave_applications
-- ------------------------------------------------------------------------------
CREATE TABLE `student_leave_applications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `leave_date` DATE NOT NULL,
  `reason` TEXT NOT NULL,
  `document_path` VARCHAR(255) NULL,
  `original_filename` VARCHAR(255) NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by_user_id` INT NULL,
  `review_note` TEXT NULL,
  `reviewed_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_leave_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_leave_reviewer` FOREIGN KEY (`reviewed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_leave_date` (`leave_date`),
  INDEX `idx_leave_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 14. Table: audit_logs
-- ------------------------------------------------------------------------------
CREATE TABLE `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `student_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `ip_address` VARCHAR(50) NULL,
  `user_agent` VARCHAR(255) NULL,
  `confidence_score` FLOAT NULL,
  `registration_version` INT NULL,
  `metadata` LONGTEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_audit_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_audit_action` (`action`),
  INDEX `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 15. Table: settings
-- ------------------------------------------------------------------------------
CREATE TABLE `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NULL,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- Seed Default System Settings
-- Note: min_sharpness is calibrated to '3' for optimal real-world camera detection
-- ------------------------------------------------------------------------------
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('min_face_size', '80', 'Minimum face bounding box size in pixels'),
('max_face_size', '450', 'Maximum face bounding box size in pixels'),
('min_brightness', '40', 'Minimum average frame brightness (0-255)'),
('max_brightness', '220', 'Maximum average frame brightness (0-255)'),
('min_sharpness', '3', 'Minimum image sharpness / contrast threshold (Laplacian gradient)'),
('detection_confidence', '0.50', 'TinyFaceDetector minimum detection score'),
('face_match_threshold', '0.55', 'Face recognition distance threshold (lower is stricter)'),
('min_attendance_confidence', '0.45', 'Minimum match confidence percentage to record attendance'),
('stable_frame_count', '2', 'Consecutive stable frames required before capture'),
('duplicate_scan_interval', '300', 'Cooldown period in seconds to prevent duplicate scans (5 min)'),
('registration_samples', '3', 'Number of facial descriptor samples required during registration'),
('kiosk_title', 'EduVision Facial Attendance Kiosk', 'Display title on Attendance Kiosk header'),
('kiosk_school_name', 'Metropolitan Institute of Technology', 'School name on kiosk & reports');

-- ------------------------------------------------------------------------------
-- Seed Default User Accounts
-- admin    -> admin123
-- teacher1 -> teacher123
-- teacher2 -> teacher123
-- student1 -> student123
-- student2 -> student123
-- student3 -> student123
-- junyue   -> student123
-- ------------------------------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`) VALUES
(1, 'admin', 'admin@school.edu', '$2y$10$nSgdyHANtEjrmWtn0abdYejcQ5ByrvvQRvNndeZLTTNR8sCoA2Vjy', 'admin', 'active'),
(2, 'teacher1', 'alice.teacher@school.edu', '$2y$10$371lUmEumo7CEtPBP2dWpe85hwI1JZd2eOBiV0d3EMmsBBpF6qFsi', 'teacher', 'active'),
(3, 'teacher2', 'bob.smith@school.edu', '$2y$10$371lUmEumo7CEtPBP2dWpe85hwI1JZd2eOBiV0d3EMmsBBpF6qFsi', 'teacher', 'active'),
(4, 'student1', 'john.doe@student.school.edu', '$2y$10$G730NO.PqA1d6t1r05VyB./xl.HYxWy4RaMqDB5BfuU1k.ExosLOO', 'student', 'active'),
(5, 'student2', 'jane.roe@student.school.edu', '$2y$10$G730NO.PqA1d6t1r05VyB./xl.HYxWy4RaMqDB5BfuU1k.ExosLOO', 'student', 'active'),
(6, 'student3', 'michael.brown@student.school.edu', '$2y$10$G730NO.PqA1d6t1r05VyB./xl.HYxWy4RaMqDB5BfuU1k.ExosLOO', 'student', 'active'),
(7, 'junyue', 'junyue@student.school.edu', '$2y$10$G730NO.PqA1d6t1r05VyB./xl.HYxWy4RaMqDB5BfuU1k.ExosLOO', 'student', 'active');

-- ------------------------------------------------------------------------------
-- Seed Default Classes
-- ------------------------------------------------------------------------------
INSERT INTO `classes` (`id`, `class_code`, `class_name`, `start_time`, `late_grace_minutes`, `rest_start_time`, `rest_end_time`, `status`) VALUES
(1, 'CS-Y1A', 'Computer Science Year 1 A', '08:00:00', 15, '12:00:00', '13:00:00', 'active'),
(2, 'CS-Y1B', 'Computer Science Year 1 B', '08:30:00', 10, '12:30:00', '13:30:00', 'active'),
(3, 'SE-Y2A', 'Software Engineering Year 2 A', '09:00:00', 15, '12:00:00', '13:00:00', 'active');

-- ------------------------------------------------------------------------------
-- Seed Default Subjects
-- ------------------------------------------------------------------------------
INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `description`, `status`) VALUES
(1, 'CS101', 'Introduction to Programming', 'Foundations of algorithms, procedural programming and problem solving', 'active'),
(2, 'CS102', 'Database Systems', 'Relational database design, SQL queries and transaction management', 'active'),
(3, 'CS103', 'Web Development Technologies', 'Client-side and server-side modern web application engineering', 'active'),
(4, 'CS201', 'Data Structures & Algorithms', 'Stacks, queues, trees, graphs, sorting and search complexities', 'active');

-- ------------------------------------------------------------------------------
-- Seed Teachers
-- ------------------------------------------------------------------------------
INSERT INTO `teachers` (`id`, `user_id`, `teacher_code`, `full_name`, `email`, `phone`, `department`, `status`) VALUES
(1, 2, 'TCH-001', 'Alice Teacher', 'alice.teacher@school.edu', '+1 555-0192', 'Computer Science', 'active'),
(2, 3, 'TCH-002', 'Dr. Bob Smith', 'bob.smith@school.edu', '+1 555-0193', 'Information Systems', 'active');

-- ------------------------------------------------------------------------------
-- Seed Students
-- ------------------------------------------------------------------------------
INSERT INTO `students` (`id`, `user_id`, `class_id`, `student_code`, `full_name`, `email`, `phone`, `gender`, `date_of_birth`, `status`, `face_registered`, `registration_version`) VALUES
(1, 4, 1, 'STU-2026-001', 'John Doe', 'john.doe@student.school.edu', '+1 555-0101', 'male', '2005-04-12', 'active', 0, 0),
(2, 5, 1, 'STU-2026-002', 'Jane Roe', 'jane.roe@student.school.edu', '+1 555-0102', 'female', '2005-08-23', 'active', 0, 0),
(3, 6, 2, 'STU-2026-003', 'Michael Brown', 'michael.brown@student.school.edu', '+1 555-0103', 'male', '2004-11-15', 'active', 0, 0),
(4, 7, 1, 'STU-2026-004', 'Jun Yue', 'junyue@student.school.edu', '+1 555-0104', 'male', '2004-06-18', 'active', 0, 0);

-- ------------------------------------------------------------------------------
-- Seed Class-Subject Mappings
-- ------------------------------------------------------------------------------
INSERT INTO `class_subjects` (`class_id`, `subject_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 1),
(2, 2),
(3, 3),
(3, 4);

-- ------------------------------------------------------------------------------
-- Seed Teacher Assignments
-- ------------------------------------------------------------------------------
INSERT INTO `teacher_assignments` (`teacher_id`, `class_id`, `subject_id`) VALUES
(1, 1, 1), -- Alice -> CS-Y1A -> Intro to Programming
(1, 1, 3), -- Alice -> CS-Y1A -> Web Development
(1, 2, 1), -- Alice -> CS-Y1B -> Intro to Programming
(2, 1, 2), -- Bob -> CS-Y1A -> Database Systems
(2, 3, 4); -- Bob -> SE-Y2A -> Data Structures

-- ------------------------------------------------------------------------------
-- Seed Sample Leave Application
-- ------------------------------------------------------------------------------
INSERT INTO `student_leave_applications` (`student_id`, `leave_date`, `reason`, `status`, `created_at`) VALUES
(2, DATE_ADD(CURRENT_DATE(), INTERVAL 2 DAY), 'Medical appointment scheduled with hospital specialist.', 'pending', CURRENT_TIMESTAMP);

-- ------------------------------------------------------------------------------
-- Seed Installation Audit Trail
-- ------------------------------------------------------------------------------
INSERT INTO `audit_logs` (`user_id`, `action`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES
(1, 'CPANEL_DATABASE_SETUP', '127.0.0.1', 'cPanel phpMyAdmin Import', '{"event":"Database successfully installed on production server"}', CURRENT_TIMESTAMP);

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- End of cPanel Import Script
-- ==============================================================================
