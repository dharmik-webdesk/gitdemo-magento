<?php
error_reporting(E_ALL);
//header('Content-Encoding: none;');
set_time_limit(0);
$handle = popen("php /opt/bitnami/apps/magento/htdocs/bin/magento indexer:reindex", "r");

if (ob_get_level() == 0)
    ob_start();

while(!feof($handle)) {

    $buffer = fgets($handle);
    $buffer = trim(htmlspecialchars($buffer));

    echo $buffer . "<br />";
    echo str_pad('', 4096);

    ob_flush();
    flush();
    sleep(1);
}

pclose($handle);
ob_end_flush();
?>