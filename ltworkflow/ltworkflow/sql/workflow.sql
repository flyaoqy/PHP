# MySQL-Front 5.1  (Build 4.2)

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE */;
/*!40101 SET SQL_MODE='STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES */;
/*!40103 SET SQL_NOTES='ON' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;


# Host: 127.0.0.1    Database: workflow
# ------------------------------------------------------
# Server version 5.1.73-community

DROP DATABASE IF EXISTS `workflow`;
CREATE DATABASE `workflow` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `workflow`;

#
# Source for table t_purchase_contract
#

DROP TABLE IF EXISTS `t_purchase_contract`;
CREATE TABLE `t_purchase_contract` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `et_uid` varchar(50) DEFAULT NULL,
  `wf_uid` varchar(50) DEFAULT NULL,
  `userno` varchar(50) DEFAULT NULL COMMENT '起草人工号',
  `username` varchar(50) DEFAULT NULL COMMENT '起草人',
  `createtime` datetime DEFAULT NULL COMMENT '创建时间',
  `contract_name` varchar(255) DEFAULT NULL COMMENT '合同',
  `contract_type` varchar(50) DEFAULT NULL COMMENT '合同类型',
  `goods_type` varchar(50) DEFAULT NULL COMMENT '采购物品类型',
  `contract_sum` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='采购';

#
# Dumping data for table t_purchase_contract
#

LOCK TABLES `t_purchase_contract` WRITE;
/*!40000 ALTER TABLE `t_purchase_contract` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_purchase_contract` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_currentstep
#

DROP TABLE IF EXISTS `t_wf_currentstep`;
CREATE TABLE `t_wf_currentstep` (
  `uid` varchar(50) NOT NULL DEFAULT '',
  `et_uid` varchar(50) DEFAULT NULL,
  `cs_salarysn` varchar(20) DEFAULT NULL,
  `cs_id` varchar(50) DEFAULT NULL,
  `cs_status` varchar(50) DEFAULT NULL,
  `cs_updateby` varchar(20) DEFAULT NULL,
  `steplock` varchar(50) DEFAULT NULL,
  `cs_endTime` datetime DEFAULT NULL,
  `cs_parentuid` varchar(50) DEFAULT NULL,
  `cs_parentid` varchar(50) DEFAULT NULL,
  `cs_orgid` varchar(50) DEFAULT NULL,
  `wf_uid` varchar(50) DEFAULT NULL,
  `cs_prestatus` varchar(50) DEFAULT NULL,
  `cs_nodename` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_currentstep
#

LOCK TABLES `t_wf_currentstep` WRITE;
/*!40000 ALTER TABLE `t_wf_currentstep` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_wf_currentstep` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_entry
#

DROP TABLE IF EXISTS `t_wf_entry`;
CREATE TABLE `t_wf_entry` (
  `et_uid` varchar(50) NOT NULL DEFAULT '',
  `wf_uid` varchar(50) DEFAULT NULL,
  `et_title` varchar(350) DEFAULT NULL,
  `et_state` smallint(6) DEFAULT NULL,
  `et_createuser` varchar(50) DEFAULT NULL,
  `et_createdate` datetime DEFAULT NULL,
  `et_handle` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`et_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_entry
#

LOCK TABLES `t_wf_entry` WRITE;
/*!40000 ALTER TABLE `t_wf_entry` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_wf_entry` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_flowstack
#

DROP TABLE IF EXISTS `t_wf_flowstack`;
CREATE TABLE `t_wf_flowstack` (
  `fs_uid` varchar(50) NOT NULL DEFAULT '',
  `fs_puid` varchar(50) DEFAULT NULL,
  `et_uid` varchar(50) DEFAULT NULL,
  `wf_uid` varchar(50) DEFAULT NULL,
  `fs_pcsid` varchar(50) DEFAULT NULL,
  `fs_createdate` datetime DEFAULT NULL,
  PRIMARY KEY (`fs_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_flowstack
#

LOCK TABLES `t_wf_flowstack` WRITE;
/*!40000 ALTER TABLE `t_wf_flowstack` DISABLE KEYS */;
INSERT INTO `t_wf_flowstack` VALUES ('2e9ee0f7-f559-9ffe-09ad-ada0f41f80bd','','88a0919c-caa2-1d76-f9be-d7ba6c63d231','1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','','2017-08-16 11:00:50');
INSERT INTO `t_wf_flowstack` VALUES ('a8955174-85f2-36c0-93f3-368d917aa0dd','','0595fe63-cf19-37bf-59bb-e2b58891ceb4','f48d23f9-e5e2-49f2-3d6c-d855aa40e270','','2017-08-04 18:02:32');
/*!40000 ALTER TABLE `t_wf_flowstack` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_log
#

DROP TABLE IF EXISTS `t_wf_log`;
CREATE TABLE `t_wf_log` (
  `wflg_uid` varchar(50) NOT NULL DEFAULT '',
  `et_uid` varchar(50) DEFAULT NULL,
  `wflg_type` varchar(50) DEFAULT NULL,
  `wflg_salarysn` varchar(50) DEFAULT NULL,
  `wflg_date` datetime DEFAULT NULL,
  `wflg_startDate` datetime DEFAULT NULL,
  `wflg_finishDate` datetime DEFAULT NULL,
  `wflg_dueDate` datetime DEFAULT NULL,
  `wf_status` varchar(50) DEFAULT NULL,
  `wf_actionid` varchar(50) DEFAULT NULL,
  `cs_id` smallint(6) DEFAULT NULL,
  `cs_name` varchar(50) DEFAULT NULL,
  `wf_uid` varchar(50) DEFAULT NULL,
  `wflg_comment` varchar(5000) DEFAULT NULL,
  `wflg_accredit` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`wflg_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_log
#

LOCK TABLES `t_wf_log` WRITE;
/*!40000 ALTER TABLE `t_wf_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_wf_log` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_steps_condition
#

DROP TABLE IF EXISTS `t_wf_steps_condition`;
CREATE TABLE `t_wf_steps_condition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wf_uid` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `step_id` varchar(50) DEFAULT NULL,
  `next_step_id` varchar(50) DEFAULT NULL,
  `expression` varchar(2000) DEFAULT NULL,
  `group_uid` varchar(50) DEFAULT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_steps_condition
#

LOCK TABLES `t_wf_steps_condition` WRITE;
/*!40000 ALTER TABLE `t_wf_steps_condition` DISABLE KEYS */;
INSERT INTO `t_wf_steps_condition` VALUES (3,'d85ca61d-b9c9-4e86-62e4-978be88a125a','custom_role',NULL,NULL,'测试角色','4c45289d-4b97-c3d8-6424-a520d5c963a8',NULL);
INSERT INTO `t_wf_steps_condition` VALUES (5,'1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','role','5',NULL,'1','afe5cf26-564b-9a86-9aff-1d1ff420e541',0);
INSERT INTO `t_wf_steps_condition` VALUES (6,'1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','condition','5','7','',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (7,'1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','role','7',NULL,'1','fadd7866-5ae3-04e4-19b1-ca094165d77e',0);
INSERT INTO `t_wf_steps_condition` VALUES (8,'1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','role','9',NULL,'1','ea59911c-b87d-15ef-b512-9ccb24616f04',0);
INSERT INTO `t_wf_steps_condition` VALUES (9,'1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','role','2',NULL,'1','3804e98e-4968-7cbb-8d17-187a62543e37',0);
INSERT INTO `t_wf_steps_condition` VALUES (10,'1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','role','3',NULL,'#合同类型# == \'服务\' || #合同类型# == \'市场\'','16288190-a703-c61c-50a4-1e3c49cd348c',1);
INSERT INTO `t_wf_steps_condition` VALUES (11,'1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','role','4',NULL,'1','5cb0c2b9-d44d-02ea-0ef2-2649a57a68b2',0);
INSERT INTO `t_wf_steps_condition` VALUES (12,'1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','role','3',NULL,'#合同类型# == \'实物\'','fd5d22ef-5eea-8eb4-9157-d0593545cfcf',2);
INSERT INTO `t_wf_steps_condition` VALUES (13,'1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','condition','2','3','#物品类型# == \'服务器\'',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (14,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','role','undefined',NULL,'1','2696b5bb-3b04-35ea-fdc9-aa07078cea06',0);
INSERT INTO `t_wf_steps_condition` VALUES (15,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','role','-2',NULL,'1','cc9df9e7-36a9-29db-eac0-28ac83126753',0);
INSERT INTO `t_wf_steps_condition` VALUES (16,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','role','-6',NULL,'1','c69d2756-d89f-5901-9fb7-0c2ace80def7',0);
INSERT INTO `t_wf_steps_condition` VALUES (17,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','role','-3',NULL,'1','7225e742-ac33-dc40-48d3-817e3e2a32d1',0);
INSERT INTO `t_wf_steps_condition` VALUES (18,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','role','-7',NULL,'1','e5df98b0-ba7e-0a36-2739-481fd5d7f131',0);
INSERT INTO `t_wf_steps_condition` VALUES (19,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','condition','-2','-6','1',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (20,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','condition','-2','-3','0',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (21,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','condition','-6','-7','1',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (22,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','condition','-1','-2','1',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (23,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','condition','-7','-5','1',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (24,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','condition','-3','-5','1',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (25,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','condition','-8','-7','1',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (26,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','condition','-6','-8','1',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (27,'f48d23f9-e5e2-49f2-3d6c-d855aa40e270','role','-8',NULL,'1','9ec01a70-40f2-18da-dd06-4dbd5f1f8516',0);
INSERT INTO `t_wf_steps_condition` VALUES (28,'f167a71d-4d8f-bdae-0a02-a485d8e8ca4e','condition','5','6','1',NULL,0);
INSERT INTO `t_wf_steps_condition` VALUES (29,'f167a71d-4d8f-bdae-0a02-a485d8e8ca4e','role','6',NULL,'1','6fc3a667-cf31-3907-4065-6e982d89fe5d',0);
INSERT INTO `t_wf_steps_condition` VALUES (30,'f167a71d-4d8f-bdae-0a02-a485d8e8ca4e','role','8',NULL,'1','66dbce78-7731-7b69-4111-7fe2849dbbfa',0);
INSERT INTO `t_wf_steps_condition` VALUES (31,'f167a71d-4d8f-bdae-0a02-a485d8e8ca4e','role','7',NULL,'1','faf697a0-1f06-3d3a-d1ba-1d28c046a58f',0);
INSERT INTO `t_wf_steps_condition` VALUES (32,'f167a71d-4d8f-bdae-0a02-a485d8e8ca4e','role','10',NULL,'1','699158ca-94e9-b863-7799-57bca9c68fce',0);
INSERT INTO `t_wf_steps_condition` VALUES (33,'f167a71d-4d8f-bdae-0a02-a485d8e8ca4e','role','5',NULL,'1','b72e1ffe-ef34-e4fd-fb12-598481341b4e',0);
INSERT INTO `t_wf_steps_condition` VALUES (34,'f167a71d-4d8f-bdae-0a02-a485d8e8ca4e','role','9',NULL,'1','d8965173-80d9-63ad-1b67-0bed2862af83',0);
INSERT INTO `t_wf_steps_condition` VALUES (35,'e17af866-29ca-7bba-ba61-5cc4e9e21d61','role','3',NULL,'1','09650f2f-2ec3-0e4b-0dd3-a34956bb74f1',0);
/*!40000 ALTER TABLE `t_wf_steps_condition` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_steps_condition_var
#

DROP TABLE IF EXISTS `t_wf_steps_condition_var`;
CREATE TABLE `t_wf_steps_condition_var` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wf_uid` varchar(50) DEFAULT NULL,
  `expression_key` varchar(50) DEFAULT NULL,
  `expression_value` varchar(500) DEFAULT NULL,
  `expression_description` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_steps_condition_var
#

LOCK TABLES `t_wf_steps_condition_var` WRITE;
/*!40000 ALTER TABLE `t_wf_steps_condition_var` DISABLE KEYS */;
INSERT INTO `t_wf_steps_condition_var` VALUES (1,'d85ca61d-b9c9-4e86-62e4-978be88a125a','@测试@','刷刷刷s',NULL);
/*!40000 ALTER TABLE `t_wf_steps_condition_var` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_steps_user
#

DROP TABLE IF EXISTS `t_wf_steps_user`;
CREATE TABLE `t_wf_steps_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `group_uid` varchar(50) DEFAULT NULL,
  `usercode` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_steps_user
#

LOCK TABLES `t_wf_steps_user` WRITE;
/*!40000 ALTER TABLE `t_wf_steps_user` DISABLE KEYS */;
INSERT INTO `t_wf_steps_user` VALUES (4,'4c45289d-4b97-c3d8-6424-a520d5c963a8','s001','刘三');
INSERT INTO `t_wf_steps_user` VALUES (9,'afe5cf26-564b-9a86-9aff-1d1ff420e541','s003','张强');
INSERT INTO `t_wf_steps_user` VALUES (10,'afe5cf26-564b-9a86-9aff-1d1ff420e541','s007','张国栋');
INSERT INTO `t_wf_steps_user` VALUES (11,'fadd7866-5ae3-04e4-19b1-ca094165d77e','s008','李菲');
INSERT INTO `t_wf_steps_user` VALUES (12,'ea59911c-b87d-15ef-b512-9ccb24616f04','s009','黄土良');
INSERT INTO `t_wf_steps_user` VALUES (17,'3804e98e-4968-7cbb-8d17-187a62543e37','s007','张国栋');
INSERT INTO `t_wf_steps_user` VALUES (29,'16288190-a703-c61c-50a4-1e3c49cd348c','s006','张爽');
INSERT INTO `t_wf_steps_user` VALUES (30,'fd5d22ef-5eea-8eb4-9157-d0593545cfcf','s004','赵丽颖');
INSERT INTO `t_wf_steps_user` VALUES (31,'5cb0c2b9-d44d-02ea-0ef2-2649a57a68b2','s009','黄土良');
INSERT INTO `t_wf_steps_user` VALUES (32,'5cb0c2b9-d44d-02ea-0ef2-2649a57a68b2','s003','张强');
INSERT INTO `t_wf_steps_user` VALUES (33,'2696b5bb-3b04-35ea-fdc9-aa07078cea06','s002','刘四');
INSERT INTO `t_wf_steps_user` VALUES (34,'2696b5bb-3b04-35ea-fdc9-aa07078cea06','s003','张强');
INSERT INTO `t_wf_steps_user` VALUES (35,'cc9df9e7-36a9-29db-eac0-28ac83126753','s002','刘四');
INSERT INTO `t_wf_steps_user` VALUES (36,'c69d2756-d89f-5901-9fb7-0c2ace80def7','s007','张国栋');
INSERT INTO `t_wf_steps_user` VALUES (37,'7225e742-ac33-dc40-48d3-817e3e2a32d1','s008','李菲');
INSERT INTO `t_wf_steps_user` VALUES (40,'9ec01a70-40f2-18da-dd06-4dbd5f1f8516','s009','黄土良');
INSERT INTO `t_wf_steps_user` VALUES (41,'e5df98b0-ba7e-0a36-2739-481fd5d7f131','s003','张强');
INSERT INTO `t_wf_steps_user` VALUES (42,'e5df98b0-ba7e-0a36-2739-481fd5d7f131','s006','张爽');
INSERT INTO `t_wf_steps_user` VALUES (43,'6fc3a667-cf31-3907-4065-6e982d89fe5d','s002','刘四');
INSERT INTO `t_wf_steps_user` VALUES (44,'66dbce78-7731-7b69-4111-7fe2849dbbfa','s007','张国栋');
INSERT INTO `t_wf_steps_user` VALUES (45,'faf697a0-1f06-3d3a-d1ba-1d28c046a58f','s003','张强');
INSERT INTO `t_wf_steps_user` VALUES (46,'faf697a0-1f06-3d3a-d1ba-1d28c046a58f','s006','张爽');
INSERT INTO `t_wf_steps_user` VALUES (47,'699158ca-94e9-b863-7799-57bca9c68fce','s004','赵丽颖');
INSERT INTO `t_wf_steps_user` VALUES (48,'b72e1ffe-ef34-e4fd-fb12-598481341b4e','s008','李菲');
INSERT INTO `t_wf_steps_user` VALUES (49,'d8965173-80d9-63ad-1b67-0bed2862af83','s009','黄土良');
INSERT INTO `t_wf_steps_user` VALUES (50,'09650f2f-2ec3-0e4b-0dd3-a34956bb74f1','s006','张爽');
/*!40000 ALTER TABLE `t_wf_steps_user` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_testcase
#

DROP TABLE IF EXISTS `t_wf_testcase`;
CREATE TABLE `t_wf_testcase` (
  `tc_uid` varchar(50) NOT NULL DEFAULT '',
  `tc_name` varchar(100) DEFAULT NULL,
  `tc_joinmark` varchar(50) DEFAULT NULL,
  `tc_xmlcontent` text,
  `tc_updateuser` varchar(50) DEFAULT NULL,
  `tc_updatetime` datetime DEFAULT NULL,
  `tc_discript` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`tc_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_testcase
#

LOCK TABLES `t_wf_testcase` WRITE;
/*!40000 ALTER TABLE `t_wf_testcase` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_wf_testcase` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_testcase_result
#

DROP TABLE IF EXISTS `t_wf_testcase_result`;
CREATE TABLE `t_wf_testcase_result` (
  `tcr_uid` int(11) NOT NULL AUTO_INCREMENT,
  `tcr_pid` varchar(50) DEFAULT NULL,
  `tcr_authoruser` varchar(50) DEFAULT NULL,
  `tcr_condtion` varchar(1000) DEFAULT NULL,
  `tcr_approvelist` varchar(1000) DEFAULT NULL,
  `tcr_createuser` varchar(50) DEFAULT NULL,
  `tcr_createtime` datetime DEFAULT NULL,
  PRIMARY KEY (`tcr_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_testcase_result
#

LOCK TABLES `t_wf_testcase_result` WRITE;
/*!40000 ALTER TABLE `t_wf_testcase_result` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_wf_testcase_result` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_workflow
#

DROP TABLE IF EXISTS `t_wf_workflow`;
CREATE TABLE `t_wf_workflow` (
  `wf_uid` varchar(50) NOT NULL DEFAULT '',
  `wf_name` varchar(50) DEFAULT NULL,
  `wf_createtime` datetime DEFAULT NULL,
  `wf_filename` text,
  `wf_layout` text,
  `wf_version` varchar(50) DEFAULT NULL,
  `wf_handle` varchar(100) DEFAULT NULL,
  `wf_superior` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`wf_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_workflow
#

LOCK TABLES `t_wf_workflow` WRITE;
/*!40000 ALTER TABLE `t_wf_workflow` DISABLE KEYS */;
INSERT INTO `t_wf_workflow` VALUES ('1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0','采购审批流程','2017-03-19 17:55:16','<?xml version=\"1.0\" encoding=\"utf8\"?>\r\n<workflow title=\"采购审批流程\">   \r\n\t<global>\r\n\t\t<init path=\"test\"/>\r\n\t\t<email type=\"html\" doctype=\"测试邮件\"/>\r\n\t\t<reject name=\"驳回\" path=\"test/Reject\"/>\r\n\t\t<countersign name=\"会签\" path=\"test/Countersign\"/>\r\n\t</global>\r\n\t<initial-actions id=\"1\">\r\n\t\t<condition/>\r\n\t\t<actions>\r\n\t\t\t<action id=\"0\" name=\"送审\" >\r\n\t\t\t\t<validate type=\"beanshell\" path=\"test/main_before\">\r\n\t\t\t\t\t<type>submit</type>\r\n\t\t\t\t</validate>\r\n\t\t\t\t<results>\r\n\t\t\t\t\t<unconditional-result step=\"2\">\r\n\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\" >\r\n\t\t\t\t\t\t\t\t</role>\r\n\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t</results>\r\n\t\t\t\t<post-function type=\"beanshell\" path=\"test/main_after\">\r\n\t\t\t\t\t<type>submit</type>\r\n\t\t\t\t</post-function>\r\n\t\t\t</action>\r\n\t\t</actions>\r\n\t</initial-actions>\r\n\t<steps>\r\n\t\t<step id=\"2\" name=\"研发经理\">\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\" >\r\n\t\t\t\t\t<results>\r\n\t\t\t\t\t\t<result step=\"3\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\" ></role>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t\t<condition type=\"config\">\r\n\t\t\t\t\t\t\t</condition>\r\n\t\t\t\t\t\t</result>\r\n\t\t\t\t\t\t<unconditional-result  step=\"4\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\"></role>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n\t\t<step id=\"3\" name=\"财务\">\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\" >\r\n\t\t\t\t\t<validate type=\"beanshell\" path=\"test/main_before\">\r\n\t\t\t\t\t\t<type>act2</type>\r\n\t\t\t\t\t</validate>\r\n\t\t\t\t\t<results>\r\n\t\t\t\t\t\t<unconditional-result  step=\"4\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\" >\r\n\t\t\t\t\t\t\t\t</role>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t\t<post-function type=\"beanshell\" path=\"test/main_after\">\r\n\t\t\t\t\t\t<type>act2</type>\r\n\t\t\t\t\t</post-function>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n\t\t<step id=\"4\" name=\"法务\">\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\" >\r\n\t\t\t\t\t<validate type=\"beanshell\" path=\"test/main_before\">\r\n\t\t\t\t\t\t<type>act3</type>\r\n\t\t\t\t\t</validate>\r\n\t\t\t\t\t<results>\r\n\t\t\t\t\t\t<unconditional-result end=\"500\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\"></role>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n\t</steps>\r\n\t<splits/>\r\n\t<joins/>\r\n\t<end-nodes>\r\n\t\t<node id=\"500\" name=\"结束\" path=\"test/main_after\">\r\n\t\t\t<type>complete</type>\r\n\t\t</node>\r\n\t</end-nodes>\r\n</workflow>','{ \"class\": \"go.GraphLinksModel\",\n  \"linkFromPortIdProperty\": \"fromPort\",\n  \"linkToPortIdProperty\": \"toPort\",\n  \"nodeDataArray\": [ \n{\"key\":\"1\", \"id\":\"1\", \"text\":\"开始\", \"category\":\"Start\", \"loc\":\"0 0\"},\n{\"key\":\"2\", \"id\":\"2\", \"text\":\"研发经理\", \"loc\":\"-1.4210854715202004e-14 71.99999999999997\"},\n{\"key\":\"3\", \"id\":\"3\", \"text\":\"财务\", \"loc\":\"111.99999999999997 111.9999999999999\"},\n{\"key\":\"4\", \"id\":\"4\", \"text\":\"法务\", \"loc\":\"0 180\"},\n{\"key\":\"500\", \"id\":\"500\", \"text\":\"结束\", \"category\":\"End\", \"loc\":\"3.552713678800501e-15 251.00000000000003\"}\n ],\n  \"linkDataArray\": [ \n{\"from\":1, \"to\":\"2\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[0,21.808122679245,0,31.808122679245,0,38.754061339622,0,38.754061339622,0,45.7,0,55.7]},\n{\"from\":\"2\", \"to\":\"3\", \"fromPort\":\"R\", \"toPort\":\"T\", \"condition_type\":\"config\", \"condition_value\":\"\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[37.81997680664061,71.99999999999997,47.81997680664061,71.99999999999997,112,71.99999999999997,112,78.78113784790031,112,85.56227569580065,112,95.56227569580065]},\n{\"from\":\"3\", \"to\":\"4\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[112,128.4377243041991,112,138.4377243041991,112,145.99999999999994,0,145.99999999999994,0,153.5622756958008,0,163.5622756958008]},\n{\"from\":\"4\", \"to\":\"500\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"\", \"role_value\":\"\", \"points\":[0,196.3,0,206.3,0,212.74593866038,0,212.74593866038,0,219.19187732076,0,229.19187732076]},\n{\"from\":\"2\", \"to\":\"4\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[0,88.3,0,98.3,0,126,0,126,0,153.7,0,163.7]}\n ]}','1.0',NULL,NULL);
INSERT INTO `t_wf_workflow` VALUES ('e17af866-29ca-7bba-ba61-5cc4e9e21d61','调用子流程测试','2017-08-21 17:28:41','<?xml version=\"1.0\" ?>\r\n<workflow title=\"调用子流程测试\">\r\n\t<global>\r\n\t\t<init path=\"test\"/>\r\n                <email type=\"html\" doctype=\"测试邮件\"/>\r\n                <reject name=\"驳回\" path=\"test/Reject\"/>\r\n                <countersign name=\"会签\" path=\"test/Countersign\"/>\r\n\t</global>\r\n\t<initial-actions id=\"1\">\r\n\t\t<condition/>\r\n\t\t<actions>\r\n\t\t\t<action id=\"0\" name=\"送审\">\r\n\t\t\t\t<results>\r\n\t\t\t\t\t<unconditional-result step=\"2\" auto=\"true\">\r\n\t\t\t\t\t\t<roles/>\r\n\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t</results>\r\n\t\t\t</action>\r\n\t\t</actions>\r\n\t</initial-actions>\r\n\t<steps>\r\n\t\t<step id=\"2\" name=\"审批子流程\">\r\n\t\t\t<sub-step>\r\n\t\t\t\t<sub-flow uid=\"f167a71d-4d8f-bdae-0a02-a485d8e8ca4e\" title=\"并行流程测试\"  desc=\"并行流程测试\">\r\n\t\t\t\t\t<condition type=\"beanshell\" path=\"test/main_check\">\r\n\t\t\t\t\t\t<type>parallel</type>\r\n\t\t\t\t\t</condition>\r\n\t\t\t\t</sub-flow>\r\n                                <sub-flow uid=\"1ba74cea-cbaf-cd6f-cb5d-0924a4e6e3c0\" title=\"采购审批流程\"  desc=\"采购审批流程\">\r\n\t\t\t\t\t<condition type=\"true\"/>\r\n\t\t\t\t</sub-flow>\r\n\t\t\t</sub-step>\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\" status=\"Underway\">\r\n\t\t\t\t\t<results>\r\n                                                \r\n\t\t\t\t\t\t<unconditional-result  step=\"3\">\r\n\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t<role type=\"2\" ></role>\r\n\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n\r\n               <step id=\"3\" name=\"总部审批\">\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\" >\r\n\t\t\t\t\t<results>\r\n\r\n\t\t\t\t\t\t<unconditional-result  end=\"500\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n\t</steps>\r\n\t<end-nodes>\r\n\t\t<node id=\"500\" name=\"结束\" type=\"end\" path=\"test/main_after\">\r\n\t\t\t<type>complete</type>\r\n\t\t</node>\r\n\t</end-nodes>\r\n</workflow>','{ \"class\": \"go.GraphLinksModel\",\n  \"linkFromPortIdProperty\": \"fromPort\",\n  \"linkToPortIdProperty\": \"toPort\",\n  \"nodeDataArray\": [ \n{\"key\":\"1\", \"id\":\"1\", \"text\":\"开始\", \"category\":\"Start\", \"loc\":\"0 0\"},\n{\"key\":\"2\", \"id\":\"2\", \"text\":\"审批子流程\", \"loc\":\"0 60\"},\n{\"key\":\"3\", \"id\":\"3\", \"text\":\"总部审批\", \"loc\":\"0 120\"},\n{\"key\":\"500\", \"id\":\"500\", \"text\":\"结束\", \"category\":\"End\", \"loc\":\"0 180\"}\n ],\n  \"linkDataArray\": [ \n{\"from\":1, \"to\":\"2\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"\", \"role_value\":\"\", \"points\":[0,21.808122679244644,0,31.808122679244644,0,32.68519918752271,0,32.68519918752271,0,33.56227569580078,0,43.56227569580078]},\n{\"from\":\"2\", \"to\":\"3\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[0,76.43772430419921,0,86.43772430419921,0,86,0,86,0,93.56227569580078,0,103.56227569580078]},\n{\"from\":\"3\", \"to\":\"500\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"\", \"role_value\":\"\", \"points\":[0,136.4377243041992,0,146.4377243041992,0,147.3148008124773,0,147.3148008124773,0,148.19187732075537,0,158.19187732075537]}\n ]}','1.0',NULL,NULL);
INSERT INTO `t_wf_workflow` VALUES ('f167a71d-4d8f-bdae-0a02-a485d8e8ca4e','并行流程测试','2017-08-04 15:46:32','<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n<workflow title=\"并行流程测试\">\r\n\t<global>\r\n\t\t<init path=\"test\"/>\r\n\t\t<email type=\"html\" doctype=\"测试邮件\"/>\r\n\t\t<reject name=\"驳回\" path=\"test/Reject\"/>\r\n\t\t<countersign name=\"会签\" path=\"test/Countersign\"   />\r\n\t</global>\r\n\t<initial-actions id=\"1\">\r\n\t\t<condition/>\r\n\t\t<actions>\r\n\t\t\t<action id=\"0\" name=\"送审\" >\r\n\t\t\t\t<validate type=\"beanshell\" path=\"test/main_before\">\r\n\t\t\t\t\t<type>submit</type>\r\n\t\t\t\t</validate>\r\n\t\t\t\t<results>\r\n\t\t\t\t\t<unconditional-result  step=\"5\">\r\n\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\">\r\n\t\t\t\t\t\t\t\t</role>\r\n\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t</results>\r\n\t\t\t\t<post-function type=\"beanshell\" path=\"test/main_after\">\r\n\t\t\t\t\t<type>submit</type>\r\n\t\t\t\t</post-function>\r\n\t\t\t</action>\r\n\t\t</actions>\r\n\t</initial-actions>\r\n\t<steps>\r\n\t\t<step id=\"5\" name=\"环节1\">\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\" type=\"split\">\r\n\t\t\t\t\t<results>\r\n\t\t\t\t\t\t<result step=\"6\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\">\r\n\t\t\t\t\t\t\t\t</role>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t\t<condition type=\"config\" >\r\n\t\t\t\t\t\t\t</condition>\r\n\t\t\t\t\t\t</result>\r\n\t\t\t\t\t\t<unconditional-result step=\"8\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\" >\r\n\t\t\t\t\t\t\t\t</role>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n\t\t<step id=\"6\" name=\"环节2\">\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\" >\r\n\t\t\t\t\t<validate type=\"beanshell\" path=\"test/main_before\">\r\n\t\t\t\t\t\t<type>act2</type>\r\n\t\t\t\t\t</validate>\r\n\t\t\t\t\t<results>\r\n\t\t\t\t\t\t<unconditional-result step=\"7\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\" >\r\n\t\t\t\t\t\t\t\t</role>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t\t<post-function type=\"beanshell\" path=\"test/main_after\">\r\n\t\t\t\t\t\t<type>act2</type>\r\n\t\t\t\t\t</post-function>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n\t\t<step id=\"7\" name=\"环节3\">\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\" >\r\n\t\t\t\t\t<validate type=\"beanshell\" path=\"test/main_before\">\r\n\t\t\t\t\t\t<type>act3</type>\r\n\t\t\t\t\t</validate>\r\n\t\t\t\t\t<results>\r\n\t\t\t\t\t\t<unconditional-result step=\"9\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\" path=\"test/main_user\">\r\n\t\t\t\t\t\t\t\t</role>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n\t\t<step id=\"8\" name=\"环节4\">\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\" status=\"Underway\">\r\n\t\t\t\t\t<results>\r\n\t\t\t\t\t\t<unconditional-result step=\"9\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\" path=\"test/main_user\">\r\n\t\t\t\t\t\t\t\t</role>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n                <step id=\"9\" name=\"环节5\" type=\"join\">\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\" >\r\n\t\t\t\t\t<results>\r\n\t\t\t\t\t\t<unconditional-result step=\"10\">\r\n\t\t\t\t\t\t\t<roles>\r\n\t\t\t\t\t\t\t\t<role type=\"2\" path=\"test/main_user\">\r\n\t\t\t\t\t\t\t\t</role>\r\n\t\t\t\t\t\t\t</roles>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n                <step id=\"10\" name=\"环节6\" >\r\n\t\t\t<actions>\r\n\t\t\t\t<action id=\"1\" name=\"通过\">\r\n\t\t\t\t\t<results>\r\n\t\t\t\t\t\t<unconditional-result  end=\"4\">\r\n\t\t\t\t\t\t\t<roles/>\r\n\t\t\t\t\t\t</unconditional-result>\r\n\t\t\t\t\t</results>\r\n\t\t\t\t</action>\r\n\t\t\t</actions>\r\n\t\t</step>\r\n\t</steps>\r\n\t<splits/>\r\n\t<joins/>\r\n\t<end-nodes>\r\n\t\t<node id=\"4\" name=\"结束\" path=\"test/main_after\">\r\n\t\t\t<type>complete</type>\r\n\t\t</node>\r\n\t</end-nodes>\r\n</workflow>','{ \"class\": \"go.GraphLinksModel\",\n  \"linkFromPortIdProperty\": \"fromPort\",\n  \"linkToPortIdProperty\": \"toPort\",\n  \"nodeDataArray\": [ \n{\"key\":\"1\", \"id\":\"1\", \"text\":\"开始\", \"category\":\"Start\", \"loc\":\"0 0\"},\n{\"key\":\"5\", \"id\":\"5\", \"text\":\"环节1\", \"loc\":\"0 60\", \"category\":\"split\"},\n{\"key\":\"6\", \"id\":\"6\", \"text\":\"环节2\", \"loc\":\"97 121\"},\n{\"key\":\"7\", \"id\":\"7\", \"text\":\"环节3\", \"loc\":\"97 206.00000000000009\"},\n{\"key\":\"8\", \"id\":\"8\", \"text\":\"环节4\", \"loc\":\"-122.00000000000003 204.00000000000003\"},\n{\"key\":\"9\", \"id\":\"9\", \"text\":\"环节5\", \"loc\":\"0 292.0000000000001\", \"category\":\"join\"},\n{\"key\":\"10\", \"id\":\"10\", \"text\":\"环节6\", \"loc\":\"0 360\"},\n{\"key\":\"4\", \"id\":\"4\", \"text\":\"结束\", \"category\":\"End\", \"loc\":\"0 439.9999999999999\"}\n ],\n  \"linkDataArray\": [ \n{\"from\":1, \"to\":\"5\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[0,21.808122679244644,0,31.808122679244644,0,32.68519918752271,0,32.68519918752271,0,33.56227569580078,0,43.56227569580078]},\n{\"from\":\"5\", \"to\":\"6\", \"fromPort\":\"R\", \"toPort\":\"T\", \"condition_type\":\"config\", \"condition_value\":\"\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[27.23657989501953,60,37.23657989501953,60,97,60,97,77.2811378479004,97,94.56227569580078,97,104.56227569580078]},\n{\"from\":\"6\", \"to\":\"7\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[97,137.4377243041992,97,147.4377243041992,97,161,97,161,97,179.5622756958008,97,189.5622756958008]},\n{\"from\":\"7\", \"to\":\"9\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[97,222.43772430419924,97,232.43772430419924,97,249,0,249,0,265.5622756958008,0,275.5622756958008]},\n{\"from\":\"9\", \"to\":\"10\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[0,308.4377243041992,0,318.4377243041992,0,326,0,326,0,333.5622756958008,0,343.5622756958008]},\n{\"from\":\"10\", \"to\":\"4\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"\", \"role_value\":\"\", \"points\":[0,376.4377243041992,0,386.4377243041992,0,397.3148008124773,0,397.3148008124773,0,408.19187732075534,0,418.19187732075534]},\n{\"from\":\"5\", \"to\":\"8\", \"fromPort\":\"L\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[-27.23657989501953,60,-37.23657989501953,60,-122,60,-122,118.78113784790037,-122,177.56227569580074,-122,187.56227569580074]},\n{\"from\":\"8\", \"to\":\"9\", \"fromPort\":\"B\", \"toPort\":\"T\", \"condition_type\":\"true\", \"condition_value\":\"true\", \"role_type\":\"2\", \"role_value\":\"\", \"points\":[-122,220.43772430419918,-122,230.43772430419918,-122,248.00000000000006,0,248.00000000000006,0,265.5622756958009,0,275.5622756958009]}\n ]}','1.0',NULL,NULL);
/*!40000 ALTER TABLE `t_wf_workflow` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table t_wf_workflow_xmllog
#

DROP TABLE IF EXISTS `t_wf_workflow_xmllog`;
CREATE TABLE `t_wf_workflow_xmllog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wf_uid` varchar(50) DEFAULT NULL,
  `wf_name` varchar(200) DEFAULT NULL,
  `wf_filename` text,
  `wf_layout` text,
  `wf_version` decimal(10,4) DEFAULT NULL,
  `logtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;

#
# Dumping data for table t_wf_workflow_xmllog
#

LOCK TABLES `t_wf_workflow_xmllog` WRITE;
/*!40000 ALTER TABLE `t_wf_workflow_xmllog` DISABLE KEYS */;
/*!40000 ALTER TABLE `t_wf_workflow_xmllog` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table v_wf_role
#

DROP TABLE IF EXISTS `v_wf_role`;
CREATE TABLE `v_wf_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `descript` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

#
# Dumping data for table v_wf_role
#

LOCK TABLES `v_wf_role` WRITE;
/*!40000 ALTER TABLE `v_wf_role` DISABLE KEYS */;
INSERT INTO `v_wf_role` VALUES (1,'fina','财务经理','');
INSERT INTO `v_wf_role` VALUES (2,'law','法务经理',NULL);
INSERT INTO `v_wf_role` VALUES (3,'development','研发经理',NULL);
/*!40000 ALTER TABLE `v_wf_role` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table v_wf_role_user
#

DROP TABLE IF EXISTS `v_wf_role_user`;
CREATE TABLE `v_wf_role_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleid` varchar(50) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `salarysn` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

#
# Dumping data for table v_wf_role_user
#

LOCK TABLES `v_wf_role_user` WRITE;
/*!40000 ALTER TABLE `v_wf_role_user` DISABLE KEYS */;
INSERT INTO `v_wf_role_user` VALUES (1,'1','fina','张爽','s006');
INSERT INTO `v_wf_role_user` VALUES (2,'1','fina','赵丽颖','s004');
INSERT INTO `v_wf_role_user` VALUES (3,'2','law','张强','s003');
INSERT INTO `v_wf_role_user` VALUES (4,'3','development','张国栋','s007');
/*!40000 ALTER TABLE `v_wf_role_user` ENABLE KEYS */;
UNLOCK TABLES;

#
# Source for table v_wf_user
#

DROP TABLE IF EXISTS `v_wf_user`;
CREATE TABLE `v_wf_user` (
  `u_usercode` varchar(50) NOT NULL DEFAULT '',
  `u_name` varchar(160) DEFAULT NULL,
  `u_englishname` varchar(50) DEFAULT NULL,
  `u_telephone` varchar(50) DEFAULT NULL,
  `u_departmentId` varchar(50) DEFAULT NULL,
  `u_company` varchar(100) DEFAULT NULL,
  `u_area` varchar(100) DEFAULT NULL,
  `u_email` varchar(100) DEFAULT NULL,
  `u_opptime` datetime DEFAULT NULL,
  PRIMARY KEY (`u_usercode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Dumping data for table v_wf_user
#

LOCK TABLES `v_wf_user` WRITE;
/*!40000 ALTER TABLE `v_wf_user` DISABLE KEYS */;
INSERT INTO `v_wf_user` VALUES ('s001','刘明天',NULL,NULL,NULL,'sina','北京','liusan@sina.com','1899-12-29');
INSERT INTO `v_wf_user` VALUES ('s002','刘四',NULL,NULL,NULL,'ali','浙江','liusi@ali.com',NULL);
INSERT INTO `v_wf_user` VALUES ('s003','张强',NULL,NULL,NULL,'','上海',NULL,NULL);
INSERT INTO `v_wf_user` VALUES ('s004','赵丽颖',NULL,NULL,NULL,NULL,'北京',NULL,NULL);
INSERT INTO `v_wf_user` VALUES ('s006','张爽',NULL,NULL,NULL,NULL,'上海',NULL,NULL);
INSERT INTO `v_wf_user` VALUES ('s007','张国栋',NULL,NULL,NULL,NULL,'北京',NULL,NULL);
INSERT INTO `v_wf_user` VALUES ('s008','李菲',NULL,NULL,NULL,NULL,'北京',NULL,NULL);
INSERT INTO `v_wf_user` VALUES ('s009','黄土良',NULL,NULL,NULL,NULL,'北京',NULL,NULL);
/*!40000 ALTER TABLE `v_wf_user` ENABLE KEYS */;
UNLOCK TABLES;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
