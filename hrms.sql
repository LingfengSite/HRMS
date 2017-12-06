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
	
	主键-id  
	索引-username
	字符串编码-UTF-8
*/
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


