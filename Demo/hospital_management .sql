-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2024 at 07:12 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hospital_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `doctor_email` varchar(100) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(150) NOT NULL,
  `appointment_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` varchar(50) DEFAULT 'Scheduled',
  `doctor_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `doctor_email`, `patient_id`, `patient_name`, `appointment_date`, `start_time`, `end_time`, `status`, `doctor_name`) VALUES
(2, 'Ah@gmail.com', 10, 'montasir mahmud saykat', '2024-11-21', '03:52:00', '04:52:00', 'Confirmed', 'Mahabub Ahmed'),
(3, 'Ah@gmail.com', 11, 'montasir mahmud saykat', '2024-11-19', '23:00:00', '00:00:00', 'Confirmed', 'Mahabub Ahmed'),
(4, 'mos@gmail.com', 12, 'montasir mahmud saykat', '2024-11-20', '00:00:00', '01:00:00', 'Confirmed', 'Dr.montasir saykat');

-- --------------------------------------------------------

--
-- Table structure for table `appointments_patients`
--

CREATE TABLE `appointments_patients` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `age` int(11) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text NOT NULL,
  `appointment_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time GENERATED ALWAYS AS (addtime(`start_time`,'01:00:00')) STORED,
  `patient_status` enum('New','Old') NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `doctor_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments_patients`
--

INSERT INTO `appointments_patients` (`id`, `email`, `name`, `phone_number`, `age`, `gender`, `address`, `appointment_date`, `start_time`, `patient_status`, `department_name`, `doctor_name`) VALUES
(1, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '01677251536', 24, 'Male', 'Dhaka', '2024-11-20', '15:28:00', 'New', 'Child and Adolescent Development Clinic', 'Dr. Farzana Ahmed'),
(2, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '01677251536', 24, 'Male', 'Dhaka', '2024-11-20', '16:30:00', 'New', 'Addiction Clinic', 'Dr. Moinul Haque'),
(3, 'saykatnsu2025@gmail.com', '', '', 0, 'Male', '', '0000-00-00', '00:00:00', 'New', 'Addiction Clinic', ''),
(4, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '01677251536', 24, 'Male', 'Dhaka', '2024-11-20', '09:58:00', 'New', 'Addiction Clinic', 'Mahabub Ahmed'),
(5, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '01677251536', 24, 'Male', 'Dhaka', '2024-11-23', '10:03:00', 'New', 'Addiction Clinic', 'Mahabub Ahmed'),
(6, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '0167789898989', 24, 'Male', 'Dhaka', '2024-11-29', '10:08:00', 'New', 'Addiction Clinic', 'Mahabub Ahmed'),
(7, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '0198989', 24, 'Male', 'Dhaka', '2024-11-22', '13:22:00', 'New', 'Addiction Clinic', 'Mahabub Ahmed'),
(8, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '0167789898989', 24, 'Male', 'Dhaka', '2024-11-25', '10:40:00', 'New', 'Addiction Clinic', 'Mahabub Ahmed'),
(9, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '0167789898989', 24, 'Male', 'Dhaka', '2024-11-25', '01:44:00', 'New', 'Addiction Clinic', 'Mahabub Ahmed'),
(10, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '0198989', 24, 'Male', 'Dhaka', '2024-11-21', '03:52:00', 'New', 'Addiction Clinic', 'Mahabub Ahmed'),
(11, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '0167789898989', 24, 'Male', 'Dhaka', '2024-11-19', '23:00:00', 'New', 'Addiction Clinic', 'Mahabub Ahmed'),
(12, 'saykatnsu2025@gmail.com', 'montasir mahmud saykat', '01677251536', 24, 'Male', 'Dhaka', '2024-11-20', '00:00:00', 'New', 'Psychotherapy Clinic', 'Dr.montasir saykat');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `department` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `first_name`, `last_name`, `phone_number`, `email`, `password`, `birthday`, `gender`, `department`, `profile_picture`) VALUES
(1, 'Mahabub', 'Ahmed', '01677251536', 'Ah@gmail.com', '$2y$10$GskBzltxkh5gq8OqqEOEcOTQDrN89AvnNAitCQzqBSr8zV0lXvhB6', '1991-02-18', 'Male', 'Addiction Clinic', NULL),
(2, 'Dr.Montasir ', 'Mahmud', '01677251536', 'sa@gamil.com', '$2y$10$rBDw1GvqFHgFkIhmjlzvpOwtlJMNWuYM0D6o65D2T0Inag/63eexK', '1993-02-22', 'Male', 'Psychotherapy Clinic', NULL),
(3, 'Dr.montasir', 'saykat', '017777', 'mos@gmail.com', '$2y$10$6tU6uraO1F7IFINnA1y3QuO6obFEH48m2hNaIxBUItlN25iGYZo/C', '2024-11-06', 'Male', 'Psychotherapy Clinic', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability`
--

CREATE TABLE `doctor_availability` (
  `id` int(11) NOT NULL,
  `doctor_email` varchar(255) DEFAULT NULL,
  `available_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_availability`
--

INSERT INTO `doctor_availability` (`id`, `doctor_email`, `available_date`, `start_time`, `end_time`) VALUES
(1, 'Ah@gmail.com', '2024-11-25', '12:08:00', '13:10:00'),
(2, 'Ah@gmail.com', '2024-11-27', '13:22:00', '19:23:00'),
(3, 'mos@gmail.com', '2024-11-22', '14:10:00', '17:14:00');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_edu`
--

CREATE TABLE `doctor_edu` (
  `edu_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `school` varchar(100) DEFAULT NULL,
  `college` varchar(100) DEFAULT NULL,
  `medical_college` varchar(100) DEFAULT NULL,
  `other_degrees` text DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `specialties` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_edu`
--

INSERT INTO `doctor_edu` (`edu_id`, `user_id`, `profile_picture`, `school`, `college`, `medical_college`, `other_degrees`, `father_name`, `mother_name`, `address`, `specialties`) VALUES
(1, 18, '673a2ac27bffd.jpg', 'Sher-e- bangla nagar govt girls  high school', 'Shaheed Bir Uttam Lt. Anwar Girls College', 'Green Life medical college', 'MBBS', 'sdasdasds', 'jesmin', 'west shawrapara,mirpur,dhaka', 'sink');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `birthday` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `role` enum('admin','doctor','patient') DEFAULT 'patient',
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `phone_number`, `email`, `password`, `birthday`, `gender`, `role`, `profile_picture`) VALUES
(10, 'Md.Mostak', 'Ahmed', '01405697800', 'md@gmail.com', '$2y$10$OqwM7m6OGu8mDBg8zQRbkeQv3JIw5hscsLxmniipKyoFghqk2LoQ2', '2024-11-13', 'Male', 'patient', NULL),
(18, 'Md.Mostak', 'Ahmed', '01405697800', 'mda@gmail.com', '$2y$10$8RfhyH7.YNQZHAcSqXLRGOdzJH6xdpKBaWzn3CwOwxXmEC2u/vrKq', '2024-11-15', 'Male', 'doctor', NULL),
(19, 'sabiha', 'jui', '01758697892', 'jka@gmail.com', '$2y$10$RKXbckiTsw0U0cm8LHXajuEbJeSmkslf.i5sP/tt55NyWvqhYprHG', '2002-08-12', 'Female', 'doctor', NULL),
(20, 'mahabub', 'Ahmed', '01405697800', 'mahabubkowsar21@gmail.com', '$2y$10$BvNBepZ/kq3yV0Akj7wjtegzn3Pca4KI6KcdLYvH6V.BYlq7B61a2', '2024-11-17', 'Male', 'doctor', NULL),
(21, 'adsa', 'asdads', '01405697800', 'mdw@gmail.com', '$2y$10$PPizUpIggEoZdpoN9TAJ6.ezjxvjulSgq/8aX2WzYXnAqhmlUBbxa', '2024-11-17', 'Female', 'doctor', NULL),
(22, 'montasir', 'saykat', '01677251536', 'saykatnsu2025@gmail.com', '$2y$10$CHGzZGOtr3lHqOW/TkhlBOgSRZ.vjvI/wgtlLfw8sNcFiGlmvdtRK', '2024-11-08', 'Male', 'patient', NULL),
(23, ' Moinul ', 'Haque', '01677251536', 'moinul@gmail.com', '$2y$10$Fj1.137TgGt6hxcmTMKD..h6IJelfR0wbdDy22AiHyrsBXPZYY.c2', '2024-11-01', 'Male', 'doctor', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `doctor_email` (`doctor_email`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `appointments_patients`
--
ALTER TABLE `appointments_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_email` (`doctor_email`);

--
-- Indexes for table `doctor_edu`
--
ALTER TABLE `doctor_edu`
  ADD PRIMARY KEY (`edu_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `appointments_patients`
--
ALTER TABLE `appointments_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctor_edu`
--
ALTER TABLE `doctor_edu`
  MODIFY `edu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`doctor_email`) REFERENCES `doctors` (`email`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `appointments_patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD CONSTRAINT `doctor_availability_ibfk_1` FOREIGN KEY (`doctor_email`) REFERENCES `doctors` (`email`);

--
-- Constraints for table `doctor_edu`
--
ALTER TABLE `doctor_edu`
  ADD CONSTRAINT `doctor_edu_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
