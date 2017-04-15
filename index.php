<?php
/**
 * Created by PhpStorm.
 * User: lixiang4u
 * Date: 2017/4/15
 * Time: 3:22
 */

include_once 'Excel2Sql.php';
$csvFile   = 'C:/Users/lixiang4u/Desktop/wp_comments.csv';
$excel2Sql = new  Excel2Sql($csvFile);


//$excel2Sql->p($excel2Sql->generateTableStruct());

//$excel2Sql->p($excel2Sql->getTableStruct());

//$excel2Sql->p($excel2Sql->writeSqlFile());
