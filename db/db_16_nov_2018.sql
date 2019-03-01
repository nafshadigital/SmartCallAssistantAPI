/*
SQLyog Enterprise - MySQL GUI v7.02 
MySQL - 5.6.24 : Database - smartcallassistant
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `tbl_admin_email_ids` */

CREATE TABLE `tbl_admin_email_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_email_ids` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `tbl_admin_email_ids` */

insert  into `tbl_admin_email_ids`(`id`,`admin_email_ids`) values (1,'test@gmail.com, kandasamy.malaris@gmail.com');

/*Table structure for table `tbl_country` */

CREATE TABLE `tbl_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(4) DEFAULT NULL,
  `country` varchar(75) DEFAULT NULL,
  `is_active` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `tbl_country` */

insert  into `tbl_country`(`id`,`country_code`,`country`,`is_active`) values (1,'+91','India',1),(2,'+1','United States',1);

/*Table structure for table `tbl_support` */

CREATE TABLE `tbl_support` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `message` text,
  `msg_date` datetime DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `tbl_support` */

insert  into `tbl_support`(`id`,`user_id`,`message`,`msg_date`,`status`) values (1,17,'this is test for small call rest handler','2018-11-16 11:18:23',0),(2,17,'this is test for small call rest handler','2018-11-16 11:19:05',0),(3,17,'1this is test for small call rest handler1','2018-11-16 11:19:27',0),(4,16,'1this is test for small call rest handler1','2018-11-16 11:19:36',0),(5,16,'','2018-11-16 11:20:54',0);

/*Table structure for table `tbl_users` */

CREATE TABLE `tbl_users` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `country_code` varchar(4) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `wallet_balance` decimal(11,2) DEFAULT '0.00',
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

/*Data for the table `tbl_users` */

insert  into `tbl_users`(`id`,`name`,`email`,`country_code`,`mobile`,`dob`,`created_date`,`modified_date`,`wallet_balance`,`is_active`) values (1,'raja','raja@gmail.com',NULL,NULL,'1993-03-19','2018-09-26 08:54:45',NULL,'0.00',1),(2,'ganesh','ganesh@gmail.com',NULL,NULL,'1994-05-13','2018-09-28 04:52:28',NULL,'0.00',1),(3,'imman','imman@gmail.com',NULL,NULL,'1992-12-17','2018-09-28 04:53:29',NULL,'0.00',1),(4,'kumar','kumar@gmail.com',NULL,NULL,'1995-08-12','2018-09-28 04:54:30',NULL,'0.00',1),(10,'suresh','suresh@gmail.com',NULL,NULL,'1993-02-15','2018-09-29 09:26:26',NULL,'20.00',1),(11,'uty','ut',NULL,NULL,'0000-00-00','2018-09-29 10:01:24',NULL,'0.00',1),(12,'gdfg','gdfg',NULL,NULL,'0000-00-00','2018-09-29 11:59:07',NULL,'0.00',1),(13,'kandasamy','kandasamy.malaris@gmail.com',NULL,NULL,'1994-05-13','2018-10-11 15:09:42',NULL,'0.00',1),(14,NULL,NULL,'+81','9632587410',NULL,'2018-11-16 00:00:00',NULL,'0.00',1),(15,NULL,NULL,'+81','9632587410',NULL,'2018-11-16 00:00:00',NULL,'0.00',1),(16,NULL,NULL,'+81','9632587410',NULL,'2018-11-16 00:00:00',NULL,'0.00',1),(17,'kannan','kannan@gmail.com','+81','9632587410',NULL,'2018-11-16 00:00:00',NULL,'0.00',1),(18,'ganesh','ganesh@gmail.com','+81','9632587410',NULL,'2018-11-16 00:00:00',NULL,'0.00',1),(19,NULL,NULL,'','9632587410',NULL,'2018-11-16 00:00:00',NULL,'0.00',1),(21,NULL,NULL,'+91','1234567890',NULL,'2018-11-16 00:00:00',NULL,'0.00',1);

/*Table structure for table `tbl_verification_code` */

CREATE TABLE `tbl_verification_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `verification_code` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `status` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/*Data for the table `tbl_verification_code` */

insert  into `tbl_verification_code`(`id`,`user_id`,`verification_code`,`created_date`,`status`) values (1,0,102910,NULL,1),(2,13,101441,NULL,0),(3,14,108567,'2018-11-16 11:29:31',1),(4,15,108091,'2018-11-16 09:54:06',0),(5,16,105906,'2018-11-16 09:55:13',0),(6,17,106956,'2018-11-16 09:55:23',1),(7,18,103850,'2018-11-16 11:13:10',1),(8,19,101513,'2018-11-16 11:26:19',1),(9,20,101534,'2018-11-16 11:27:30',1),(10,21,100914,'2018-11-16 11:28:36',1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;