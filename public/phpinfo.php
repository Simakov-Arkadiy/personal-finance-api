<?php

echo 'PHP version: ' . PHP_VERSION . '<br>';
echo 'SAPI: ' . php_sapi_name() . '<br>';
echo 'Loaded php.ini: ' . php_ini_loaded_file() . '<br>';
echo 'Extension dir: ' . ini_get('extension_dir') . '<br>';
echo 'Scanned ini files: ' . php_ini_scanned_files() . '<br>';

echo '<hr>';

echo 'PDO_SQLSRV: ';
var_dump(extension_loaded('pdo_sqlsrv'));

echo 'SQLSRV: ';
var_dump(extension_loaded('sqlsrv'));

echo 'PDO drivers: ';
var_dump(PDO::getAvailableDrivers());

phpinfo();