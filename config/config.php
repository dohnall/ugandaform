<?php
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge,chrome=1');

function __autoload($classname) {
  	if(file_exists(CLASSES.$classname.'.php')) {
    	require_once CLASSES.$classname.'.php';
  	
	} elseif(substr(strtolower($classname), 0, 4) == 'dibi') {
		// preload for compatiblity
		array_map('class_exists', [
			'DibiConnection',
			'DibiDateTime',
			'DibiDriverException',
			'DibiEvent',
			'DibiException',
			'DibiFluent',
			'DibiLiteral',
			'DibiNotImplementedException',
			'DibiNotSupportedException',
			'DibiPcreException',
			'DibiProcedureException',
			'DibiResult',
			'DibiRow',
			'IDataSource',
			'IDibiDriver',
		]);
		static $map = [
			'dibi' => 'dibi.php',
			'Dibi\Bridges\Nette\DibiExtension22' => 'Bridges/Nette/DibiExtension22.php',
			'Dibi\Bridges\Nette\DibiExtension21' => 'Bridges/Nette/DibiExtension21.php',
			'Dibi\Bridges\Nette\Panel' => 'Bridges/Nette/Panel.php',
			'Dibi\Bridges\Tracy\Panel' => 'Bridges/Tracy/Panel.php',
			'Dibi\Connection' => 'Connection.php',
			'Dibi\DataSource' => 'DataSource.php',
			'Dibi\DateTime' => 'DateTime.php',
			'Dibi\Driver' => 'interfaces.php',
			'Dibi\DriverException' => 'exceptions.php',
			'Dibi\Drivers\FirebirdDriver' => 'Drivers/FirebirdDriver.php',
			'Dibi\Drivers\SqlsrvDriver' => 'Drivers/SqlsrvDriver.php',
			'Dibi\Drivers\SqlsrvReflector' => 'Drivers/SqlsrvReflector.php',
			'Dibi\Drivers\MsSqlDriver' => 'Drivers/MsSqlDriver.php',
			'Dibi\Drivers\MsSqlReflector' => 'Drivers/MsSqlReflector.php',
			'Dibi\Drivers\MySqlDriver' => 'Drivers/MySqlDriver.php',
			'Dibi\Drivers\MySqliDriver' => 'Drivers/MySqliDriver.php',
			'Dibi\Drivers\MySqlReflector' => 'Drivers/MySqlReflector.php',
			'Dibi\Drivers\OdbcDriver' => 'Drivers/OdbcDriver.php',
			'Dibi\Drivers\OracleDriver' => 'Drivers/OracleDriver.php',
			'Dibi\Drivers\PdoDriver' => 'Drivers/PdoDriver.php',
			'Dibi\Drivers\PostgreDriver' => 'Drivers/PostgreDriver.php',
			'Dibi\Drivers\Sqlite3Driver' => 'Drivers/Sqlite3Driver.php',
			'Dibi\Drivers\SqliteReflector' => 'Drivers/SqliteReflector.php',
			'Dibi\Event' => 'Event.php',
			'Dibi\Exception' => 'exceptions.php',
			'Dibi\Fluent' => 'Fluent.php',
			'Dibi\HashMap' => 'HashMap.php',
			'Dibi\HashMapBase' => 'HashMap.php',
			'Dibi\Helpers' => 'Helpers.php',
			'Dibi\IDataSource' => 'interfaces.php',
			'Dibi\Literal' => 'Literal.php',
			'Dibi\Loggers\FileLogger' => 'Loggers/FileLogger.php',
			'Dibi\Loggers\FirePhpLogger' => 'Loggers/FirePhpLogger.php',
			'Dibi\NotImplementedException' => 'exceptions.php',
			'Dibi\NotSupportedException' => 'exceptions.php',
			'Dibi\PcreException' => 'exceptions.php',
			'Dibi\ProcedureException' => 'exceptions.php',
			'Dibi\Reflection\Column' => 'Reflection/Column.php',
			'Dibi\Reflection\Database' => 'Reflection/Database.php',
			'Dibi\Reflection\ForeignKey' => 'Reflection/ForeignKey.php',
			'Dibi\Reflection\Index' => 'Reflection/Index.php',
			'Dibi\Reflection\Result' => 'Reflection/Result.php',
			'Dibi\Reflection\Table' => 'Reflection/Table.php',
			'Dibi\Reflector' => 'interfaces.php',
			'Dibi\Result' => 'Result.php',
			'Dibi\ResultDriver' => 'interfaces.php',
			'Dibi\ResultIterator' => 'ResultIterator.php',
			'Dibi\Row' => 'Row.php',
			'Dibi\Strict' => 'Strict.php',
			'Dibi\Translator' => 'Translator.php',
			'Dibi\Type' => 'Type.php',
		], $old2new = [
			'Dibi' => 'dibi.php',
			'DibiColumnInfo' => 'Dibi\Reflection\Column',
			'DibiConnection' => 'Dibi\Connection',
			'DibiDatabaseInfo' => 'Dibi\Reflection\Database',
			'DibiDataSource' => 'Dibi\DataSource',
			'DibiDateTime' => 'Dibi\DateTime',
			'DibiDriverException' => 'Dibi\DriverException',
			'DibiEvent' => 'Dibi\Event',
			'DibiException' => 'Dibi\Exception',
			'DibiFileLogger' => 'Dibi\Loggers\FileLogger',
			'DibiFirebirdDriver' => 'Dibi\Drivers\FirebirdDriver',
			'DibiFirePhpLogger' => 'Dibi\Loggers\FirePhpLogger',
			'DibiFluent' => 'Dibi\Fluent',
			'DibiForeignKeyInfo' => 'Dibi\Reflection\ForeignKey',
			'DibiHashMap' => 'Dibi\HashMap',
			'DibiHashMapBase' => 'Dibi\HashMapBase',
			'DibiIndexInfo' => 'Dibi\Reflection\Index',
			'DibiLiteral' => 'Dibi\Literal',
			'DibiMsSql2005Driver' => 'Dibi\Drivers\SqlsrvDriver',
			'DibiMsSql2005Reflector' => 'Dibi\Drivers\SqlsrvReflector',
			'DibiMsSqlDriver' => 'Dibi\Drivers\MsSqlDriver',
			'DibiMsSqlReflector' => 'Dibi\Drivers\MsSqlReflector',
			'DibiMySqlDriver' => 'Dibi\Drivers\MySqlDriver',
			'DibiMySqliDriver' => 'Dibi\Drivers\MySqliDriver',
			'DibiMySqlReflector' => 'Dibi\Drivers\MySqlReflector',
			'DibiNette21Extension' => 'Dibi\Bridges\Nette\DibiExtension21',
			'DibiNettePanel' => 'Dibi\Bridges\Nette\Panel',
			'DibiNotImplementedException' => 'Dibi\NotImplementedException',
			'DibiNotSupportedException' => 'Dibi\NotSupportedException',
			'DibiOdbcDriver' => 'Dibi\Drivers\OdbcDriver',
			'DibiOracleDriver' => 'Dibi\Drivers\OracleDriver',
			'DibiPcreException' => 'Dibi\PcreException',
			'DibiPdoDriver' => 'Dibi\Drivers\PdoDriver',
			'DibiPostgreDriver' => 'Dibi\Drivers\PostgreDriver',
			'DibiProcedureException' => 'Dibi\ProcedureException',
			'DibiResult' => 'Dibi\Result',
			'DibiResultInfo' => 'Dibi\Reflection\Result',
			'DibiResultIterator' => 'Dibi\ResultIterator',
			'DibiRow' => 'Dibi\Row',
			'DibiSqlite3Driver' => 'Dibi\Drivers\Sqlite3Driver',
			'DibiSqliteReflector' => 'Dibi\Drivers\SqliteReflector',
			'DibiTableInfo' => 'Dibi\Reflection\Table',
			'DibiTranslator' => 'Dibi\Translator',
			'IDataSource' => 'Dibi\IDataSource',
			'IDibiDriver' => 'Dibi\Driver',
			'IDibiReflector' => 'Dibi\Reflector',
			'IDibiResultDriver' => 'Dibi\ResultDriver',
			'Dibi\Drivers\MsSql2005Driver' => 'Dibi\Drivers\SqlsrvDriver',
			'Dibi\Drivers\MsSql2005Reflector' => 'Dibi\Drivers\SqlsrvReflector',
		];

		if (isset($map[$classname])) {
			require CLASSES . 'Dibi' . DS . $map[$classname];
		} elseif (isset($old2new[$classname])) {
			class_alias($old2new[$classname], $classname);
		}
	} elseif(substr(strtolower($classname), 0, 6) == 'smarty') {
	    $_class = strtolower($classname);
	    $_classes = array(
	        'smarty_config_source' => true,
	        'smarty_config_compiled' => true,
	        'smarty_security' => true,
	        'smarty_cacheresource' => true,
	        'smarty_cacheresource_custom' => true,
	        'smarty_cacheresource_keyvaluestore' => true,
	        'smarty_resource' => true,
	        'smarty_resource_custom' => true,
	        'smarty_resource_uncompiled' => true,
	        'smarty_resource_recompiled' => true,
	    );
	
	    if (!strncmp($_class, 'smarty_internal_', 16) || isset($_classes[$_class])) {
	        include SMARTY_SYSPLUGINS_DIR . $_class . '.php';
	    }
  	} elseif(substr(strtolower($classname), 0, 8) == 'phpexcel') {
        if ((class_exists($classname,FALSE)) || (strpos($classname, 'PHPExcel') !== 0)) {
            //    Either already loaded, or not a PHPExcel class request
            return FALSE;
        }

        $pClassFilePath = CLASSES .
                          str_replace('_',DIRECTORY_SEPARATOR,$classname) .
                          '.php';

        if ((file_exists($pClassFilePath) === FALSE) || (is_readable($pClassFilePath) === FALSE)) {
            //    Can't load
            return FALSE;
        }

        require($pClassFilePath);
	} else {
    	return false;
  	}
}

function d() {
    $arg_num = func_num_args();
    if($arg_num > 0) {
        $arg_list = func_get_args();
        foreach($arg_list as $arg) {
            echo '<xmp>'; var_dump($arg); echo '</xmp>';
        }
        exit;
    }
}

mb_internal_encoding('UTF-8');
setlocale(LC_ALL, 'cs_CZ.UTF8');
date_default_timezone_set('Europe/Prague');
/*
iconv_set_encoding('internal_encoding', 'UTF-8');
iconv_set_encoding('input_encoding', 'UTF-8');
iconv_set_encoding('output_encoding', 'UTF-8');
*/
if($_SERVER['SERVER_ADDR']=='127.0.0.1' || $_SERVER['SERVER_ADDR']=='::1') {
	error_reporting(0);
	$DEBUG = true;
} else {
	error_reporting(0);
	$DEBUG = false;
}

require_once 'config.local.php';

define('DS', DIRECTORY_SEPARATOR);
define('LOCAL', dirname(dirname(__FILE__)).DS);

define('DESIGN', ROOT.'design/'.$ENV.'/');
define('JS', ROOT.'js/'.$ENV.'/');

define('CLASSES', LOCAL.'classes'.DS);
define('CONFIG', LOCAL.'config'.DS);
define('LOG', LOCAL.'log'.DS);
define('FILES', LOCAL.'files'.DS);
define('SCRIPTS', LOCAL.'scripts'.DS.$ENV.DS);
define('TEMPLATES', LOCAL.'templates'.DS.$ENV.DS);
define('TEMPLATES_C', LOCAL.'templates_c'.DS);
