-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 03, 2014 at 06:40 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `juicydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `champions_lol`
--

CREATE TABLE IF NOT EXISTS `champions_lol` (
  `championid` int(10) unsigned NOT NULL,
  `rankedPlayEnabled` tinyint(1) NOT NULL,
  `name` varchar(16) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `freeToPlay` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`cid`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`cid`, `name`) VALUES
(1, 'Canada'),
(2, 'United States');

-- --------------------------------------------------------

--
-- Table structure for table `esports`
--

CREATE TABLE IF NOT EXISTS `esports` (
  `esportid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `abbrv` varchar(8) NOT NULL,
  `type` varchar(16) NOT NULL,
  `description` varchar(128) NOT NULL,
  `imageurl` varchar(64) NOT NULL,
  PRIMARY KEY (`esportid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `esports`
--

INSERT INTO `esports` (`esportid`, `name`, `abbrv`, `type`, `description`, `imageurl`) VALUES
(1, 'League of Legends', 'LoL', 'MOBA', 'is a fast-paced, Multiplayer Online Battle Arena (MOBA) competitive game.', 'http://tinyurl.com/6hjx6pl'),
(2, 'Counterstrike: Global Offensive', 'CSGO', 'FPS', 'is a competitive first person shooter where two opposing forces battle with precision.', 'http://tinyurl.com/bwyurf5');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `gameid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `esportid` int(10) unsigned NOT NULL,
  `teamaid` int(11) unsigned NOT NULL,
  `teambid` int(11) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `winnerid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`gameid`),
  UNIQUE KEY `winnerid` (`winnerid`),
  KEY `esportid` (`esportid`),
  KEY `teamaid` (`teamaid`),
  KEY `teambid` (`teambid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ip_log`
--

CREATE TABLE IF NOT EXISTS `ip_log` (
  `ip` mediumint(8) unsigned NOT NULL,
  `logged` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `locationsupport`
--

CREATE TABLE IF NOT EXISTS `locationsupport` (
  `countryid` int(10) unsigned NOT NULL,
  `provincestateid` int(10) unsigned NOT NULL,
  `regionid` int(10) unsigned NOT NULL,
  KEY `countryid` (`countryid`,`provincestateid`,`regionid`),
  KEY `provincestateid` (`provincestateid`),
  KEY `regionid` (`regionid`),
  KEY `countryid_2` (`countryid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `locationsupport`
--

INSERT INTO `locationsupport` (`countryid`, `provincestateid`, `regionid`) VALUES
(1, 68, 6);

-- --------------------------------------------------------

--
-- Table structure for table `pendingaccounts`
--

CREATE TABLE IF NOT EXISTS `pendingaccounts` (
  `uid` int(10) unsigned NOT NULL,
  `email` varchar(64) NOT NULL,
  `validationkey` varchar(32) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `email` (`email`,`validationkey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pendingaccounts`
--

INSERT INTO `pendingaccounts` (`uid`, `email`, `validationkey`) VALUES
(23, 'Email@Test.Com', 'FVGYZmMDv6NbQf8f'),
(24, 'hebert.vincent@hotmail.ca', 'wo8fKHgXZBHasPrQ'),
(21, 'john@doe.com', 'VB1GOOnnvNpWfqRu'),
(22, 'p@p.com', 'ITJOlT6HAS7VxVYu'),
(20, 'philipblaquiere@me.com', 'eG0ALq4Q0GPh0l4y');

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE IF NOT EXISTS `region` (
  `regionid` int(11) unsigned NOT NULL DEFAULT '0',
  `provincestateid` int(11) unsigned DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`regionid`),
  KEY `provincestateid` (`provincestateid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `region`
--

INSERT INTO `region` (`regionid`, `provincestateid`, `name`) VALUES
(1, 68, 'Bas-Saint-Laurent'),
(2, 68, 'Saguenay–Lac-Saint-Jean'),
(3, 68, 'Capitale-Nationale'),
(4, 68, 'Mauricie'),
(5, 68, 'Estrie'),
(6, 68, 'Montréal'),
(7, 68, 'Outaouais'),
(8, 68, 'Abitibi-Témiscamingue'),
(9, 68, 'Côte-Nord'),
(10, 68, 'Nord-du-Québec'),
(11, 68, 'Gaspésie–Îles-de-la-Madeleine'),
(12, 68, 'Chaudière-Appalaches'),
(13, 68, 'Laval'),
(14, 68, 'Lanaudière'),
(15, 68, 'Laurentide'),
(16, 68, 'Montérégie'),
(17, 68, 'Centre-du-Québec');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE IF NOT EXISTS `state` (
  `provincestateid` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `abbreviation` varchar(255) DEFAULT NULL,
  `countryid` int(11) DEFAULT NULL,
  PRIMARY KEY (`provincestateid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`provincestateid`, `name`, `abbreviation`, `countryid`) VALUES
(1, 'Alabama', 'AL', 2),
(2, 'Alaska', 'AK', 2),
(3, 'Arizona', 'AZ', 2),
(4, 'Arkansas', 'AR', 2),
(5, 'California', 'CA', 2),
(6, 'Colorado', 'CO', 2),
(7, 'Connecticut', 'CT', 2),
(8, 'Delaware', 'DE', 2),
(9, 'Florida', 'FL', 2),
(10, 'Georgia', 'GA', 2),
(11, 'Hawaii', 'HI', 2),
(12, 'Idaho', 'ID', 2),
(13, 'Illinois', 'IL', 2),
(14, 'Indiana', 'IN', 2),
(15, 'Iowa', 'IA', 2),
(16, 'Kansas', 'KS', 2),
(17, 'Kentucky', 'KY', 2),
(18, 'Louisiana', 'LA', 2),
(19, 'Maine', 'ME', 2),
(20, 'Maryland', 'MD', 2),
(21, 'Massachusetts', 'MA', 2),
(22, 'Michigan', 'MI', 2),
(23, 'Minnesota', 'MN', 2),
(24, 'Mississippi', 'MS', 2),
(25, 'Missouri', 'MO', 2),
(26, 'Montana', 'MT', 2),
(27, 'Nebraska', 'NE', 2),
(28, 'Nevada', 'NV', 2),
(29, 'New Hampshire', 'NH', 2),
(30, 'New Jersey', 'NJ', 2),
(31, 'New Mexico', 'NM', 2),
(32, 'New York', 'NY', 2),
(33, 'North Carolina', 'NC', 2),
(34, 'North Dakota', 'ND', 2),
(35, 'Ohio', 'OH', 2),
(36, 'Oklahoma', 'OK', 2),
(37, 'Oregon', 'OR', 2),
(38, 'Pennsylvania', 'PA', 2),
(39, 'Rhode Island', 'RI', 2),
(40, 'South Carolina', 'SC', 2),
(41, 'South Dakota', 'SD', 2),
(42, 'Tennessee', 'TN', 2),
(43, 'Texas', 'TX', 2),
(44, 'Utah', 'UT', 2),
(45, 'Vermont', 'VT', 2),
(46, 'Virginia', 'VA', 2),
(47, 'Washington', 'WA', 2),
(48, 'West Virginia', 'WV', 2),
(49, 'Wisconsin', 'WI', 2),
(50, 'Wyoming', 'WY', 2),
(51, 'Washington DC', 'DC', 2),
(52, 'Puerto Rico', 'PR', 2),
(53, 'U.S. Virgin Islands', 'VI', 2),
(54, 'American Samoa', 'AS', 2),
(55, 'Guam', 'GU', 2),
(56, 'Northern Mariana Islands', 'MP', 2),
(60, 'Alberta ', 'AB', 1),
(61, 'British Columbia ', 'BC', 1),
(62, 'Manitoba ', 'MB', 1),
(63, 'New Brunswick ', 'NB', 1),
(64, 'Newfoundland and Labrador ', 'NL', 1),
(65, 'Nova Scotia ', 'NS', 1),
(66, 'Ontario ', 'ON', 1),
(67, 'Prince Edward Island ', 'PE', 1),
(68, 'Quebec ', 'QC', 1),
(69, 'Saskatchewan ', 'SK', 1),
(70, 'Northwest Territories ', 'NT', 1),
(71, 'Nunavut ', 'NU', 1),
(72, 'Yukon Territory ', 'YT', 1);

-- --------------------------------------------------------

--
-- Table structure for table `statistics_lol`
--

CREATE TABLE IF NOT EXISTS `statistics_lol` (
  `gameid` int(10) unsigned NOT NULL,
  `gametype` varchar(16) NOT NULL,
  `summonerid` int(11) NOT NULL,
  `spell1` smallint(5) unsigned NOT NULL,
  `teamid` smallint(5) unsigned NOT NULL,
  `level` smallint(5) unsigned NOT NULL,
  `gold_earned` int(10) unsigned NOT NULL,
  `num_deaths` smallint(5) unsigned NOT NULL,
  `minions_killed` smallint(5) unsigned NOT NULL,
  `champions_killed` smallint(5) unsigned NOT NULL,
  `total_damage_dealt` int(10) unsigned NOT NULL,
  `total_damage_taken` int(10) unsigned NOT NULL,
  `double_kills` smallint(5) unsigned NOT NULL,
  `triple_kills` smallint(5) unsigned NOT NULL,
  `quadra_kills` smallint(5) unsigned NOT NULL,
  `penta_kills` smallint(5) unsigned NOT NULL,
  `lose` smallint(5) unsigned NOT NULL,
  `item0` int(10) unsigned NOT NULL,
  `item1` int(10) unsigned NOT NULL,
  `item2` int(10) unsigned NOT NULL,
  `item3` int(10) unsigned NOT NULL,
  `item4` int(10) unsigned NOT NULL,
  `item5` int(10) unsigned NOT NULL,
  `ward_placed` smallint(5) unsigned NOT NULL,
  `assists` smallint(5) unsigned NOT NULL,
  `spell2` int(10) unsigned NOT NULL,
  `championid` int(10) unsigned NOT NULL,
  `createdate` int(10) unsigned NOT NULL,
  `gamemode` varchar(16) NOT NULL,
  PRIMARY KEY (`gameid`),
  KEY `summonerid` (`summonerid`,`spell1`,`teamid`,`item0`,`item1`,`item2`,`item3`,`item4`,`item5`,`spell2`,`championid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `summoners`
--

CREATE TABLE IF NOT EXISTS `summoners` (
  `UserId` int(10) unsigned NOT NULL,
  `SummonerId` int(11) unsigned NOT NULL,
  `SummonerName` varchar(16) NOT NULL,
  `ProfileIconId` int(10) unsigned NOT NULL,
  `RevisionDate` int(10) unsigned NOT NULL,
  `SummonerLevel` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SummonerId`),
  UNIQUE KEY `SummonerId` (`SummonerId`,`SummonerName`),
  KEY `UserId` (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `summoners`
--

INSERT INTO `summoners` (`UserId`, `SummonerId`, `SummonerName`, `ProfileIconId`, `RevisionDate`, `SummonerLevel`, `created`) VALUES
(1, 29208894, 'seejimmyrun', 579, 4294967295, 30, '2013-12-23 19:30:21'),
(20, 32274301, 'runjimmysee', 31, 4294967295, 16, '2013-12-23 23:45:01');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE IF NOT EXISTS `teams` (
  `teamid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `esportid` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `captainid` int(10) unsigned NOT NULL,
  `countryid` int(10) unsigned NOT NULL,
  `stateid` int(10) unsigned NOT NULL,
  `regionid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`teamid`),
  UNIQUE KEY `name` (`name`),
  KEY `leaderid` (`captainid`,`countryid`,`stateid`,`regionid`),
  KEY `esportid` (`esportid`),
  KEY `name_2` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`teamid`, `name`, `esportid`, `created`, `captainid`, `countryid`, `stateid`, `regionid`) VALUES
(2, 'test', 1, '2013-12-24 03:46:25', 22, 1, 68, 6),
(3, 'test2', 1, '2013-12-24 03:46:49', 22, 1, 68, 6),
(12, 'Montreal Boyz', 1, '2013-12-30 18:05:34', 1, 1, 68, 6),
(13, 'Big Green', 1, '2013-12-31 21:14:43', 20, 1, 68, 6);

-- --------------------------------------------------------

--
-- Table structure for table `teams_lol`
--

CREATE TABLE IF NOT EXISTS `teams_lol` (
  `teamid` int(10) unsigned NOT NULL,
  `summonerid` int(10) unsigned DEFAULT NULL,
  `joined_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`teamid`),
  KEY `summonerid` (`summonerid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `teams_lol`
--

INSERT INTO `teams_lol` (`teamid`, `summonerid`, `joined_date`) VALUES
(12, 29208894, '2013-12-30 18:05:34'),
(13, 32274301, '2013-12-31 21:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `team_invite_lol`
--

CREATE TABLE IF NOT EXISTS `team_invite_lol` (
  `teamid` int(10) unsigned NOT NULL,
  `summonerid` int(10) unsigned NOT NULL,
  `message` varchar(140) NOT NULL,
  `invite_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(16) NOT NULL DEFAULT 'new',
  KEY `teamid` (`teamid`,`summonerid`),
  KEY `summonerid` (`summonerid`),
  KEY `read` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `team_invite_lol`
--

INSERT INTO `team_invite_lol` (`teamid`, `summonerid`, `message`, `invite_date`, `status`) VALUES
(12, 32274301, 'test23', '2013-12-31 20:44:57', 'new'),
(12, 32274301, 'Hey I saw you play a game the other day, awesome job on the the kill', '2013-12-31 20:58:33', 'new');

-- --------------------------------------------------------

--
-- Table structure for table `team_old_names`
--

CREATE TABLE IF NOT EXISTS `team_old_names` (
  `teamid` int(10) unsigned NOT NULL,
  `name` int(11) NOT NULL,
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `teamid` (`teamid`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trades_lol`
--

CREATE TABLE IF NOT EXISTS `trades_lol` (
  `teamaid` int(10) unsigned NOT NULL,
  `summoneraid` int(10) unsigned NOT NULL,
  `teambid` int(10) unsigned NOT NULL,
  `summonerbid` int(10) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(16) NOT NULL DEFAULT 'pending',
  KEY `teamaid` (`teamaid`,`summoneraid`,`teambid`,`summonerbid`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `UserId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `password` varchar(64) NOT NULL,
  `salt` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `regionid` int(10) unsigned NOT NULL,
  `provincestateid` int(10) unsigned NOT NULL,
  `countryid` int(10) unsigned NOT NULL,
  `registertime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `validated` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`UserId`),
  UNIQUE KEY `email` (`email`),
  KEY `cityid` (`regionid`,`provincestateid`,`countryid`),
  KEY `provincestateid` (`provincestateid`),
  KEY `countryid` (`countryid`),
  KEY `countryid_2` (`countryid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserId`, `password`, `salt`, `email`, `firstname`, `lastname`, `regionid`, `provincestateid`, `countryid`, `registertime`, `last_login_time`, `validated`) VALUES
(1, 'dbece9200b7f05d9b8b08475a2fb50ed9f8b6a20', '45TX+7', 'philipblaquiere@gmail.com', 'Philip', 'Blaquiere', 6, 68, 1, '2014-01-02 23:45:08', '2014-01-02 23:45:08', 1),
(4, 'ca9afdc04043d6017bc36f65d44c8cd411b881d1', 'm5nhiv', 'test@test.com', 'Test', 'Test', 6, 68, 1, '2013-12-15 19:32:16', '0000-00-00 00:00:00', 0),
(5, '5174024ef2d8c06890fa6f0c9de7692fc8cc805e', 'n6fkaC', 'test@test1.com', 'Test', 'Test', 6, 68, 1, '2013-12-15 19:33:16', '0000-00-00 00:00:00', 0),
(6, 'f711431496ba13a7f8cdbc374adb6dd6216f7065', 'QuDeEq', 'test3@test3.com', 'test', 'test', 6, 68, 1, '2013-12-15 19:34:28', '0000-00-00 00:00:00', 0),
(20, 'cc847d314b11ed1529978a4097e2757293d676e7', '4RX2RY', 'philipblaquiere@me.com', 'Philip', 'Blaquiere', 6, 68, 1, '2014-01-03 05:54:42', '2014-01-03 05:54:42', 1),
(21, '4da65974c0cc1def39fc07fe5ec5c5ad10f28780', 'JP~bk9', 'john@doe.com', 'John', 'Doe', 6, 68, 1, '2013-12-18 21:22:16', '2013-12-18 21:22:16', 1),
(22, 'a907c7ef55500a09c29ec1249a8355bfcec385bb', '*uy3%a', 'p@p.com', 'Philip', 'Blaquiere', 6, 68, 1, '2013-12-30 20:34:20', '2013-12-30 20:34:20', 1),
(23, 'cfbf6939df59f6a4f9f3857b42e5dc57a58cfb1d', '3!Qgq+', 'Email@Test.Com', 'Email', 'Test', 6, 68, 1, '2013-12-24 14:38:38', '0000-00-00 00:00:00', 0),
(24, 'fb4335ea76a6729728eb016e36e769aaadc30996', 'rmqY+7', 'hebert.vincent@hotmail.ca', 'Vincent', 'Hebert', 6, 68, 1, '2013-12-25 20:54:21', '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_esport`
--

CREATE TABLE IF NOT EXISTS `user_esport` (
  `UserId` int(10) unsigned NOT NULL,
  `esportid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`UserId`),
  KEY `esportid` (`esportid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_esport`
--

INSERT INTO `user_esport` (`UserId`, `esportid`) VALUES
(1, 1),
(20, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE IF NOT EXISTS `user_roles` (
  `uid` int(10) unsigned NOT NULL,
  `rid` smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`),
  KEY `rid` (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='1 == user, 4 == admin';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`esportid`) REFERENCES `esports` (`esportid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `games_ibfk_2` FOREIGN KEY (`winnerid`) REFERENCES `teams` (`teamid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `games_ibfk_3` FOREIGN KEY (`teamaid`) REFERENCES `teams` (`teamid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `games_ibfk_4` FOREIGN KEY (`teambid`) REFERENCES `teams` (`teamid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `locationsupport`
--
ALTER TABLE `locationsupport`
  ADD CONSTRAINT `locationsupport_ibfk_1` FOREIGN KEY (`countryid`) REFERENCES `countries` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `locationsupport_ibfk_2` FOREIGN KEY (`provincestateid`) REFERENCES `state` (`provincestateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `locationsupport_ibfk_3` FOREIGN KEY (`regionid`) REFERENCES `region` (`regionid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pendingaccounts`
--
ALTER TABLE `pendingaccounts`
  ADD CONSTRAINT `pendingaccounts_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`UserId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `region`
--
ALTER TABLE `region`
  ADD CONSTRAINT `region_ibfk_1` FOREIGN KEY (`provincestateid`) REFERENCES `state` (`provincestateid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `summoners`
--
ALTER TABLE `summoners`
  ADD CONSTRAINT `summoners_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`esportid`) REFERENCES `esports` (`esportid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `team_invite_lol`
--
ALTER TABLE `team_invite_lol`
  ADD CONSTRAINT `team_invite_lol_ibfk_2` FOREIGN KEY (`teamid`) REFERENCES `teams` (`teamid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `team_invite_lol_ibfk_3` FOREIGN KEY (`summonerid`) REFERENCES `summoners` (`SummonerId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `team_old_names`
--
ALTER TABLE `team_old_names`
  ADD CONSTRAINT `team_old_names_ibfk_1` FOREIGN KEY (`teamid`) REFERENCES `teams` (`teamid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`regionid`) REFERENCES `region` (`regionid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`provincestateid`) REFERENCES `state` (`provincestateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`countryid`) REFERENCES `countries` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`UserId`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
