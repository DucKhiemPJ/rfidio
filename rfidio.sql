
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `admin` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `admin_name` VARCHAR(30) NOT NULL,
  `admin_email` VARCHAR(80) NOT NULL,
  `admin_pwd` LONGTEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `admin` (`id`, `admin_name`, `admin_email`, `admin_pwd`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$89uX3LBy4mlU/DcBveQ1l.32nSianDP/E1MfUh.Z.6B4Z0ql3y7PK');


CREATE TABLE `devices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `device_name` VARCHAR(50) NOT NULL,
  `device_dep` VARCHAR(20) NOT NULL,
  `device_uid` TEXT NOT NULL,
  `device_date` DATE NOT NULL,
  `device_mode` TINYINT(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pwd_reset` (
  `pwd_reset_id` INT(11) NOT NULL AUTO_INCREMENT,
  `pwd_reset_email` VARCHAR(255) NOT NULL,
  `pwd_reset_selector` VARCHAR(255) NOT NULL,
  `pwd_reset_token` TEXT NOT NULL,
  `pwd_reset_expires` INT(11) NOT NULL,
  PRIMARY KEY (`pwd_reset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `goods` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `good` VARCHAR(30) NOT NULL DEFAULT 'None',
  `serialnumber` VARCHAR(255) NOT NULL DEFAULT '0',
  `fragile` VARCHAR(11) NOT NULL DEFAULT 'None',
  `origin` VARCHAR(50) NOT NULL DEFAULT 'None',
  `card_uid` VARCHAR(30) NOT NULL,
  `card_select` TINYINT(1) NOT NULL DEFAULT '0',
  `good_date` DATE NOT NULL,
  `exp_date` VARCHAR(10),
  `device_uid` VARCHAR(20) NOT NULL DEFAULT '0',
  `device_dep` VARCHAR(20) NOT NULL DEFAULT '0',
  `add_card` TINYINT(1) NOT NULL DEFAULT '0',
  `status` VARCHAR(50) NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users_logs`
--

CREATE TABLE `goods_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `good` VARCHAR(100) NOT NULL,
  `serialnumber` VARCHAR(255) NOT NULL,
  `card_uid` VARCHAR(30) NOT NULL,
  `device_uid` VARCHAR(20) NOT NULL,
  `device_dep` VARCHAR(20) NOT NULL,
  `checkindate` DATE NOT NULL,
  `timein` TIME NOT NULL,
  `timeout` TIME NOT NULL DEFAULT '00:00:00',
  `card_out` TINYINT(1) NOT NULL DEFAULT 0,
  `status` VARCHAR(50) NOT NULL DEFAULT 'check in'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

