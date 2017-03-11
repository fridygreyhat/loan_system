-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2017 at 02:21 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `loandisk2`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrower`
--

CREATE TABLE IF NOT EXISTS `borrower` (
  `bid` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `mnumber` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `address` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `landline` varchar(255) NOT NULL,
  `bname` varchar(255) NOT NULL,
  `wstatus` varchar(255) NOT NULL,
  `pimage` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `staffacess` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `borrower`
--

INSERT INTO `borrower` (`bid`, `fname`, `lname`, `gender`, `country`, `title`, `mnumber`, `dob`, `address`, `city`, `landline`, `bname`, `wstatus`, `pimage`, `description`, `attachment`, `staffacess`) VALUES
(1, 'asasa', 'asss', 'Female', 'SR', '3', '', '0000-00-00', '', '', '', '', '', '', 'Array', '', ''),
(2, 'kanani', 'Ronald', 'Female', 'SR', '2', '', '0000-00-00', 'ssddfdd', 'kampala', '01221122112', 'klujkkj', 'Employee', 'logo3.png', 'Array', '', 'Array'),
(3, 'monica', 'MOngyeni', 'Female', 'UG', '2', '042157896', '0000-00-00', 'MATEEKA', 'KAMPALA', '01442222', 'NON', 'Employee', 'thinkx add.PNG', 'Array', '', 'Array'),
(4, 'QQQ', 'QQ', 'Male', 'UG', '0', '', '0000-00-00', '', '', '', '', '', '', 'Array', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `name`, `age`) VALUES
(1, 'anish', 12),
(2, 'dennis', 23);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrower`
--
ALTER TABLE `borrower`
  ADD PRIMARY KEY (`bid`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrower`
--
ALTER TABLE `borrower`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
