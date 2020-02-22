drop database if exists pro2;
create schema pro2;
use pro2;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `ufname` varchar(45) NOT NULL,
  `ulname` varchar(45) NOT NULL,
  `uemail` varchar(45) DEFAULT NULL,
  `uusername` varchar(45) NOT NULL,
  `upwd` varchar(45) NOT NULL,
  `ulat` double(10,6) DEFAULT NULL,
  `ulng` double(10,6) DEFAULT NULL,
  PRIMARY KEY (`uid`)
);

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` (`uid`, `ufname`, `ulname`, `uemail`, `uusername`, `upwd`, `ulat`, `ulng`) VALUES
(1, 'jf', 'w', 'wjf@gmail.com', 'wjf', '19b4e95f4656b24fdccc7488a98d596f', NULL, NULL),
(2, 'mx', 'c', 'cmx@gmail.com', 'cmx', '8e16d3332f617b0b48a5ad8a7d4ae306', NULL, NULL),
(3, 'ln', 'm', 'mln@gmail.com', 'mln', 'ae33d20c70e59a4c734d9f2c19c0df56', NULL, NULL),
(4, 'zh', 'h', 'hzh@gmail.com', 'hzh', 'fc49d07911be544a10e819426734d03a', NULL, NULL),
(5, 'aaa', 'bbb', 'aaaa@gmail.com', 'aaaa', '74b87337454200d4d33f80c4663dc5e5', NULL, NULL);

-- ----------------------------
-- Table structure for schedule
-- ----------------------------
DROP TABLE IF EXISTS `schedule`;
CREATE TABLE `schedule` (
  `sid` int(11) NOT NULL AUTO_INCREMENT,
  `sstart` datetime NOT NULL,
  `send` datetime NOT NULL,
  `sweekday` varchar(45) DEFAULT NULL,
  `srepeat` boolean NOT NULL,
  PRIMARY KEY (`sid`)
);

-- ----------------------------
-- Records of schedule
-- ----------------------------
INSERT INTO `schedule` (`sid`, `sstart`, `send`, `sweekday`, `srepeat`) VALUES
(1, '2018-12-08 00:00:00', '2018-12-28 00:00:00', '', 0),
(2, '2018-06-01 00:00:00', '0000-00-00 00:00:00', '', 0),
(3, '2016-12-06 00:00:00', '0000-00-00 00:00:00', '', 0),
(4, '2015-07-14 00:00:00', '0000-00-00 00:00:00', '', 0),
(5, '1990-01-01 00:00:00', '0000-00-00 00:00:00', '', 0),
(6, '2018-12-08 00:00:00', '2018-12-28 00:00:00', '4', 1),
(7, '2018-08-08 00:00:00', '2018-12-21 00:00:00', '1,3,5', 1),
(8, '2015-05-12 00:00:00', '2020-12-31 00:00:00', '', 0),
(9, '2018-12-08 00:00:00', '2018-12-28 00:00:00', '', 0),
(11, '2018-12-11 00:00:00', '2018-12-28 00:00:00', '', 0),
(12, '2018-12-08 00:00:00', '0000-00-00 00:00:00', '', 0),
(13, '2018-12-22 00:00:00', '0000-00-00 00:00:00', '1,2,3,4,5', 1),
(14, '2018-12-13 00:00:00', '0000-00-00 00:00:00', '', 0),
(15, '2018-12-11 00:00:00', '2018-12-28 00:00:00', '2,3,4', 1),
(16, '2018-12-11 00:00:00', '0000-00-00 00:00:00', '', 0),
(17, '2018-12-11 00:00:00', '0000-00-00 00:00:00', '', 0);

-- ----------------------------
-- Table structure for note
-- ----------------------------
DROP TABLE IF EXISTS `note`;
CREATE TABLE `note` (
  `nid` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `nname` varchar(45) NOT NULL,
  `nlat` double(10,6) NOT NULL,
  `nlng` double(10,6) NOT NULL,
  `nradius` int(11) NOT NULL,
  `ncontent` varchar(45) NOT NULL,
  `showto` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`nid`),
  FOREIGN KEY (`sid`) REFERENCES `schedule` (`sid`)
);

-- ----------------------------
-- Records of note
-- ----------------------------
INSERT INTO `note` (`nid`, `sid`, `nname`, `nlat`, `nlng`, `nradius`, `ncontent`, `showto`) VALUES
(1, 1, 'Best Hotpot', 40.718651, -73.994235, 5, 'Tang Hotpot', 'all'),
(2, 2, 'The Eagle Apartment', 40.693245, -73.981981, 100, 'A great apartment for leasing', 'all'),
(3, 3, 'Library', 40.694546, -73.985672, 10, 'Dibner Library', 'all'),
(4, 4, 'Apple SoHo', 40.725130, -73.998936, 5, 'Great Apple store', 'all'),
(5, 5, 'Times Square', 40.758581, -73.985069, 50, 'NYC', 'all'),
(6, 7, 'Hard Rock Cafe', 40.757055, -73.986484, 8, 'A good cafe near the Times Square', 'all'),
(7, 8, 'Sushi!', 40.736438, -73.992139, 3, '15 East - the best sushi restaurants in town', 'all'),
(8, 15, 'Best Hotpot', 40.718651, -73.994235, 5, 'dsad', 'all'),
(9, 16, 'Best Apartment', 40.718651, -73.994235, 20, 'dasda', 'friend');

-- ----------------------------
-- Table structure for commentsofnotecommentsofnote
-- ----------------------------
DROP TABLE IF EXISTS `commentsofnote`;
CREATE TABLE `commentsofnote` (
  `nid` int(11) NOT NULL,
  `ctime` datetime NOT NULL,
  `comment` varchar(45) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY (`nid`,`ctime`,`uid`),
  FOREIGN KEY (`nid`) REFERENCES `note` (`nid`),
  FOREIGN KEY (`uid`) REFERENCES `user` (`uid`)
);

-- ----------------------------
-- Records of commentsofnote
-- ----------------------------
INSERT INTO `commentsofnote` (`nid`, `ctime`, `comment`, `uid`) VALUES
(1, '2018-12-13 12:52:45', 'Tang is a good place to go!', 1),
(1, '2018-12-13 13:04:39', 'yay!', 4),
(2, '2018-12-13 13:04:54', 'I live here!', 4),
(3, '2018-12-13 13:05:09', 'Not a big library', 4),
(5, '2018-12-13 12:53:16', 'Yeah!!!!', 1),
(7, '2018-12-13 12:52:23', 'Agreed!', 1);

-- ----------------------------
-- Table structure for filter
-- ----------------------------
DROP TABLE IF EXISTS `filter`;
CREATE TABLE `filter` (
  `filid` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `fillat` double(10,6) NOT NULL,
  `fillng` double(10,6) NOT NULL,
  `tag` varchar(45) DEFAULT NULL,
  `ustate` varchar(45) DEFAULT NULL,
  `fromwhom` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`filid`),
  FOREIGN KEY (`uid`) REFERENCES `user` (`uid`),
  FOREIGN KEY (`sid`) REFERENCES `schedule` (`sid`)
);

-- ----------------------------
-- Records of filter
-- ----------------------------
INSERT INTO `filter` (`filid`, `sid`, `uid`, `fillat`, `fillng`, `tag`, `ustate`, `fromwhom`) VALUES
(1, 6, 2, 40.751775, -73.990070, '#nyc', '', 'all'),
(5, 12, 4, 40.751775, -73.990070, '', '', 'all'),
(6, 13, 4, 40.694546, -73.985672, '', '', 'all'),
(7, 14, 4, 40.724894, -73.983026, '#baber', '', 'all'),
(8, 17, 2, 40.693245, -73.981981, '#test', '', 'all');

-- ----------------------------
-- Table structure for friendship
-- ----------------------------
DROP TABLE IF EXISTS `friendship`;
CREATE TABLE `friendship` (
  `uid` int(11) NOT NULL,
  `other` int(11) NOT NULL,
  `fstate` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`uid`,`other`),
  FOREIGN KEY (`uid`) REFERENCES `user` (`uid`)
);

-- ----------------------------
-- Records of friendship
-- ----------------------------
INSERT INTO `friendship` (`uid`, `other`, `fstate`) VALUES
(1, 3, 'friend'),
(1, 4, 'friend'),
(2, 1, 'friend'),
(2, 3, 'friend'),
(2, 4, 'friend'),
(3, 4, 'request'),
(5, 1, 'friend'),
(5, 2, 'request');

-- ----------------------------
-- Table structure for tag
-- ----------------------------
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `nid` int(11) NOT NULL,
  `tname` varchar(45) NOT NULL,
  PRIMARY KEY (`tid`),
  FOREIGN KEY (`nid`) REFERENCES `note` (`nid`)
);

-- ----------------------------
-- Records of tag
-- ----------------------------
INSERT INTO `tag` (`tid`, `nid`, `tname`) VALUES
(1, 1, '#great'),
(2, 1, '#hotpot'),
(3, 2, '#living'),
(4, 2, '#better'),
(5, 3, '#study'),
(6, 3, '#library'),
(7, 3, '#final'),
(8, 4, '#Apple'),
(9, 4, '#iPhone'),
(10, 4, '#iPad'),
(11, 5, '#timessquare'),
(12, 5, '#nyc'),
(13, 6, '#coffee'),
(14, 7, '#best'),
(15, 7, '#sushi'),
(16, 8, '#hotpot');

-- ----------------------------
-- Table structure for writes
-- ----------------------------
DROP TABLE IF EXISTS `writes`;
CREATE TABLE `writes` (
  `uid` int(11) NOT NULL,
  `nid` int(11) NOT NULL,
  `ustate` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`uid`,`nid`),
  FOREIGN KEY (`uid`) REFERENCES `user` (`uid`),
  FOREIGN KEY (`nid`) REFERENCES `note` (`nid`)
);

-- ----------------------------
-- Records of writes
-- ----------------------------
INSERT INTO `writes` (`uid`, `nid`, `ustate`) VALUES
(1, 1, 'happy'),
(1, 2, 'home'),
(1, 3, 'study'),
(2, 4, 'apple'),
(2, 5, 'symbol'),
(3, 6, 'work'),
(3, 7, 'eat'),
(5, 8, 'happy'),
(5, 9, '');