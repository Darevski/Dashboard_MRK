-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Мар 15 2016 г., 22:30
-- Версия сервера: 5.5.47-0ubuntu0.14.04.1
-- Версия PHP: 5.6.19-1+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `MGVRK`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Config`
--

CREATE TABLE IF NOT EXISTS `Config` (
  `id` int(11) NOT NULL,
  `even` text CHARACTER SET latin1 NOT NULL,
  `uneven` text CHARACTER SET latin1 NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `Config`
--

INSERT INTO `Config` (`id`, `even`, `uneven`) VALUES
(0, 'ch', 'zn');

-- --------------------------------------------------------

--
-- Структура таблицы `departments_list`
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
-- Дамп данных таблицы `departments_list`
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
-- Структура таблицы `faculty_list`
--

CREATE TABLE IF NOT EXISTS `faculty_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `code` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `faculty_list`
--

INSERT INTO `faculty_list` (`id`, `name`, `code`) VALUES
(1, 'Компьютерных технологий', 2),
(2, 'Радиотехническое', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_number` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `lesson_number` int(11) NOT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `lesson_id` int(11) NOT NULL,
  `classroom` text COLLATE utf8_unicode_ci NOT NULL,
  `numerator` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `professor_id` (`professor_id`),
  KEY `professor_id_2` (`professor_id`),
  KEY `lesson_id` (`lesson_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=26 ;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`id`, `group_number`, `day_number`, `lesson_number`, `professor_id`, `lesson_id`, `classroom`, `numerator`) VALUES
(1, 32494, 1, 4, 1, 3, '201', 'all'),
(2, 32494, 1, 5, 2, 3, '201', 'all'),
(3, 32494, 1, 6, 1, 3, '201', 'all'),
(4, 32494, 2, 4, 1, 3, '201', 'zn'),
(5, 32494, 2, 4, 1, 3, '201', 'ch'),
(6, 32494, 1, 7, 1, 3, '201', 'all'),
(7, 32494, 2, 5, 1, 3, '201', 'zn'),
(8, 32494, 2, 5, 1, 3, '201', 'ch'),
(9, 32494, 2, 6, 1, 3, '201', 'all'),
(10, 32494, 2, 7, 1, 3, '201', 'all'),
(11, 32494, 3, 4, 1, 3, '201', 'ch'),
(12, 32494, 3, 5, 1, 3, '201', 'ch'),
(13, 32494, 3, 6, 1, 3, '201', 'all'),
(14, 32494, 3, 7, 1, 3, '201', 'all'),
(15, 32494, 4, 4, 1, 3, '201', 'all'),
(16, 32494, 4, 5, 1, 3, '201', 'all'),
(17, 32494, 4, 7, 3, 3, '201', 'all'),
(18, 32494, 4, 7, 1, 3, '201', 'all'),
(19, 32494, 5, 3, 1, 3, 'Спорт. Зал', 'all'),
(20, 32494, 5, 4, 2, 3, '407', 'all'),
(21, 32494, 5, 5, 3, 3, '108', 'ch'),
(22, 32494, 5, 5, 4, 3, '201', 'zn'),
(23, 32494, 5, 6, 4, 3, '117', 'all'),
(24, 32494, 6, 3, 1, 3, '201', 'all'),
(25, 32494, 6, 4, 2, 3, '201', 'all');

-- --------------------------------------------------------

--
-- Структура таблицы `groups_list`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `groups_list`
--

INSERT INTO `groups_list` (`id`, `group_number`, `grade`, `class`, `specialization`, `faculty`) VALUES
(1, 32494, 3, 9, 4, 2),
(2, 22414, 4, 1, 4, 2),
(3, 32791, 3, 9, 7, 2),
(4, 52412, 1, 1, 4, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `holidays`
--

CREATE TABLE IF NOT EXISTS `holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_number` text COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `type` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `holidays`
--

INSERT INTO `holidays` (`id`, `group_number`, `date`, `type`) VALUES
(1, '32494', '2016-02-07', 'weekend');

-- --------------------------------------------------------

--
-- Структура таблицы `lessons_list`
--

CREATE TABLE IF NOT EXISTS `lessons_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `department_code` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `department_code` (`department_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `lessons_list`
--

INSERT INTO `lessons_list` (`id`, `name`, `department_code`) VALUES
(3, 'ФИЗРа', 7),
(4, 'Физ Культура', 7);

-- --------------------------------------------------------

--
-- Структура таблицы `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `state` text COLLATE utf8_unicode_ci NOT NULL,
  `group_number` int(11) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `starting_date` date NOT NULL,
  `ending_date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_2` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

--
-- Дамп данных таблицы `notification`
--

INSERT INTO `notification` (`id`, `state`, `group_number`, `text`, `starting_date`, `ending_date`) VALUES
(24, 'info', 32494, 'second', '2016-02-22', '2016-03-10');

-- --------------------------------------------------------

--
-- Структура таблицы `professors`
--

CREATE TABLE IF NOT EXISTS `professors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `department_code` int(11) DEFAULT NULL,
  `photo_url` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `departmenet_id` (`department_code`),
  KEY `department_code` (`department_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `professors`
--

INSERT INTO `professors` (`id`, `name`, `department_code`, `photo_url`) VALUES
(1, 'Дерман У.В.', 7, ''),
(2, 'Чикун Е.О.', 6, ''),
(3, 'Смолер И. Г.', 1, ''),
(4, 'Черкас А. М.', 1, '');

-- --------------------------------------------------------

--
-- Структура таблицы `shift`
--

CREATE TABLE IF NOT EXISTS `shift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number_gr` int(11) NOT NULL,
  `shift` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `shift`
--

INSERT INTO `shift` (`id`, `number_gr`, `shift`) VALUES
(1, 32494, 'first');

-- --------------------------------------------------------

--
-- Структура таблицы `specialization_list`
--

CREATE TABLE IF NOT EXISTS `specialization_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `code` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `specialization_list`
--

INSERT INTO `specialization_list` (`id`, `name`, `code`) VALUES
(1, 'Нанотехника', 5),
(2, 'Микроэллектронника', 7),
(3, 'ПОИТ', 4);

-- --------------------------------------------------------

--
-- Структура таблицы `swap`
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
-- Структура таблицы `timetable`
--

CREATE TABLE IF NOT EXISTS `timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num_lesson` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `timetable`
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
-- Структура таблицы `users`
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
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `hash`, `privilege`, `name`, `number_gr`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'yH7RH7kAFFTRENe', 'Admin', 'Администратор', '');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons_list` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `groups_list`
--
ALTER TABLE `groups_list`
  ADD CONSTRAINT `groups_list_ibfk_1` FOREIGN KEY (`faculty`) REFERENCES `faculty_list` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `groups_list_ibfk_2` FOREIGN KEY (`specialization`) REFERENCES `specialization_list` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `lessons_list`
--
ALTER TABLE `lessons_list`
  ADD CONSTRAINT `lessons_list_ibfk_1` FOREIGN KEY (`department_code`) REFERENCES `departments_list` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `professors`
--
ALTER TABLE `professors`
  ADD CONSTRAINT `professors_ibfk_1` FOREIGN KEY (`department_code`) REFERENCES `departments_list` (`code`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
