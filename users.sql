-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Сен 21 2020 г., 10:34
-- Версия сервера: 5.5.62
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `users`
--

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `date` varchar(50) NOT NULL,
  `text` varchar(500) NOT NULL,
  `edit_check` int(2) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `parent_id`, `user_id`, `date`, `text`, `edit_check`) VALUES
(139, NULL, 50, '2020-09-18 14:35:29', 'aadadsasdsdasd', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(15) NOT NULL,
  `second_name` varchar(15) NOT NULL,
  `email` varchar(30) NOT NULL,
  `number` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `town` varchar(30) NOT NULL,
  `password` varchar(61) NOT NULL,
  `image` varchar(266) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `first_name`, `second_name`, `email`, `number`, `date`, `town`, `password`, `image`) VALUES
(40, 'Valera', 'Dodkin', 'Dodkin@gmail.con', '099999999', '2020-09-07', 'Poltava', '$2y$10$k/0m/0r2hLv2rsVkC8HJwuDCQy5W731FJwJRBGfdlM5ofAfcEHrvO', NULL),
(41, 'Valera', 'Dodkin', 'fffffffff@gmail.con', '099999999', '2020-09-07', 'Poltava', '$2y$10$VBWaaq6Z9jryT9NXrlwd4Or8uyUYAVWOZRGvSYE2EZYNUsvyW.Xu6', NULL),
(42, 'Valera', 'Dodkin', 'fffffgggffff@gmail.con', '099999999', '2020-09-07', 'Poltava', '$2y$10$CgQKMkElH/203bRYQB9qfOcwGSPB6FaNyBKM/5FMKB8.KN3B6JMlK', NULL),
(43, 'Dimon', 'Syrotyak', 'Dimon@gmail.com', '0969089358', '1999-03-04', 'Komsomolsk', '$2y$10$iCDvpVWgNfnKTQ0v6GjcA.GL68Yshx1bmsZrjGhYtYXcm7AK/9FGG', NULL),
(44, 'Valera', 'Dodkin', '10000@gmail.con', '099999999', '2020-09-07', 'Poltava', '$2y$10$s6YdY33y0I7mJbx2K6tNFuWVWb5NMqqU1vJLDMFPJUu3Xqnyh15uG', NULL),
(46, 'Dima', 'Syrotiak', 'syrotyaka@gmail.com', '0969089358', '2012-12-01', 'Plotava', '$2y$10$ZtvtzpPi69liZkrw3DuKAeg.GpJISTSWK0ro2cHSvKy56d/gXS.ie', NULL),
(47, 'Dima', 'Syrotiak', 'Jamser81@gmail.com', '0969089358', '2012-12-01', 'Plotava', '$2y$10$O7lMCvhtXxGhXfpede552.2EF507MahII75FsMpnT9r9L56SGwnP2', NULL),
(48, 'Dima', 'Syrotiak', 'Jamser511@gmail.com', '0969089358', '2012-12-01', 'Plotava', '$2y$10$EQm5FFEwa3lOLh1N3lxk2./y5/wTCMYn8SYpcFMhFYYH4bvp7sQFu', NULL),
(49, 'Dimon', 'Syrotiak', 'Jamser52@gmail.com', '0969089358', '2012-10-05', 'Poltava', '$2y$10$eZ2Rq3c9Vqig/KA0dymzNOJYB4SZkht81yUJKRTVDdWgwfefFENlK', 'user-49'),
(50, 'Бовбас', 'Даніїл', 'syrotyaka2@gmail.com', '0969089358', '2012-06-29', 'Plotava', '$2y$10$tcd2WFJy0fvXlWaiMQFbQeU08vP/Rp8dGFSrVaWG84Sh0k0IL/ZuS', 'user-50'),
(51, 'Dima', 'Syrotiak', 'sypotiak.sbase@gmail.com', '0969089358', '2012-12-01', 'Plotava', '$2y$10$f96riF82VcVz11BFZ.HCoeB/6FjxB26pt1/tyWnapD0xLEf9Tjnlu', 'user-51');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
