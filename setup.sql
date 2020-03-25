/*
 Navicat MySQL Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 100137
 Source Host           : localhost:3306
 Source Schema         : db2

 Target Server Type    : MySQL
 Target Server Version : 100137
 File Encoding         : 65001

 Date: 2/12/2020 22:55:30
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


DROP TABLE IF EXISTS `enroll`;
DROP TABLE IF EXISTS `enroll2`;
DROP TABLE IF EXISTS `assign`;
DROP TABLE IF EXISTS `mentors`;
DROP TABLE IF EXISTS `mentees`;
DROP TABLE IF EXISTS `material`;
DROP TABLE IF EXISTS `meetings`;
DROP TABLE IF EXISTS `time_slot`;
DROP TABLE IF EXISTS `groups`;
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `parents`;
DROP TABLE IF EXISTS `users`;

-- ----------------------------
-- Table structure for users
-- ----------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for parents
-- ----------------------------

CREATE TABLE `parents` (
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`parent_id`),
  CONSTRAINT `parent_user` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for students
-- ----------------------------

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `grade` int(11) DEFAULT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`student_id`),
  KEY `student_parent` (`parent_id`),
  CONSTRAINT `student_user` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`parent_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for admins
-- ----------------------------

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  PRIMARY KEY (`admin_id`),
  CONSTRAINT `admins_user` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for groups
-- ----------------------------

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` int(11) DEFAULT NULL,
  `mentor_grade_req` int(11) DEFAULT NULL,
  `mentee_grade_req` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------
-- Table structure for time_slot
-- ----------------------------

CREATE TABLE `time_slot` (
  `time_slot_id` int(11) NOT NULL AUTO_INCREMENT,
  `day_of_the_week` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`time_slot_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for meetings
-- ----------------------------

CREATE TABLE `meetings` (
  `meet_id` int(11) NOT NULL AUTO_INCREMENT,
  `meet_name` varchar(255) NOT NULL,
  `date` date DEFAULT NULL,
  `time_slot_id` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `announcement` varchar(255) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`meet_id`),
  KEY `meeting_group` (`group_id`),
  KEY `meeting_time_slot` (`time_slot_id`),
  CONSTRAINT `meeting_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  CONSTRAINT `meeting_time_slot` FOREIGN KEY (`time_slot_id`) REFERENCES `time_slot` (`time_slot_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for material
-- ----------------------------

CREATE TABLE `material` (
  `material_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `assigned_date` date NOT NULL,
  `notes` text,
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for mentees
-- ----------------------------

CREATE TABLE `mentees` (
  `mentee_id` int(11) NOT NULL,
  PRIMARY KEY (`mentee_id`),
  CONSTRAINT `mentee_student` FOREIGN KEY (`mentee_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for mentors
-- ----------------------------

CREATE TABLE `mentors` (
  `mentor_id` int(11) NOT NULL,
  PRIMARY KEY (`mentor_id`),
  CONSTRAINT `mentor_student` FOREIGN KEY (`mentor_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for enroll
-- ----------------------------

CREATE TABLE `enroll` (
  `meet_id` int(11) NOT NULL,
  `mentee_id` int(11) NOT NULL,
  PRIMARY KEY (`meet_id`,`mentee_id`),
  KEY `enroll_mentee` (`mentee_id`),
  CONSTRAINT `enroll_mentee` FOREIGN KEY (`mentee_id`) REFERENCES `mentees` (`mentee_id`) ON DELETE CASCADE,
  CONSTRAINT `enroll_meetings` FOREIGN KEY (`meet_id`) REFERENCES `meetings` (`meet_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for enroll2
-- ----------------------------

CREATE TABLE `enroll2` (
  `meet_id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  PRIMARY KEY (`meet_id`,`mentor_id`),
  KEY `enroll2_mentor` (`mentor_id`),
  CONSTRAINT `enroll2_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `mentors` (`mentor_id`) ON DELETE CASCADE,
  CONSTRAINT `enroll2_meetings` FOREIGN KEY (`meet_id`) REFERENCES `meetings` (`meet_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for assign
-- ----------------------------

CREATE TABLE `assign` (
  `meet_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  PRIMARY KEY (`meet_id`,`material_id`),
  KEY `assign_material` (`material_id`),
  KEY `assign_meetings` (`meet_id`),
  CONSTRAINT `assign_material` FOREIGN KEY (`material_id`) REFERENCES `material` (`material_id`) ON DELETE CASCADE,
  CONSTRAINT `assign_meetings` FOREIGN KEY (`meet_id`) REFERENCES `meetings` (`meet_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO users (id, email, password, name, phone) VALUES (1, 'admin@gmail.com', 'db2pass', 'admin admin', '999-999-9999');
INSERT INTO admins (admin_id) VALUES (1);

INSERT INTO users (id, email, password, name, phone) VALUES (2, 'parent1@gmail.com', 'db2pass', 'parent 1', '999-999-9999');
INSERT INTO users (id, email, password, name, phone) VALUES (3, 'parent2@gmail.com', 'db2pass', 'parent 2', '999-999-9998');
INSERT INTO users (id, email, password, name, phone) VALUES (4, 'parent3@gmail.com', 'db2pass', 'parent 3', '999-999-9997');
INSERT INTO users (id, email, password, name, phone) VALUES (5, 'parent4@gmail.com', 'db2pass', 'parent 4', '999-999-9999');
INSERT INTO users (id, email, password, name, phone) VALUES (6, 'parent5@gmail.com', 'db2pass', 'parent 5', '999-999-9998');
INSERT INTO users (id, email, password, name, phone) VALUES (7, 'parent6@gmail.com', 'db2pass', 'parent 6', '999-999-9997');
INSERT INTO users (id, email, password, name, phone) VALUES (8, 'parent7@gmail.com', 'db2pass', 'parent 7', '999-999-9999');
INSERT INTO users (id, email, password, name, phone) VALUES (9, 'parent8@gmail.com', 'db2pass', 'parent 8', '999-999-9998');
INSERT INTO users (id, email, password, name, phone) VALUES (10, 'parent9@gmail.com', 'db2pass', 'parent 9', '999-999-9997');
INSERT INTO users (id, email, password, name, phone) VALUES (11, 'parent10@gmail.com', 'db2pass', 'parent 10', '999-999-9997');

INSERT INTO parents (parent_id) VALUES (2);
INSERT INTO parents (parent_id) VALUES (3);
INSERT INTO parents (parent_id) VALUES (4);
INSERT INTO parents (parent_id) VALUES (5);
INSERT INTO parents (parent_id) VALUES (6);
INSERT INTO parents (parent_id) VALUES (7);
INSERT INTO parents (parent_id) VALUES (8);
INSERT INTO parents (parent_id) VALUES (9);
INSERT INTO parents (parent_id) VALUES (10);
INSERT INTO parents (parent_id) VALUES (11);

INSERT INTO users (id, email, password, name, phone) VALUES (12, 'student1@gmail.com', 'db2pass', 'student 1', '999-999-9999');
INSERT INTO users (id, email, password, name, phone) VALUES (13, 'student2@gmail.com', 'db2pass', 'student 2', '999-999-9998');
INSERT INTO users (id, email, password, name, phone) VALUES (14, 'student3@gmail.com', 'db2pass', 'student 3', '999-999-9997');
INSERT INTO users (id, email, password, name, phone) VALUES (15, 'student4@gmail.com', 'db2pass', 'student 4', '999-999-9999');
INSERT INTO users (id, email, password, name, phone) VALUES (16, 'student5@gmail.com', 'db2pass', 'student 5', '999-999-9998');
INSERT INTO users (id, email, password, name, phone) VALUES (17, 'student6@gmail.com', 'db2pass', 'student 6', '999-999-9997');
INSERT INTO users (id, email, password, name, phone) VALUES (18, 'student7@gmail.com', 'db2pass', 'student 7', '999-999-9999');
INSERT INTO users (id, email, password, name, phone) VALUES (19, 'student8@gmail.com', 'db2pass', 'student 8', '999-999-9998');
INSERT INTO users (id, email, password, name, phone) VALUES (20, 'student9@gmail.com', 'db2pass', 'student 9', '999-999-9997');
INSERT INTO users (id, email, password, name, phone) VALUES (21, 'student10@gmail.com', 'db2pass', 'student 10', '999-999-9997');
INSERT INTO users (id, email, password, name, phone) VALUES (22, 'student11@gmail.com', 'db2pass', 'student 11', '999-999-9999');
INSERT INTO users (id, email, password, name, phone) VALUES (23, 'student12@gmail.com', 'db2pass', 'student 12', '999-999-9998');
INSERT INTO users (id, email, password, name, phone) VALUES (24, 'student13@gmail.com', 'db2pass', 'student 13', '999-999-9997');
INSERT INTO users (id, email, password, name, phone) VALUES (25, 'student14@gmail.com', 'db2pass', 'student 14', '999-999-9999');
INSERT INTO users (id, email, password, name, phone) VALUES (26, 'student15@gmail.com', 'db2pass', 'student 15', '999-999-9998');
INSERT INTO users (id, email, password, name, phone) VALUES (27, 'student16@gmail.com', 'db2pass', 'student 16', '999-999-9998');
INSERT INTO users (id, email, password, name, phone) VALUES (28, 'student17@gmail.com', 'db2pass', 'student 17', '999-999-9997');
INSERT INTO users (id, email, password, name, phone) VALUES (29, 'student18@gmail.com', 'db2pass', 'student 18', '999-999-9999');
INSERT INTO users (id, email, password, name, phone) VALUES (30, 'student19@gmail.com', 'db2pass', 'student 19', '999-999-9998');

INSERT INTO students (student_id, grade, parent_id) VALUES (12, 6, 2);
INSERT INTO students (student_id, grade, parent_id) VALUES (13, 6, 3);
INSERT INTO students (student_id, grade, parent_id) VALUES (14, 7, 4);
INSERT INTO students (student_id, grade, parent_id) VALUES (15, 8, 5);
INSERT INTO students (student_id, grade, parent_id) VALUES (16, 9, 6);
INSERT INTO students (student_id, grade, parent_id) VALUES (17, 9, 7);
INSERT INTO students (student_id, grade, parent_id) VALUES (18, 9, 8);
INSERT INTO students (student_id, grade, parent_id) VALUES (19, 10, 9);
INSERT INTO students (student_id, grade, parent_id) VALUES (20, 11, 10);
INSERT INTO students (student_id, grade, parent_id) VALUES (21, 12, 11);
INSERT INTO students (student_id, grade, parent_id) VALUES (22, 12, 2);
INSERT INTO students (student_id, grade, parent_id) VALUES (23, 9, 4);
INSERT INTO students (student_id, grade, parent_id) VALUES (24, 12, 5);
INSERT INTO students (student_id, grade, parent_id) VALUES (25, 12, 7);
INSERT INTO students (student_id, grade, parent_id) VALUES (26, 12, 7);
INSERT INTO students (student_id, grade, parent_id) VALUES (27, 6, 8);
INSERT INTO students (student_id, grade, parent_id) VALUES (28, 6, 4);
INSERT INTO students (student_id, grade, parent_id) VALUES (29, 9, 10);
INSERT INTO students (student_id, grade, parent_id) VALUES (30, 9, 6);

INSERT INTO `groups` (group_id, `name`, `description`, `mentor_grade_req`, `mentee_grade_req`) VALUES (6, 'Group 6', 6, 9, NULL);
INSERT INTO `groups` (group_id, `name`, `description`, `mentor_grade_req`, `mentee_grade_req`) VALUES (7, 'Group 7', 7, 10, NULL);
INSERT INTO `groups` (group_id, `name`, `description`, `mentor_grade_req`, `mentee_grade_req`) VALUES (8, 'Group 8', 8, 11, NULL);
INSERT INTO `groups` (group_id, `name`, `description`, `mentor_grade_req`, `mentee_grade_req`) VALUES (9, 'Group 9', 9, 12, 6);
INSERT INTO `groups` (group_id, `name`, `description`, `mentor_grade_req`, `mentee_grade_req`) VALUES (10, 'Group 10', 10, NULL, 7);
INSERT INTO `groups` (group_id, `name`, `description`, `mentor_grade_req`, `mentee_grade_req`) VALUES (11, 'Group 11', 11, NULL, 8);
INSERT INTO `groups` (group_id, `name`, `description`, `mentor_grade_req`, `mentee_grade_req`) VALUES (12, 'Group 12', 12, NULL, 9);

INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (1, 'Saturday', '16:00:00', '17:00:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (2, 'Saturday', '16:30:00', '17:30:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (3, 'Saturday', '17:00:00', '18:00:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (4, 'Saturday', '17:30:00', '18:30:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (5, 'Saturday', '18:00:00', '19:00:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (6, 'Saturday', '18:30:00', '19:30:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (7, 'Saturday', '19:00:00', '20:00:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (8, 'Saturday', '19:30:00', '20:30:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (9, 'Sunday', '16:00:00', '17:00:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (10, 'Sunday', '16:30:00', '17:30:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (11, 'Sunday', '17:00:00', '18:00:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (12, 'Sunday', '17:30:00', '18:30:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (13, 'Sunday', '18:00:00', '19:00:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (14, 'Sunday', '18:30:00', '19:30:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (15, 'Sunday', '19:00:00', '20:00:00');
INSERT INTO time_slot (time_slot_id, day_of_the_week, start_time, end_time) VALUES (16, 'Sunday', '19:30:00', '20:30:00');

INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (1, 'Math', '2020-03-21', 1, 6, 'Announcement 1', 6);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (2, 'English', '2020-03-21', 1, 6, 'Announcement 2', 7);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (3, 'English', '2020-03-21', 1, 6, 'Announcement 3', 8);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (4, 'Math', '2020-03-21', 1, 6, 'Announcement 4', 9);

INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (5, 'Math', '2020-04-11', 1, 6, 'Announcement 1', 6);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (6, 'Math', '2020-04-11', 1, 6, 'Announcement 2', 7);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (7, 'Math', '2020-04-11', 1, 6, 'Announcement 3', 8);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (8, 'Math', '2020-04-11', 1, 6, 'Announcement 4', 9);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (9, 'Math', '2020-04-11', 5, 6, 'Announcement 5', 6);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (10, 'Math', '2020-04-11', 5, 6, 'Announcement 6', 7);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (11, 'Math', '2020-04-11', 5, 6, 'Announcement 7', 8);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (12, 'Math', '2020-04-11', 5, 6, 'Announcement 8', 9);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (13, 'Math', '2020-04-12', 9, 6, 'Announcement 9', 6);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (14, 'Math', '2020-04-12', 9, 6, 'Announcement 10', 7);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (15, 'Math', '2020-04-12', 9, 6, 'Announcement 11', 8);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (16, 'Math', '2020-04-12', 9, 6, 'Announcement 12', 9);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (17, 'Math', '2020-04-18', 1, 6, 'Announcement 1', 6);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (18, 'Math', '2020-04-18', 1, 6, 'Announcement 2', 7);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (19, 'Math', '2020-04-18', 1, 6, 'Announcement 3', 8);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (20, 'Math', '2020-04-18', 1, 6, 'Announcement 4', 9);

INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (21, 'English', '2020-04-11', 1, 6, 'Announcement 1', 6);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (22, 'English', '2020-04-11', 1, 6, 'Announcement 2', 7);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (23, 'English', '2020-04-11', 1, 6, 'Announcement 3', 8);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (24, 'English', '2020-04-11', 1, 6, 'Announcement 4', 9);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (25, 'English', '2020-04-11', 5, 6, 'Announcement 5', 6);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (26, 'English', '2020-04-11', 5, 6, 'Announcement 6', 7);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (27, 'English', '2020-04-11', 5, 6, 'Announcement 7', 8);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (28, 'English', '2020-04-11', 5, 6, 'Announcement 8', 9);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (29, 'English', '2020-04-12', 9, 6, 'Announcement 9', 6);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (30, 'English', '2020-04-12', 9, 6, 'Announcement 10', 7);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (31, 'English', '2020-04-12', 9, 6, 'Announcement 11', 8);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (32, 'English', '2020-04-12', 9, 6, 'Announcement 12', 9);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (33, 'English', '2020-04-19', 1, 6, 'Announcement 1', 6);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (34, 'English', '2020-04-19', 1, 6, 'Announcement 2', 7);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (35, 'English', '2020-04-19', 1, 6, 'Announcement 3', 8);
INSERT INTO meetings (meet_id, meet_name, date, time_slot_id, capacity, announcement, group_id) VALUES (36, 'English', '2020-04-19', 1, 6, 'Announcement 4', 9);

INSERT INTO mentees (mentee_id) VALUES (12);
INSERT INTO mentees (mentee_id) VALUES (13);
INSERT INTO mentees (mentee_id) VALUES (14);
INSERT INTO mentees (mentee_id) VALUES (15);
INSERT INTO mentees (mentee_id) VALUES (16);
INSERT INTO mentees (mentee_id) VALUES (17);
INSERT INTO mentees (mentee_id) VALUES (18);
INSERT INTO mentees (mentee_id) VALUES (27);
INSERT INTO mentees (mentee_id) VALUES (28);
INSERT INTO mentees (mentee_id) VALUES (29);
INSERT INTO mentees (mentee_id) VALUES (30);

INSERT INTO mentors (mentor_id) VALUES (16);
INSERT INTO mentors (mentor_id) VALUES (17);
INSERT INTO mentors (mentor_id) VALUES (18);
INSERT INTO mentors (mentor_id) VALUES (19);
INSERT INTO mentors (mentor_id) VALUES (20);
INSERT INTO mentors (mentor_id) VALUES (21);
INSERT INTO mentors (mentor_id) VALUES (22);
INSERT INTO mentors (mentor_id) VALUES (23);
INSERT INTO mentors (mentor_id) VALUES (24);
INSERT INTO mentors (mentor_id) VALUES (25);
INSERT INTO mentors (mentor_id) VALUES (26);

INSERT INTO enroll (mentee_id, meet_id) VALUES (12, 5);
INSERT INTO enroll (mentee_id, meet_id) VALUES (13, 5);
INSERT INTO enroll (mentee_id, meet_id) VALUES (14, 6);
INSERT INTO enroll (mentee_id, meet_id) VALUES (15, 23);
INSERT INTO enroll (mentee_id, meet_id) VALUES (16, 12);
INSERT INTO enroll (mentee_id, meet_id) VALUES (17, 12);
INSERT INTO enroll (mentee_id, meet_id) VALUES (18, 12);
INSERT INTO enroll (mentee_id, meet_id) VALUES (27, 5);
INSERT INTO enroll (mentee_id, meet_id) VALUES (28, 5);
INSERT INTO enroll (mentee_id, meet_id) VALUES (29, 28);
INSERT INTO enroll (mentee_id, meet_id) VALUES (30, 28);

INSERT INTO enroll2 (mentor_id, meet_id) VALUES (16, 5);
INSERT INTO enroll2 (mentor_id, meet_id) VALUES (17, 5);
INSERT INTO enroll2 (mentor_id, meet_id) VALUES (18, 5);
INSERT INTO enroll2 (mentor_id, meet_id) VALUES (19, 6);
INSERT INTO enroll2 (mentor_id, meet_id) VALUES (20, 7);
INSERT INTO enroll2 (mentor_id, meet_id) VALUES (21, 12);
INSERT INTO enroll2 (mentor_id, meet_id) VALUES (22, 6);
INSERT INTO enroll2 (mentor_id, meet_id) VALUES (23, 21);
INSERT INTO enroll2 (mentor_id, meet_id) VALUES (24, 14);
INSERT INTO enroll2 (mentor_id, meet_id) VALUES (25, 14);
INSERT INTO enroll2 (mentor_id, meet_id) VALUES (26, 23);

INSERT INTO material (material_id, title, author, type, url, assigned_date, notes) VALUES (1, 'a', 'a', 'book', 'http://website.com/a', '2020-03-22', 'aaa');
INSERT INTO material (material_id, title, author, type, url, assigned_date, notes) VALUES (2, 'b', 'b', 'book', 'http://website.com/b', '2020-03-22', 'bbb');
INSERT INTO material (material_id, title, author, type, url, assigned_date, notes) VALUES (3, 'c', 'c', 'book', 'http://website.com/c', '2020-03-22', 'ccc');
INSERT INTO material (material_id, title, author, type, url, assigned_date, notes) VALUES (4, 'd', 'd', 'book', 'http://website.com/d', '2020-03-22', 'ddd');
INSERT INTO material (material_id, title, author, type, url, assigned_date, notes) VALUES (5, 'e', 'e', 'book', 'http://website.com/e', '2020-03-22', 'eee');
INSERT INTO material (material_id, title, author, type, url, assigned_date, notes) VALUES (6, 'f', 'f', 'book', 'http://website.com/f', '2020-03-22', 'fff');
INSERT INTO material (material_id, title, author, type, url, assigned_date, notes) VALUES (7, 'g', 'g', 'book', 'http://website.com/g', '2020-03-22', 'ggg');
INSERT INTO material (material_id, title, author, type, url, assigned_date, notes) VALUES (8, 'h', 'h', 'book', 'http://website.com/h', '2020-03-22', 'hhh');
INSERT INTO material (material_id, title, author, type, url, assigned_date, notes) VALUES (9, 'i', 'i', 'book', 'http://website.com/i', '2020-03-22', 'iii');
INSERT INTO material (material_id, title, author, type, url, assigned_date, notes) VALUES (10, 'j', 'j', 'book', 'http://website.com/j', '2020-03-22', 'jjj');

INSERT INTO assign (meet_id, material_id) VALUES (5, 1);
INSERT INTO assign (meet_id, material_id) VALUES (5, 2);
INSERT INTO assign (meet_id, material_id) VALUES (6, 3);
INSERT INTO assign (meet_id, material_id) VALUES (7, 4);
INSERT INTO assign (meet_id, material_id) VALUES (11, 5);
INSERT INTO assign (meet_id, material_id) VALUES (12, 6);
INSERT INTO assign (meet_id, material_id) VALUES (15, 7);
INSERT INTO assign (meet_id, material_id) VALUES (22, 8);
INSERT INTO assign (meet_id, material_id) VALUES (23, 9);
INSERT INTO assign (meet_id, material_id) VALUES (34, 10);
