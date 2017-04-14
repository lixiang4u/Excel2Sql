/**
 * Created by Excel2Sql
 * Author: lixiang4u
 * Date: 2017-04-15
 *
 * This file is auto generated.
 * Generate Time: 2017-04-14 19:22
 */

DROP TABLE IF EXISTS `wp_comments`;
CREATE TABLE `wp_comments` (
  `comment_ID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增唯一ID',
  `comment_post_ID` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '对应文章ID',
  `comment_author` VARCHAR(255)  NOT NULL DEFAULT '' COMMENT '评论者',
  `comment_author_email` VARCHAR(255)  NOT NULL DEFAULT '' COMMENT '评论者邮箱',
  `comment_author_url` VARCHAR(255)  NOT NULL DEFAULT '' COMMENT '评论者网址',
  `comment_author_IP` VARCHAR(255)  NOT NULL DEFAULT '' COMMENT '评论者IP',
  `comment_date` DATETIME  NOT NULL DEFAULT '0' COMMENT '评论时间',
  `comment_content` TEXT  NOT NULL DEFAULT '' COMMENT '评论正文',
  `comment_parent` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父评论ID',
  `user_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '评论者用户ID（不一定存在）',
  PRIMARY KEY (`comment_ID`),
  KEY `idx_wp_comments_comment_post_ID` (`comment_post_ID`),
  KEY `idx_wp_comments_comment_author` (`comment_author`),
  KEY `idx_wp_comments_comment_parent` (`comment_parent`),
  KEY `idx_wp_comments_user_id` (`user_id`)
) ENGINE=InnoDB  COMMENT `评论信息表`;


##########################
#### END OF FILE
##########################
