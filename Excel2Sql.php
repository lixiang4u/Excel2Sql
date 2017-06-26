<?php

/**
 * Created by PhpStorm.
 * User: lixiang4u
 * Date: 2017/4/14
 * Time: 22:04
 */
class Excel2Sql {

	/**
	 * 换行符
	 * @var string
	 */
	private $lineSplit = "\r\n";
	/**
	 * csv文件的分隔符
	 * @var string
	 */
	private $csvSplit = ',';
	/**
	 * 将要解析SQL的csv文件
	 * @var string
	 */
	private $csvFile;
	/**
	 * 生成的目标SQL文件，与csv文件同级目录，命名规则参考构造方法
	 * @var string
	 */
	private $sqlFile;
	/**
	 * 指向当前表名称
	 * @var
	 */
	private $tableCursor;
	/**
	 * 表明当前解析的表是不是第一个
	 * @var bool
	 */
	private $firstTable = true;
	/**
	 * 所有解析的表结构都放到这个数组中
	 * @var array
	 */
	private $parsedTableStruct = array();

	public function __construct($csvFile = __FILE__) {
		$this->csvFile = $csvFile;
		$pathInfo      = pathinfo($this->csvFile);
		$this->sqlFile = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '_' . date('Ymd') . '.sql';
		if ( ! is_file($this->csvFile) ) {
			throw  new Exception('文件不存在');
		}
	}

	/**
	 * 将解析出的SQL结构体转化为SQL语句写入文件
	 * @return string 表名->SQL 字典
	 */
	public function writeSqlFile() {
		$this->generateTableStruct();
		$result = $this->tableStruct2Sql();
		$this->writeFile($this->sqlFile, $this->getFileWriteTitle());
		$this->writeFile($this->sqlFile, $result);
		$this->writeFile($this->sqlFile, $this->getFileWriteTail());
		return $result;
	}

	/**
	 * 只生成文件中描述的表结构（数据存放在内存）,并返回解析的结构体数据
	 * @return array 解析出的表结构体数据
	 */
	public function generateTableStruct() {
		//1、按行读取数据存入结构体
		//2、将 上述结构体放入list
		//3、遇到行解析出表名的则计算上一个$firstTable结构，并解析出SQL语句
		//
		$fp = fopen($this->csvFile, 'r');
		while ($row = fgets($fp)) {
			$this->parseLine($row);
		}
		fclose($fp);
		return $this->parsedTableStruct;
	}

	/**
	 * 获取生成的表结构体数据，默认全部表，参数table表示只当表结构，table指定的数据不存在返回null
	 *
	 * @param bool $table 获解析出多个表指定表名的结构数据
	 *
	 * @return array|mixed 解析出的表结构体数据
	 */
	public function getTableStruct($table = false) {
		$this->generateTableStruct();
		$result = $this->parsedTableStruct;
		if ( ! empty($table) ) {
			$result = $result[$table];
		}
		return $result;
	}

	/**
	 * 解析文件行数据
	 *
	 * @param $str
	 */
	protected function parseLine($str) {
		$str = trim(trim($str), $this->csvSplit);
		if ( empty($str) ) {
			//空行，丢弃
		} elseif ( preg_match("/表名：(\w+)（(\S+)）/", $str, $matches) ) {
			//有用
			$this->tableCursor = isset($matches[1]) ? $matches[1] : '';

			//判断扫描行的表名是不是第一次出现，不是第一次需要重置数据
			if ( $this->firstTable === true ) {
				$this->firstTable = false;
			} else {
				$this->parsedTableStruct[$this->tableCursor] = $this->getDefaultTableStruct();;
			}

			//数据解析并存储
			$this->parsedTableStruct[$this->tableCursor]['tableName'] = $this->tableCursor;
			$this->parsedTableStruct[$this->tableCursor]['comment']   = isset($matches[2]) ? $matches[2] : '';
		} elseif ( preg_match("/引擎：(\w+)/", $str, $matches) ) {
			$this->parsedTableStruct[$this->tableCursor]['engine'] = isset($matches[1]) ? $matches[1] : '';
			if ( preg_match("/编码：(\w+)/", $str, $matches) ) {
				$this->parsedTableStruct[$this->tableCursor]['charset'] = isset($matches[1]) ? $matches[1] : '';
			}
		} elseif ( preg_match("/^字段名/", $str, $matches) ) {
			//标题行，丢弃
		} else {
			//或许是列数据
			$columnStruct = $this->parseFieldLine(explode($this->csvSplit, $str));
			$columnStruct ? ($this->parsedTableStruct[$this->tableCursor]['fields'][] = $columnStruct) : null;
		}
	}

	/**
	 * 解析的数据表的数据结构体
	 * @return array
	 */
	protected function getDefaultTableStruct() {
		return array(
			'tableName' => '',//表名：
			'engine'    => '',//引擎：
			'charset'   => '',//编码：
			'fields'    => array(),//参见 getDefaultColumnStruct
			'comment'   => '',//表备注信息
		);
	}

	/**
	 * 解析的数据列（字段）的数据结构体
	 * @return array
	 */
	protected function getDefaultColumnStruct() {
		//字段名	类型	属性	默认值	整理类型	空	额外	索引	备注
		return array(
			'name'         => '',//字段名
			'type'         => '',//类型
			'attr'         => '',//属性
			'defaultValue' => '',//默认值
			'charset'      => '',//整理类型
			'isNull'       => '',//空
			'extra'        => '',//额外
			'index'        => '',//索引，存放
			'comment'      => '',//备注
		);
	}

	/**
	 * 数据映射
	 *
	 * @param $fields
	 *
	 * @return array|bool
	 */
	protected function parseFieldLine($fields) {
		$flag         = false;
		$columnStruct = $this->getDefaultColumnStruct();
		if ( count($fields) >= 9 ) {
			$flag                         = true;
			$columnStruct['name']         = $fields[0];
			$columnStruct['type']         = $fields[1];
			$columnStruct['attr']         = $fields[2];
			$columnStruct['defaultValue'] = $fields[3];
			$columnStruct['charset']      = $fields[4];
			$columnStruct['isNull']       = $fields[5];
			$columnStruct['extra']        = $fields[6];
			$columnStruct['index']        = $fields[7];
			$columnStruct['comment']      = $fields[8];
		}
		return $flag ? $columnStruct : $flag;
	}

	/**
	 * 根据表数据的结构欧体拼接SQL语句
	 *
	 * @param $tableStruct
	 *
	 * @return string
	 */
	protected function buildCreateTableSql($tableStruct) {
		//1、必要字段空检测
		//2、拼接列描述
		//3、拼接主键
		//4、拼接索引
		//5、拼接整体SQL

		$tableSqlStr = $fieldSqlStr = $primaryKeyStr = $indexStr = $uniqueStr = $tmpStr = '';
		$primaryKey  = $index = $unique = array();

		foreach ($tableStruct['fields'] as $field) {

			$field['index'] = strtoupper($field['index']);
			//索引信息转储
			if ( $field['index'] == 'PRIMARY_KEY' ) {
				$primaryKey[] = $field['name'];
			} elseif ( ! empty($field['index']) && $field['index'] == 'INDEX' ) {
				$index[] = $field['name'];
			} elseif ( ! empty($field['index']) && $field['index'] == 'UNIQUE' ) {
				$unique[] = $field['name'];
			}

			//字段
			$fieldSqlStr .= "  `{$field['name']}` ";
			$fieldSqlStr .= strtoupper("{$field['type']} ");
			$fieldSqlStr .= strtoupper("{$field['attr']} ");


			//不为空，默认值
			if ( $field['isNull'] == '否' && $field['defaultValue'] == '空字符串' ) {
				$fieldSqlStr .= "NOT NULL DEFAULT '' ";
			} elseif ( $field['isNull'] == '否' && $field['defaultValue'] == '无' ) {
				$fieldSqlStr .= "NOT NULL ";
			} elseif ( $field['isNull'] == '否' ) {
				$fieldSqlStr .= "NOT NULL DEFAULT '{$field['defaultValue']}' ";
			}

			if ( $field['extra'] ) {
				$fieldSqlStr .= "{$field['extra']} ";
			}

			$fieldSqlStr .= "COMMENT '{$field['comment']}',";
			$fieldSqlStr .= $this->lineSplit;
		}

		//索引拼接
		if ( ! empty($primaryKey) ) {
			$tmpStr        = implode('`,`', $primaryKey);
			$primaryKeyStr = "  PRIMARY KEY (`{$tmpStr}`)," . $this->lineSplit;
		}
		if ( ! empty($index) ) {
			foreach ($index as $item) {
				$indexStr .= "  KEY `idx_{$tableStruct['tableName']}_{$item}` (`{$item}`)," . $this->lineSplit;
			}
		}
		if ( ! empty($unique) ) {
			foreach ($unique as $item) {
				$uniqueStr .= "  UNIQUE KEY `uni_{$tableStruct['tableName']}_{$item}` (`{$item}`)," . $this->lineSplit;
			}
		}
		//拼接列字段和索引
		$tmpStr = trim($fieldSqlStr . $primaryKeyStr . $indexStr . $uniqueStr, ',' . $this->lineSplit);

		//表引擎和字符集拼接
		$tableEngine  = isset($tableStruct['engine']) ? 'ENGINE=' . $tableStruct['engine'] : 'ENGINE=InnoDB';
		$tableCharset = isset($tableStruct['charset']) ? 'DEFAULT CHARSET=' . $tableStruct['charset'] : 'DEFAULT CHARSET=utf8';

		//表整体结构拼接
		$tableSqlStr .= '' . "DROP TABLE IF EXISTS `{$tableStruct['tableName']}`;" . $this->lineSplit;
		$tableSqlStr .= '' . "CREATE TABLE `{$tableStruct['tableName']}` (" . $this->lineSplit;
		$tableSqlStr .= '' . $tmpStr . $this->lineSplit;
		$tableSqlStr .= '' . ") {$tableEngine} {$tableCharset} COMMENT '{$tableStruct['comment']}';" . $this->lineSplit;
		$tableSqlStr .= '' . $this->lineSplit;

		return $tableSqlStr;
	}

	protected function tableStruct2Sql() {
		$sql = array();
		foreach ($this->parsedTableStruct as $key => $item) {
			$sql[$key] = $this->buildCreateTableSql($item);
		}
		return $sql;
	}


	/**
	 * 写文件
	 *
	 * @param        $fileName
	 * @param string $data
	 * @param string $mode
	 *
	 * @return int
	 */
	protected function writeFile($fileName, $data, $mode = 'a+') {
		$fp     = fopen($fileName, $mode);
		$result = fwrite($fp, is_array($data) ? implode($this->lineSplit, $data) : $data);
		fclose($fp);
		return $result;
	}

	/**
	 * @return string
	 */
	protected function getFileWriteTitle() {
		$time = date('Y-m-d H:i');
		return <<<EOL
/**
 * Created by Excel2Sql
 * Author: lixiang4u
 * Date: 2017-04-15
 *
 * This file is auto generated.
 * Generate Time: {$time}
 */


EOL;

	}


	/**
	 * @return string
	 */
	protected function getFileWriteTail() {
		return <<<EOL

##########################
#### END OF FILE
##########################

EOL;

	}

	/**
	 * 打印数据
	 *
	 * @param      $val
	 * @param bool $isJson
	 */
	public function p($val, $isJson = true) {
		if ( $isJson ) {
			header('Content-type: application/json');
			echo json_encode($val);
		} else {
			echo '<pre>';
			print_r($val);
			echo '</pre>';
		}
	}

}
