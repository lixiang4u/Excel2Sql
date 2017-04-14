<?php
/**
 * Created by PhpStorm.
 * User: lixiang4u
 * Date: 2017/4/15
 * Time: 3:22
 */

include_once 'Excel2Sql.php';

$csvFile = 'C:/Users/lixiang4u/Desktop/wp_comments.csv';

$app = new  Excel2Sql($csvFile);
$app->generateSqlFile();






