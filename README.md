# Excel2Sql

将excel格式的数据库表格转换为sql语句。

> * 需要一份符合格式的excel数据表文件
> * 需要将excel转换为csv（逗号分割，UTF-8格式）文件
> * 使用**Excel2Sql**提供的**writeSqlFile()**实例方法
> * 最终将得到一份**sql**文件


## excel格式
1、第一行为表名信息
> 表名：wp_comments（博客评论表）

其中**表名：\$tableName（\$tableComment）**为不变的格式
除可替换变量外，均为中文输入字符

2、紧接着是数据表引擎、字符集描述
> 引擎：InnoDB，编码：utf8_general_ci

其中**引擎：\$engine，编码：\$charset**为不变的格式
除可替换变量外，均为中文输入字符
若不指定则表示无此要求


3、接下来是数据表列字段标题

|字段名	|类型	|属性	|默认值	|整理类型	|空	|额外	|索引	|备注
|----	|----	|----	|----	|----	|----	|----	|----	|----

> 1、字段名：对应数据表字段名

> 2、类型：字段的数据类型，例如**VARCHAR(255)**，**INT(10)**

> 3、属性：当数据类型是整形时，此处可填入**UNSIGNED**表示无符号，否则空

> 4、默认值：**无**表示没有默认值，当类型是字符串时，可填入**空字符串**表示默认空字符串，（默认值暂不支持**NOW()**等数据库内置函数），其它值会默认反应到生成的SQL语句中

> 5、整理类型：默认**utf8_general_ci**

> 6、空：表示当前字段是否为空，**否**表示不为空，对应sql中**NOT NULL**语法

> 7、额外：只有当用于**AUTO_INCREMENT**自增时填入**AUTO_INCREMENT**

> 8、索引：填入**primary_key**表示当前列为主键，如果有多个表示liane主键；填入**index**表示当前列为默认索引键，填入**unique**表示当前列为唯一键索引，目前没有支持联合索引

> 9、备注：当前字段的备注信息，对应SQL的COMMENT语法

4、重复上述格式代表多个数据表


## 使用方法

1、定义csv文件路径
``` javascript
$csvFile = 'C:/Users/lixiang4u/Desktop/wp_comments.csv';
```

2、实例对象
``` javascript
$excel2Sql = new Excel2Sql($csvFile);
```


3、执行生成操作
``` javascript
Object $excel2Sql->generateTableStruct();
```
此方法将对csv文件进行解析，并得到解析后的表结构体数据并返回


4、获取解析结果中指定表结构体数据
``` javascript
Object $excel2Sql->getTableStruct($tableName = false);
```
此方法将基于**generateTableStruct()**的结果返回指定表结构体数据，如果**$tableName**为空则返回值同**generateTableStruct()**。


5、将解析生成的结果转为SQL语句并写入文件
``` javascript
Object $excel2Sql->writeSqlFile();
```
此方法将解析生成的表信息转化为SQL语句，并写入文件，返回值是**表名->表SQL语句**形式的键值对。


## 示例
1、参考**wp_comments.xlsx**数据表文件

2、生成表结构体数据如下：
``` javascript
{
  "wp_comments": {
    "tableName": "wp_comments",
    "comment": "评论信息表",
    "engine": "InnoDB",
    "fields": [
      {
        "name": "comment_ID",
        "type": "INT(10)",
        "attr": "unsigned",
        "defaultValue": "无",
        "charset": "utf8_general_ci",
        "isNull": "否",
        "extra": "AUTO_INCREMENT",
        "index": "primary_key",
        "comment": "自增唯一ID"
      },
      {
        "name": "comment_post_ID",
        "type": "INT(10)",
        "attr": "unsigned",
        "defaultValue": "0",
        "charset": "utf8_general_ci",
        "isNull": "否",
        "extra": "",
        "index": "index",
        "comment": "对应文章ID"
      },
      {
        "name": "comment_author",
        "type": "VARCHAR(255)",
        "attr": "",
        "defaultValue": "空字符串",
        "charset": "utf8_general_ci",
        "isNull": "否",
        "extra": "",
        "index": "index",
        "comment": "评论者"
      },
      {
        "name": "comment_author_email",
        "type": "VARCHAR(255)",
        "attr": "",
        "defaultValue": "空字符串",
        "charset": "utf8_general_ci",
        "isNull": "否",
        "extra": "",
        "index": "",
        "comment": "评论者邮箱"
      },
      {
        "name": "comment_author_url",
        "type": "VARCHAR(255)",
        "attr": "",
        "defaultValue": "空字符串",
        "charset": "utf8_general_ci",
        "isNull": "否",
        "extra": "",
        "index": "",
        "comment": "评论者网址"
      },
      {
        "name": "comment_author_IP",
        "type": "VARCHAR(255)",
        "attr": "",
        "defaultValue": "空字符串",
        "charset": "utf8_general_ci",
        "isNull": "否",
        "extra": "",
        "index": "",
        "comment": "评论者IP"
      },
      {
        "name": "comment_date",
        "type": "DATETIME",
        "attr": "",
        "defaultValue": "0",
        "charset": "utf8_general_ci",
        "isNull": "否",
        "extra": "",
        "index": "",
        "comment": "评论时间"
      },
      {
        "name": "comment_content",
        "type": "TEXT",
        "attr": "",
        "defaultValue": "空字符串",
        "charset": "utf8_general_ci",
        "isNull": "否",
        "extra": "",
        "index": "",
        "comment": "评论正文"
      },
      {
        "name": "comment_parent",
        "type": "INT(10)",
        "attr": "unsigned",
        "defaultValue": "0",
        "charset": "utf8_general_ci",
        "isNull": "否",
        "extra": "",
        "index": "index",
        "comment": "父评论ID"
      },
      {
        "name": "user_id",
        "type": "INT(10)",
        "attr": "unsigned",
        "defaultValue": "0",
        "charset": "utf8_general_ci",
        "isNull": "否",
        "extra": "",
        "index": "index",
        "comment": "评论者用户ID（不一定存在）"
      }
    ]
  }
}
```

2、生成结果如下：

``` sql
/**
 * Created by Excel2Sql
 * Author: lixiang4u
 * Date: 2017-04-15
 *
 * This file is auto generated.
 * Generate Time: 2017-04-14 19:17
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

```


## 注意
> 1、目前没有支持联合主键的情况


---丸---
