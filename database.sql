-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Дек 04 2015 г., 01:50
-- Версия сервера: 5.5.46-0ubuntu0.14.04.2
-- Версия PHP: 5.5.9-1ubuntu4.14

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
  `even` text COLLATE utf8_unicode_ci NOT NULL,
  `uneven` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `Config`
--

INSERT INTO `Config` (`even`, `uneven`) VALUES
('ch', 'zn'),
('ch', 'zn'),
('ch', 'zn'),
('ch', 'zn'),
('ch', 'zn'),
('ch', 'zn'),
('ch', 'zn');

-- --------------------------------------------------------

--
-- Структура таблицы `departments_list`
--

CREATE TABLE IF NOT EXISTS `departments_list` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `depart_name` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `departments_list`
--

INSERT INTO `departments_list` (`ID`, `depart_name`) VALUES
(1, 'Informatiki');

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
  `lesson_name` text COLLATE utf8_unicode_ci NOT NULL,
  `classroom` int(11) NOT NULL,
  `numerator` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `professor_id` (`professor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=43 ;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`id`, `group_number`, `day_number`, `lesson_number`, `professor_id`, `lesson_name`, `classroom`, `numerator`) VALUES
(18, 32494, 1, 4, 7, 'Экономика и организация', 201, 'all'),
(19, 32494, 1, 5, 7, 'Охрана труда', 201, 'all'),
(20, 32494, 1, 6, 7, 'Основы менеджмента', 201, 'all'),
(21, 32494, 2, 4, 7, 'Математическое моделирование', 201, 'ch'),
(22, 32494, 2, 5, 7, 'Техническое разработка программного обеспечения', 201, 'ch'),
(23, 32494, 2, 6, 7, 'Прикладное программное обеспечение', 201, 'all'),
(24, 32494, 2, 7, 7, 'Конструирование программ и языки программирования', 201, 'all'),
(25, 32494, 3, 4, 7, 'Физическая культура и здоровье (девушки)', 201, 'ch'),
(26, 32494, 3, 5, 7, 'Прикладное программное обеспечение', 201, 'ch'),
(27, 32494, 3, 6, 7, 'Техническое разработка программного обеспечения', 201, 'all'),
(28, 32494, 3, 7, 7, 'Защита компьютерной информации', 201, 'all'),
(29, 32494, 4, 4, 7, 'Кураторский час', 201, 'all'),
(30, 32494, 4, 5, 7, 'Охрана труда', 201, 'all'),
(31, 32494, 4, 6, 7, 'Конструирование программ и языки программирования', 201, 'all'),
(32, 32494, 4, 7, 7, 'Тестирование и отладка программного обеспечения', 201, 'all'),
(33, 32494, 5, 3, 7, 'Физическая культура и здоровье (девушки)', 201, 'all'),
(34, 32494, 5, 4, 7, 'Экономика и организация', 201, 'all'),
(35, 32494, 5, 5, 7, 'Математическое моделирование', 201, 'ch'),
(36, 32494, 5, 6, 7, 'Защита компьютерной информации', 201, 'all'),
(37, 32494, 6, 3, 7, 'Конструирование программ и языки программирования', 201, 'all'),
(38, 32494, 6, 4, 7, 'Физическая культура и здоровье (юноши)', 201, 'all'),
(39, 32494, 2, 4, 7, 'Математическое моделирование', 201, 'zn'),
(40, 32494, 2, 5, 7, 'Физическая культура и здоровье (юноши)', 201, 'zn'),
(41, 32494, 5, 5, 7, 'Техническое разработка программного обеспечения', 201, 'zn'),
(42, 32494, 1, 7, 7, 'Техническое разработка программного обеспечения', 201, 'all');

-- --------------------------------------------------------

--
-- Структура таблицы `groups_list`
--

CREATE TABLE IF NOT EXISTS `groups_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_number` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `groups_list`
--

INSERT INTO `groups_list` (`id`, `group_number`, `grade`) VALUES
(1, 32494, 3),
(2, 22494, 4),
(3, 32491, 3),
(4, 52492, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `notification`
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `notification`
--

INSERT INTO `notification` (`id`, `state`, `group_number`, `text`, `starting_date`, `ending_date`) VALUES
(1, 'critical', 32494, 'мэйдэй-мэйдэй', '2015-12-03', '2015-12-24'),
(2, 'warning', 0, 'все хорошо', '2015-12-02', '2015-12-24'),
(3, 'warning', 32494, 'все не очень хорошо', '2015-12-01', '2015-12-31');

-- --------------------------------------------------------

--
-- Структура таблицы `professors`
--

CREATE TABLE IF NOT EXISTS `professors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `professor` text COLLATE utf8_unicode_ci NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `photo_url` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `departmenet_id` (`department_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `professors`
--

INSERT INTO `professors` (`id`, `professor`, `department_id`, `photo_url`) VALUES
(7, 'Апанасевич С.А.', 1, '');

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
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Hhe972e64KBdHZT', 'Admin', 'Администратор', '');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `professors`
--
ALTER TABLE `professors`
  ADD CONSTRAINT `professors_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments_list` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
