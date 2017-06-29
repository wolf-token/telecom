/*
Navicat MySQL Data Transfer

Source Server         : 易触云
Source Server Version : 50505
Source Host           : 123.56.161.151:3306
Source Database       : test_mlmbuy

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-06-29 17:10:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ims_telecom_area
-- ----------------------------
DROP TABLE IF EXISTS `ims_telecom_area`;
CREATE TABLE `ims_telecom_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '区域的id',
  `name` varchar(255) DEFAULT NULL COMMENT '区域的名字',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='区域表';

-- ----------------------------
-- Table structure for ims_telecom_business
-- ----------------------------
DROP TABLE IF EXISTS `ims_telecom_business`;
CREATE TABLE `ims_telecom_business` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '营业厅得id',
  `name` varchar(255) DEFAULT NULL COMMENT '营业厅的名称',
  `position` varchar(255) DEFAULT NULL COMMENT '所在位置',
  `tell` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `starttime` varchar(255) DEFAULT NULL COMMENT '营业时间',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `photo` varchar(255) DEFAULT NULL COMMENT '店面展示图',
  `lat` double(255,6) DEFAULT NULL COMMENT '店面的纬度',
  `lng` double(255,6) DEFAULT NULL COMMENT '店面的经度',
  `principal` varchar(255) DEFAULT NULL COMMENT '负责人姓名',
  `area` varchar(255) DEFAULT NULL COMMENT '营业厅所属区域',
  `type` int(11) DEFAULT '0' COMMENT '活动状态的id 0：代表单独的活动 ',
  `wechat` varchar(255) DEFAULT NULL COMMENT '店长微信号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='营业厅表';

-- ----------------------------
-- Table structure for ims_telecom_content
-- ----------------------------
DROP TABLE IF EXISTS `ims_telecom_content`;
CREATE TABLE `ims_telecom_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '活动的id',
  `name` varchar(255) DEFAULT NULL COMMENT '活动的主题',
  `content` text COMMENT '活动内容',
  `starttime` int(11) DEFAULT NULL COMMENT '活动开始时间',
  `endtime` int(11) DEFAULT NULL COMMENT '活动结束时间',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `pid` int(11) unsigned zerofill DEFAULT NULL COMMENT '店面的id',
  `picture` varchar(255) DEFAULT NULL COMMENT '活动的图片',
  `kind` int(11) DEFAULT '0' COMMENT '文章是否为全局 0代表为单独 1：代表为全局',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='活动表';

-- ----------------------------
-- Table structure for ims_telecom_maintain
-- ----------------------------
DROP TABLE IF EXISTS `ims_telecom_maintain`;
CREATE TABLE `ims_telecom_maintain` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '维修网点的id',
  `name` varchar(255) DEFAULT NULL COMMENT '维修网点的名称',
  `tell` varchar(255) DEFAULT NULL COMMENT '联系电话',
  `position` varchar(255) DEFAULT NULL COMMENT '门店地址',
  `address` varchar(255) DEFAULT NULL COMMENT '所属区县',
  `time` datetime DEFAULT NULL COMMENT '添加时间',
  `photo` varchar(255) DEFAULT NULL COMMENT '维修网店图片',
  `lat` double(255,6) DEFAULT NULL COMMENT '店面的纬度',
  `lng` double(255,6) DEFAULT NULL COMMENT '店面的经度',
  `start` varchar(255) DEFAULT NULL COMMENT '网店营业时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='维修网点表';
