/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : phbs_hrms

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-12-07 10:24:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for hrms_member
-- ----------------------------
/*
	用户表
	@param userid        用户id
	@param username      用户名
	@param password      密码
	@param encrypt       加密规则
	@param lastloginip   最后登陆ip
	@param lastlogintime 最后登陆时间
	@param email         Email
	@param roleid        角色id
	@param realname      角色名称
	@param lang          语言
	//@param openid		 微信用户唯一标识
	//@param headimgurl  微信用户头像
	
	//louis
	@param bind_staff_id 绑定服务量录入行政人员ID，可能多个
	
	主键-id  
	索引-username
	字符串编码-UTF-8
*/
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
  `bind_staff_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`userid`),
  KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hrms_service
-- ----------------------------
/*
    Created by Louis
	工作量表
	@param id        服务记分ID
	@user_id         记分用户ID
	@school_term     学期
	@lack            剩余分数
	@expacted        该用户预设分数
	@final           该用户目前分数
	@score           记分项目详细，JSON字符串
    @update_time     数据更新时间
    @update_ueser_id  数据提交用户ID
	
	主键-id  
	索引-user_id
	字符串编码-UTF-8
*/
DROP TABLE IF EXISTS `hrms_service`;
CREATE TABLE `hrms_service` (
  `id` int(6) NOT NULL,
  `user_id` int(6) NOT NULL,
  `school_term` varchar(20) NOT NULL,
  `lack` float(8,0) NOT NULL,
  `expacted` float(8,0) NOT NULL,
  `final` float(8,0) NOT NULL,
  `score` text NOT NULL,
  `update_time` datetime NOT NULL,
  `update_ueser_id` int(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`update_ueser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hrms_workload
-- ----------------------------
/*
	工作量表
	@param workid        工作id
	@param pargramname   工作名称
	@param project       项目
	@param num       	 数量/天数
	@param type       	 类型
	@param iscost   	 是否计费
	@param userid   	 录入教师id
	@param username 	 上传用户名称
	@param uploadtime    上传时间
	@param credit        学分
	
	主键-id  
	索引-pargramname、username、project、schoolyear
	字符串编码-UTF-8
*/
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
