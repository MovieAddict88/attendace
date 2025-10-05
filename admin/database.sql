--
-- Database: `quiz_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Science'),
(2, 'History'),
(3, 'Math');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option1` varchar(255) NOT NULL,
  `option2` varchar(255) NOT NULL,
  `option3` varchar(255) NOT NULL,
  `option4` varchar(255) NOT NULL,
  `correct_option` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`category_id`, `question_text`, `option1`, `option2`, `option3`, `option4`, `correct_option`) VALUES
(1, 'What is the chemical symbol for water?', 'O2', 'H2O', 'CO2', 'NaCl', 2),
(1, 'Which planet is known as the Red Planet?', 'Earth', 'Mars', 'Jupiter', 'Venus', 2),
(2, 'Who was the first president of the United States?', 'Abraham Lincoln', 'George Washington', 'Thomas Jefferson', 'John Adams', 2),
(2, 'In which year did World War II end?', '1942', '1945', '1950', '1939', 2),
(3, 'What is 2 + 2?', '3', '4', '5', '6', 2),
(3, 'What is 10 * 5?', '45', '50', '55', '60', 2);