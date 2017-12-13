/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : phbs_hrms

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-12-13 14:47:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hrms_member
-- ----------------------------
DROP TABLE IF EXISTS `hrms_member`;
CREATE TABLE `hrms_member` (
  `userid` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `encrypt` varchar(6) DEFAULT NULL,
  `lastloginip` varchar(15) DEFAULT NULL,
  `lastlogintime` int(10) unsigned DEFAULT '0',
  `email` varchar(40) DEFAULT NULL,
  `roleid` smallint(5) DEFAULT '0',
  `realname` varchar(50) NOT NULL DEFAULT '',
  `lang` varchar(6) NOT NULL,
  PRIMARY KEY (`userid`),
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of hrms_member
-- ----------------------------
INSERT INTO `hrms_member` VALUES ('1', '刘圣麟', 'e10adc3949ba59abbe56e057f20f883e', 'md5', null, '0', null, '2', '', '');

-- ----------------------------
-- Table structure for hrms_service
-- ----------------------------
DROP TABLE IF EXISTS `hrms_service`;
CREATE TABLE `hrms_service` (
  `id` int(6) NOT NULL,
  `user_id` int(6) NOT NULL,
  `school_term` varchar(20) NOT NULL,
  `program` int(6) NOT NULL,
  `project` int(6) NOT NULL,
  `score` int(6) NOT NULL,
  `update_time` int(11) NOT NULL,
  `update_user_id` int(6) NOT NULL,
  `state` tinyint(1) unsigned zerofill DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`update_user_id`,`program`,`project`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of hrms_service
-- ----------------------------
INSERT INTO `hrms_service` VALUES ('1', '1', '2017-2018', '2', '2', '2', '1513066553', '1', '0');
INSERT INTO `hrms_service` VALUES ('2', '2', '2017-2018', '3', '3', '2', '1513048562', '1', '0');
INSERT INTO `hrms_service` VALUES ('3', '2', '2017-2018', '3', '3', '2', '1513048562', '2', '0');
INSERT INTO `hrms_service` VALUES ('4', '2', '2017-2018', '1', '6', '3', '1513048562', '2', '0');
INSERT INTO `hrms_service` VALUES ('5', '1', '2017-2018', '3', '2', '3', '1513049709', '1', '1');

-- ----------------------------
-- Table structure for hrms_workload
-- ----------------------------
DROP TABLE IF EXISTS `hrms_workload`;
CREATE TABLE `hrms_workload` (
  `workid` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `pargram` varchar(40) NOT NULL,
  `project` varchar(10) NOT NULL,
  `num` mediumint(6) DEFAULT NULL,
  `type` mediumint(6) NOT NULL,
  `iscost` mediumint(6) unsigned DEFAULT '0',
  `userid` varchar(20) DEFAULT NULL,
  `username` varchar(20) NOT NULL,
  `uploadtime` int(10) unsigned DEFAULT '0',
  `credit` int(10) unsigned DEFAULT '0',
  `schoolyear` varchar(20) NOT NULL,
  PRIMARY KEY (`workid`),
  KEY `pargram` (`pargram`,`username`,`project`,`schoolyear`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of hrms_workload
-- ----------------------------