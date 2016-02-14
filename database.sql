-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 14, 2016 at 05:46 PM
-- Server version: 5.5.47-0ubuntu0.14.04.1
-- PHP Version: 5.6.18-1+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `MGVRK`
--

-- --------------------------------------------------------

--
-- Table structure for table `Config`
--

CREATE TABLE IF NOT EXISTS `Config` (
  `id` int(11) NOT NULL,
  `even` text CHARACTER SET latin1 NOT NULL,
  `uneven` text CHARACTER SET latin1 NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `Config`
--

INSERT INTO `Config` (`id`, `even`, `uneven`) VALUES
(0, 'ch', 'zn');

-- --------------------------------------------------------

--
-- Table structure for table `departments_list`
--

CREATE TABLE IF NOT EXISTS `departments_list` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `depart_name` text COLLATE utf8_unicode_ci NOT NULL,
  `code` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`),
  UNIQUE KEY `code` (`code`),
  KEY `code_2` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `departments_list`
--

INSERT INTO `departments_list` (`ID`, `depart_name`, `code`) VALUES
(1, 'Программное обеспечение информационных технологий', 1),
(2, 'Проектирование и производство радиоэлектронных средств', 2),
(3, 'Техническая эксплуатациия радиоэлектронных средств', 3),
(4, 'Электронные вычислительные средства', 4),
(5, 'Микро- и наноэлектроника', 5),
(6, 'Социально-гуманитарные дисциплины', 6),
(7, 'Физическое воспитание', 7);

-- --------------------------------------------------------

--
-- Table structure for table `faculty_list`
--

CREATE TABLE IF NOT EXISTS `faculty_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `code` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `faculty_list`
--

INSERT INTO `faculty_list` (`id`, `name`, `code`) VALUES
(1, 'Компьютерных технологий', 2),
(2, 'Радиотехническое', 1);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_number` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `lesson_number` int(11) NOT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `lesson_name` text COLLATE utf8_unicode_ci NOT NULL,
  `classroom` text COLLATE utf8_unicode_ci NOT NULL,
  `numerator` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `professor_id` (`professor_id`),
  KEY `professor_id_2` (`professor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=26 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `group_number`, `day_number`, `lesson_number`, `professor_id`, `lesson_name`, `classroom`, `numerator`) VALUES
(1, 32494, 1, 4, 1, 'Экономика и организация', '201', 'all'),
(2, 32494, 1, 5, 2, 'Охрана труда', '201', 'all'),
(3, 32494, 1, 6, 1, 'Основы менеджмента', '201', 'all'),
(4, 32494, 2, 4, 1, 'Математическое моделирование', '201', 'zn'),
(5, 32494, 2, 4, 1, 'Математическое моделирование', '201', 'ch'),
(6, 32494, 1, 7, 1, 'Техническое разработка программного обеспечения', '201', 'all'),
(7, 32494, 2, 5, 1, 'Физическая культура и здоровье (юноши)', '201', 'zn'),
(8, 32494, 2, 5, 1, 'Техническое разработка программного обеспечения', '201', 'ch'),
(9, 32494, 2, 6, 1, 'Прикладное программное обеспечение', '201', 'all'),
(10, 32494, 2, 7, 1, 'Конструирование программ и языки программирования', '201', 'all'),
(11, 32494, 3, 4, 1, 'Физическая культура и здоровье (девушки)', '201', 'ch'),
(12, 32494, 3, 5, 1, 'Прикладное программное обеспечение', '201', 'ch'),
(13, 32494, 3, 6, 1, 'Техническое разработка программного обеспечения', '201', 'all'),
(14, 32494, 3, 7, 1, 'Защита компьютерной информации', '201', 'all'),
(15, 32494, 4, 4, 1, 'Кураторский час', '201', 'all'),
(16, 32494, 4, 5, 1, 'Охрана труда', '201', 'all'),
(17, 32494, 4, 7, 3, 'Конструирование программ и языки программирования', '201', 'all'),
(18, 32494, 4, 7, 1, 'Тестирование и отладка программного обеспечения', '201', 'all'),
(19, 32494, 5, 3, 1, 'Физическая культура и здоровье (девушки)', 'Спорт. Зал', 'all'),
(20, 32494, 5, 4, 2, 'Экономика организаций', '407', 'all'),
(21, 32494, 5, 5, 3, 'Математическое моделирование', '108', 'ch'),
(22, 32494, 5, 5, 4, 'Техническое разработка программного обеспечения', '201', 'zn'),
(23, 32494, 5, 6, 4, 'Защита компьютерной информации', '117', 'all'),
(24, 32494, 6, 3, 1, 'Конструирование программ и языки программирования', '201', 'all'),
(25, 32494, 6, 4, 2, 'Физическая культура и здоровье (юноши)', '201', 'all');

-- --------------------------------------------------------

--
-- Table structure for table `groups_list`
--

CREATE TABLE IF NOT EXISTS `groups_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_number` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `class` int(11) NOT NULL,
  `specialization` int(11) NOT NULL,
  `faculty` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `faculty` (`faculty`),
  KEY `specialization` (`specialization`),
  KEY `specialization_2` (`specialization`),
  KEY `faculty_2` (`faculty`),
  KEY `specialization_3` (`specialization`,`faculty`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `groups_list`
--

INSERT INTO `groups_list` (`id`, `group_number`, `grade`, `class`, `specialization`, `faculty`) VALUES
(1, 32494, 3, 9, 4, 2),
(2, 22414, 4, 1, 4, 2),
(3, 32791, 3, 9, 7, 2),
(4, 52412, 1, 1, 4, 2),
(6, 12594, 2, 0, 5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE IF NOT EXISTS `holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_number` text COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `type` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`id`, `group_number`, `date`, `type`) VALUES
(1, '32494', '2016-02-07', 'weekend');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `group_number` int(11) NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `starting_date` date NOT NULL,
  `ending_date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_2` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `state`, `group_number`, `text`, `starting_date`, `ending_date`) VALUES
(1, 'alert', 32494, 'мэйдэй-мэйдэй', '2015-12-03', '2015-12-24'),
(2, 'alert', 0, 'все хорошо', '2015-12-02', '2015-12-24'),
(3, 'info', 32494, 'все не очень хорошо', '2015-12-01', '2015-12-31'),
(11, 'info', 32494, 'Уведомление1', '2016-01-09', '2017-09-20'),
(12, 'info', 32494, '32494', '2016-02-06', '2016-09-20');

-- --------------------------------------------------------

--
-- Table structure for table `professors`
--

CREATE TABLE IF NOT EXISTS `professors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `professor` text COLLATE utf8_unicode_ci NOT NULL,
  `department_code` int(11) DEFAULT NULL,
  `photo_url` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `departmenet_id` (`department_code`),
  KEY `department_code` (`department_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`id`, `professor`, `department_code`, `photo_url`) VALUES
(1, 'Дерман У.В.', 7, ''),
(2, 'Чикун Е.О.', 6, ''),
(3, 'Смолер И. Г.', 1, ''),
(4, 'Черкас А. М.', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE IF NOT EXISTS `shift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number_gr` int(11) NOT NULL,
  `shift` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`id`, `number_gr`, `shift`) VALUES
(1, 32494, 'first');

-- --------------------------------------------------------

--
-- Table structure for table `specialization_list`
--

CREATE TABLE IF NOT EXISTS `specialization_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `code` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `specialization_list`
--

INSERT INTO `specialization_list` (`id`, `name`, `code`) VALUES
(1, 'Нанотехника', 5),
(2, 'Микроэллектронника', 7),
(3, 'ПОИТ', 4);

-- --------------------------------------------------------

--
-- Table structure for table `swap`
--

CREATE TABLE IF NOT EXISTS `swap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number_gr` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `para` int(11) NOT NULL,
  `lesson` text COLLATE utf8_unicode_ci NOT NULL,
  `professor` text COLLATE utf8_unicode_ci NOT NULL,
  `date_swap` int(11) NOT NULL,
  `smena` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE IF NOT EXISTS `timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num_lesson` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `num_lesson`, `start_time`, `end_time`) VALUES
(1, 1, '08:00:00', '09:35:00'),
(2, 2, '09:45:00', '11:20:00'),
(3, 3, '11:40:00', '13:15:00'),
(4, 4, '13:25:00', '15:00:00'),
(5, 5, '15:20:00', '16:55:00'),
(6, 6, '17:05:00', '18:40:00'),
(7, 7, '18:50:00', '20:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` text COLLATE utf8_unicode_ci NOT NULL,
  `password` text COLLATE utf8_unicode_ci NOT NULL,
  `hash` text COLLATE utf8_unicode_ci NOT NULL,
  `privilege` text COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `number_gr` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `hash`, `privilege`, `name`, `number_gr`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Dybiky3Q8iR6fyy', 'Admin', 'Администратор', '');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `groups_list`
--
ALTER TABLE `groups_list`
  ADD CONSTRAINT `groups_list_ibfk_1` FOREIGN KEY (`faculty`) REFERENCES `faculty_list` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `groups_list_ibfk_2` FOREIGN KEY (`specialization`) REFERENCES `specialization_list` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `professors`
--
ALTER TABLE `professors`
  ADD CONSTRAINT `professors_ibfk_1` FOREIGN KEY (`department_code`) REFERENCES `departments_list` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
