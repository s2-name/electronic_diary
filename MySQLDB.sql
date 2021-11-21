-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Ноя 21 2021 г., 13:10
-- Версия сервера: 8.0.27-0ubuntu0.21.04.1
-- Версия PHP: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `temp`
--

-- --------------------------------------------------------

--
-- Структура таблицы `daily_group_statistics`
--

CREATE TABLE `daily_group_statistics` (
  `id` int NOT NULL,
  `group_id` int NOT NULL,
  `date` varchar(10) NOT NULL,
  `percent` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `disciplines`
--

CREATE TABLE `disciplines` (
  `id` int NOT NULL,
  `title` varchar(20) NOT NULL,
  `group_id` int NOT NULL,
  `teacher_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `disciplines`
--

INSERT INTO `disciplines` (`id`, `title`, `group_id`, `teacher_id`) VALUES
(1, 'example discipline', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE `groups` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`id`, `name`) VALUES
(1, 'example group');

-- --------------------------------------------------------

--
-- Структура таблицы `months`
--

CREATE TABLE `months` (
  `id` int NOT NULL,
  `month` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `scores`
--

CREATE TABLE `scores` (
  `id` int NOT NULL,
  `score` int NOT NULL,
  `discipline_id` int NOT NULL,
  `student_id` varchar(11) NOT NULL,
  `date` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Структура таблицы `students`
--

CREATE TABLE `students` (
  `id` varchar(11) NOT NULL,
  `group_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `login` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `students`
--

INSERT INTO `students` (`id`, `group_id`, `full_name`, `login`, `password`) VALUES
('E2 50 30 25', 1, 'Example student (0000)', 'student', '4a7d1ed414474e4033ac29ccb8653d9b');

-- --------------------------------------------------------

--
-- Структура таблицы `teachers`
--

CREATE TABLE `teachers` (
  `id` int NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `login` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `teachers`
--

INSERT INTO `teachers` (`id`, `full_name`, `login`, `password`) VALUES
(1, 'example reacher (0000)', 'teacher', '0000');

-- --------------------------------------------------------

--
-- Структура таблицы `visits`
--

CREATE TABLE `visits` (
  `id` int NOT NULL,
  `student_id` varchar(11) NOT NULL,
  `date` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `daily_group_statistics`
--
ALTER TABLE `daily_group_statistics`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `disciplines`
--
ALTER TABLE `disciplines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Индексы таблицы `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `months`
--
ALTER TABLE `months`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discipline_id` (`discipline_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Индексы таблицы `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Индексы таблицы `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `daily_group_statistics`
--
ALTER TABLE `daily_group_statistics`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `disciplines`
--
ALTER TABLE `disciplines`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `months`
--
ALTER TABLE `months`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `scores`
--
ALTER TABLE `scores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `visits`
--
ALTER TABLE `visits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `disciplines`
--
ALTER TABLE `disciplines`
  ADD CONSTRAINT `disciplines_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `disciplines_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Ограничения внешнего ключа таблицы `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`discipline_id`) REFERENCES `disciplines` (`id`),
  ADD CONSTRAINT `scores_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Ограничения внешнего ключа таблицы `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
