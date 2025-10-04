CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `grade_levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grade_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_name` varchar(255) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `grade_level_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `grade_level_id` (`grade_level_id`),
  CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sections_ibfk_2` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `roll_number` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `grade_level_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roll_number` (`roll_number`),
  KEY `grade_level_id` (`grade_level_id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON DELETE SET NULL,
  CONSTRAINT `students_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `parents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT 'default.png',
  `phone_number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `student_parent` (
  `student_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`student_id`,`parent_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `student_parent_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_parent_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('admin','teacher') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','late') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `teacher_id` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `grade_level_id` (`grade_level_id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assignments_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `quizzes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quiz_date` date DEFAULT NULL,
  `teacher_id` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `grade_level_id` (`grade_level_id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quizzes_ibfk_2` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quizzes_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `grades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `quarter` int(11) NOT NULL,
  `grade` decimal(5,2) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `teacher_id` (`teacher_id`),
  CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;