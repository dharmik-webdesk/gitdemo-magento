<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * require __DIR__ . '/app/bootstrap.php';
 * $params = $_SERVER;
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'website2';
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
 * $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
 * \/** @var \Magento\Framework\App\Http $app *\/
 * $app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
 * $bootstrap->run($app);
 * --------------------------------------------
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* $id= $_SERVER['REMOTE_ADDR'];
$html='<html style="height:100%">
<body style="background:url(\'under-maintance.jpg\') no-repeat center center;height:100%;background-size:50%">
<h2 style="text-align: center;font-family: airal; font-size: 27px;width: 50%;margin: auto;font-family: arial;">Our Site is in for routine maintenance and upgrades, please call <a href="tel:866.778.6572" style="color:black">866.778.6572</a> to contact one of our experts<h2>
<div style="display:none">'.$id.'</div>
</body>
</html>';
if($id == '91.207.175.207' || $id == '27.61.191.57' || $id == '117.198.175.74' || $id == '106.76.90.30' || $id=='194.59.251.42' || $id=='117.215.41.190' || $id=='157.32.223.196' || $id=='106.77.71.195' || $id == '91.207.175.198' || $id == '117.198.163.204' || $id == '157.32.229.173' || $id == '157.32.155.45'){

}else{
echo $html;
exit;
} */

//ini_set('display_errors','on');
//error_reporting(E_ALL);

try {
	require __DIR__ . '/app/bootstrap.php';
} catch (\Exception $e) {
	echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
	exit(1);
}
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
$bootstrap->run($app);
