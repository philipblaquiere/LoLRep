-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 04, 2014 at 12:39 AM
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
-- Table structure for table `banned_summoners`
--

CREATE TABLE IF NOT EXISTS `banned_summoners` (
  `email` varchar(36) NOT NULL,
  `SummonerName` varchar(16) NOT NULL,
  `summonerid` int(10) unsigned NOT NULL,
  `ban_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reason` varchar(256) NOT NULL,
  KEY `UserId` (`email`,`summonerid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

--
-- Dumping data for table `champions_lol`
--

INSERT INTO `champions_lol` (`championid`, `rankedPlayEnabled`, `name`, `active`, `freeToPlay`) VALUES
(266, 1, 'Aatrox', 1, 0),
(103, 1, 'Ahri', 1, 0),
(84, 1, 'Akali', 1, 1),
(12, 1, 'Alistar', 1, 0),
(32, 1, 'Amumu', 1, 0),
(34, 1, 'Anivia', 1, 0),
(1, 1, 'Annie', 1, 0),
(22, 1, 'Ashe', 1, 0),
(53, 1, 'Blitzcrank', 1, 0),
(63, 1, 'Brand', 1, 0),
(51, 1, 'Caitlyn', 1, 0),
(69, 1, 'Cassiopeia', 1, 0),
(31, 1, 'Chogath', 1, 0),
(42, 1, 'Corki', 1, 0),
(122, 1, 'Darius', 1, 0),
(131, 1, 'Diana', 1, 1),
(119, 1, 'Draven', 1, 0),
(36, 1, 'DrMundo', 1, 0),
(60, 1, 'Elise', 1, 0),
(28, 1, 'Evelynn', 1, 0),
(81, 1, 'Ezreal', 1, 0),
(9, 1, 'FiddleSticks', 1, 0),
(114, 1, 'Fiora', 1, 0),
(105, 1, 'Fizz', 1, 0),
(3, 1, 'Galio', 1, 0),
(41, 1, 'Gangplank', 1, 0),
(86, 1, 'Garen', 1, 1),
(79, 1, 'Gragas', 1, 0),
(104, 1, 'Graves', 1, 0),
(120, 1, 'Hecarim', 1, 0),
(74, 1, 'Heimerdinger', 1, 0),
(39, 1, 'Irelia', 1, 0),
(40, 1, 'Janna', 1, 1),
(59, 1, 'JarvanIV', 1, 0),
(24, 1, 'Jax', 1, 0),
(126, 1, 'Jayce', 1, 0),
(222, 1, 'Jinx', 1, 0),
(43, 1, 'Karma', 1, 0),
(30, 1, 'Karthus', 1, 0),
(38, 1, 'Kassadin', 1, 0),
(55, 1, 'Katarina', 1, 0),
(10, 1, 'Kayle', 1, 0),
(85, 1, 'Kennen', 1, 0),
(121, 1, 'Khazix', 1, 0),
(96, 1, 'KogMaw', 1, 0),
(7, 1, 'Leblanc', 1, 0),
(64, 1, 'LeeSin', 1, 1),
(89, 1, 'Leona', 1, 1),
(127, 1, 'Lissandra', 1, 0),
(236, 1, 'Lucian', 1, 0),
(117, 1, 'Lulu', 1, 0),
(99, 1, 'Lux', 1, 0),
(54, 1, 'Malphite', 1, 0),
(90, 1, 'Malzahar', 1, 0),
(57, 1, 'Maokai', 1, 0),
(11, 1, 'MasterYi', 1, 0),
(21, 1, 'MissFortune', 1, 0),
(62, 1, 'MonkeyKing', 1, 0),
(82, 1, 'Mordekaiser', 1, 0),
(25, 1, 'Morgana', 1, 0),
(267, 1, 'Nami', 1, 0),
(75, 1, 'Nasus', 1, 0),
(111, 1, 'Nautilus', 1, 0),
(76, 1, 'Nidalee', 1, 0),
(56, 1, 'Nocturne', 1, 0),
(20, 1, 'Nunu', 1, 0),
(2, 1, 'Olaf', 1, 0),
(61, 1, 'Orianna', 1, 0),
(80, 1, 'Pantheon', 1, 0),
(78, 1, 'Poppy', 1, 0),
(133, 1, 'Quinn', 1, 0),
(33, 1, 'Rammus', 1, 0),
(58, 1, 'Renekton', 1, 0),
(107, 1, 'Rengar', 1, 0),
(92, 1, 'Riven', 1, 0),
(68, 1, 'Rumble', 1, 0),
(13, 1, 'Ryze', 1, 1),
(113, 1, 'Sejuani', 1, 0),
(35, 1, 'Shaco', 1, 0),
(98, 1, 'Shen', 1, 0),
(102, 1, 'Shyvana', 1, 0),
(27, 1, 'Singed', 1, 0),
(14, 1, 'Sion', 1, 0),
(15, 1, 'Sivir', 1, 0),
(72, 1, 'Skarner', 1, 0),
(37, 1, 'Sona', 1, 0),
(16, 1, 'Soraka', 1, 0),
(50, 1, 'Swain', 1, 0),
(134, 1, 'Syndra', 1, 0),
(91, 1, 'Talon', 1, 0),
(44, 1, 'Taric', 1, 0),
(17, 1, 'Teemo', 1, 1),
(412, 1, 'Thresh', 1, 0),
(18, 1, 'Tristana', 1, 0),
(48, 1, 'Trundle', 1, 0),
(23, 1, 'Tryndamere', 1, 0),
(4, 1, 'TwistedFate', 1, 0),
(29, 1, 'Twitch', 1, 0),
(77, 1, 'Udyr', 1, 0),
(6, 1, 'Urgot', 1, 0),
(110, 1, 'Varus', 1, 1),
(67, 1, 'Vayne', 1, 1),
(45, 1, 'Veigar', 1, 0),
(254, 1, 'Vi', 1, 0),
(112, 1, 'Viktor', 1, 0),
(8, 1, 'Vladimir', 1, 0),
(106, 1, 'Volibear', 1, 0),
(19, 1, 'Warwick', 1, 0),
(101, 1, 'Xerath', 1, 0),
(5, 1, 'XinZhao', 1, 0),
(157, 1, 'Yasuo', 1, 0),
(83, 1, 'Yorick', 1, 0),
(154, 1, 'Zac', 1, 0),
(238, 1, 'Zed', 1, 0),
(115, 1, 'Ziggs', 1, 0),
(26, 1, 'Zilean', 1, 0),
(143, 1, 'Zyra', 1, 0);

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
  `gameid` varchar(36) NOT NULL,
  `esportid` int(10) unsigned NOT NULL,
  `teamaid` varchar(36) NOT NULL,
  `teambid` varchar(36) NOT NULL,
  `date` datetime NOT NULL,
  `winnerid` int(10) unsigned NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'scheduled',
  PRIMARY KEY (`gameid`),
  UNIQUE KEY `winnerid` (`winnerid`),
  KEY `esportid` (`esportid`),
  KEY `teamaid` (`teamaid`),
  KEY `teambid` (`teambid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `uid` varchar(36) NOT NULL,
  `email` varchar(64) NOT NULL,
  `validationkey` varchar(32) NOT NULL,
  PRIMARY KEY (`uid`),
  KEY `email` (`email`,`validationkey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pendingaccounts`
--

INSERT INTO `pendingaccounts` (`uid`, `email`, `validationkey`) VALUES
('1b1ea6f8-e4f8-55fe-b7a0-0a0558fc4499', 'p@p.com', 'N9gWfX6waPNN1i9X'),
('75dc3008-3a9d-56a5-b708-d3b84487f3da', 'philipblaquiere@gmail.com', 'jbrU2dtyXs9wekhb'),
('7512b30f-2f1b-587d-85f9-aa36fc0c680e', 'philipblaquiere@me.com', 'QGobuP0x4j1AxREB');

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
  `gameid` varchar(36) NOT NULL,
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
  `UserId` varchar(36) NOT NULL,
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
('75dc3008-3a9d-56a5-b708-d3b84487f3da', 29208894, 'seejimmyrun', 589, 4294967295, 30, '2014-01-03 17:54:45'),
('7512b30f-2f1b-587d-85f9-aa36fc0c680e', 32274301, 'runjimmysee', 31, 4294967295, 16, '2014-01-03 18:03:08'),
('1b1ea6f8-e4f8-55fe-b7a0-0a0558fc4499', 49000727, 'Voracik', 23, 4294967295, 5, '2014-01-03 18:06:31');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE IF NOT EXISTS `teams` (
  `teamid` varchar(36) NOT NULL,
  `name` varchar(32) NOT NULL,
  `esportid` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `captainid` varchar(36) NOT NULL,
  `countryid` int(11) NOT NULL,
  `stateid` int(11) NOT NULL,
  `regionid` int(11) NOT NULL,
  PRIMARY KEY (`teamid`),
  UNIQUE KEY `teamid` (`teamid`,`name`),
  KEY `captainid` (`captainid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`teamid`, `name`, `esportid`, `created`, `captainid`, `countryid`, `stateid`, `regionid`) VALUES
('889f98e0-929b-5287-9df4-5fcf97fab286', 'Big Green', 1, '2014-01-03 18:42:46', '7512b30f-2f1b-587d-85f9-aa36fc0c680e', 7512, 68, 6),
('9429107c-68ee-5df2-bb91-2c3ecaba5c0b', 'Montreal Boys', 1, '2014-01-03 18:18:48', '75dc3008-3a9d-56a5-b708-d3b84487f3da', 75, 68, 6);

-- --------------------------------------------------------

--
-- Table structure for table `teams_lol`
--

CREATE TABLE IF NOT EXISTS `teams_lol` (
  `teamid` varchar(36) NOT NULL,
  `summonerid` int(10) unsigned DEFAULT NULL,
  `joined_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(16) NOT NULL DEFAULT 'active',
  `leave_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `summonerid` (`summonerid`),
  KEY `teamid` (`teamid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `teams_lol`
--

INSERT INTO `teams_lol` (`teamid`, `summonerid`, `joined_date`, `status`, `leave_date`) VALUES
('9429107c-68ee-5df2-bb91-2c3ecaba5c0b', 29208894, '2014-01-03 18:18:48', 'active', '0000-00-00 00:00:00'),
('889f98e0-929b-5287-9df4-5fcf97fab286', 32274301, '2014-01-03 18:42:46', 'active', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `team_invite_lol`
--

CREATE TABLE IF NOT EXISTS `team_invite_lol` (
  `inviteid` varchar(36) NOT NULL,
  `teamid` varchar(36) NOT NULL,
  `summonerid` int(10) unsigned NOT NULL,
  `message` varchar(140) NOT NULL,
  `invite_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(8) NOT NULL DEFAULT 'new',
  PRIMARY KEY (`inviteid`),
  KEY `teamid` (`teamid`,`summonerid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `team_invite_lol`
--

INSERT INTO `team_invite_lol` (`inviteid`, `teamid`, `summonerid`, `message`, `invite_date`, `status`) VALUES
('1a31f2e2-1d40-5dd5-a0f4-d61332cbe44a', '889f98e0-929b-5287-9df4-5fcf97fab286', 49000727, 'test', '2014-01-03 18:47:01', 'accepted'),
('518528f6-bc94-5637-b1d7-438a9b538b42', '9429107c-68ee-5df2-bb91-2c3ecaba5c0b', 49000727, 'test', '2014-01-03 18:53:24', 'declined');

-- --------------------------------------------------------

--
-- Table structure for table `team_old_names`
--

CREATE TABLE IF NOT EXISTS `team_old_names` (
  `teamid` varchar(36) NOT NULL,
  `name` varchar(32) NOT NULL,
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `teamid` (`teamid`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trades_lol`
--

CREATE TABLE IF NOT EXISTS `trades_lol` (
  `teamaid` varchar(36) NOT NULL,
  `summoneraid` int(10) unsigned NOT NULL,
  `teambid` varchar(36) NOT NULL,
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
  `UserId` varchar(36) NOT NULL,
  `password` varchar(64) NOT NULL,
  `salt` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `regionid` int(10) unsigned NOT NULL,
  `provincestateid` int(10) unsigned NOT NULL,
  `countryid` int(10) unsigned NOT NULL,
  `registertime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `validated` tinyint(4) NOT NULL DEFAULT '0',
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `UserId` (`UserId`),
  KEY `cityid` (`regionid`,`provincestateid`,`countryid`),
  KEY `provincestateid` (`provincestateid`),
  KEY `countryid` (`countryid`),
  KEY `countryid_2` (`countryid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserId`, `password`, `salt`, `email`, `firstname`, `lastname`, `regionid`, `provincestateid`, `countryid`, `registertime`, `last_login_time`, `validated`) VALUES
('1b1ea6f8-e4f8-55fe-b7a0-0a0558fc4499', '27cb51fd5f840f47e6694d24445a0561d0350c9b', 'OMa)Ye', 'p@p.com', 'Philip', 'B', 6, 68, 1, '2014-01-03 18:53:35', '2014-01-03 18:53:35', 1),
('75dc3008-3a9d-56a5-b708-d3b84487f3da', 'b04513bfb74fbf367b887aa6620228d56521b246', 'jUnbQT', 'philipblaquiere@gmail.com', 'Philip', 'Blaquiere', 6, 68, 1, '2014-01-03 20:11:12', '2014-01-03 23:32:06', 1),
('7512b30f-2f1b-587d-85f9-aa36fc0c680e', '68f6eeddb11c7cd200a9160e9ddba87f8c1c0660', 'sZLpb~', 'philipblaquiere@me.com', 'Philip', 'Blaquiere', 6, 68, 1, '2014-01-03 18:46:44', '2014-01-03 20:15:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_esport`
--

CREATE TABLE IF NOT EXISTS `user_esport` (
  `UserId` varchar(36) NOT NULL,
  `esportid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`UserId`),
  KEY `esportid` (`esportid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_esport`
--

INSERT INTO `user_esport` (`UserId`, `esportid`) VALUES
('1b1ea6f8-e4f8-55fe-b7a0-0a0558fc4499', 1),
('7512b30f-2f1b-587d-85f9-aa36fc0c680e', 1),
('75dc3008-3a9d-56a5-b708-d3b84487f3da', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE IF NOT EXISTS `user_roles` (
  `uid` varchar(36) NOT NULL,
  `rid` smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`),
  KEY `rid` (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='1 == user, 4 == admin';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `locationsupport`
--
ALTER TABLE `locationsupport`
  ADD CONSTRAINT `locationsupport_ibfk_1` FOREIGN KEY (`countryid`) REFERENCES `countries` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `locationsupport_ibfk_2` FOREIGN KEY (`provincestateid`) REFERENCES `state` (`provincestateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `locationsupport_ibfk_3` FOREIGN KEY (`regionid`) REFERENCES `region` (`regionid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `region`
--
ALTER TABLE `region`
  ADD CONSTRAINT `region_ibfk_1` FOREIGN KEY (`provincestateid`) REFERENCES `state` (`provincestateid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`regionid`) REFERENCES `region` (`regionid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`provincestateid`) REFERENCES `state` (`provincestateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`countryid`) REFERENCES `countries` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
