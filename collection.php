<style>
.sitecatul li.cat ul{padding:0 0 0 30px;}
.sitecatul li.cat ul li { list-style: outside none none !important; }
ul.sitecatul li.cat { width: 25%; }
.sitecatul li .strongsm { display:inline-block; }

.treesitemap ul { list-style: none outside none; }
.treesitemap li a { line-height: 25px; }
.treesitemap > ul > li > a {
    color: #3B4C56;
    display: block;
    font-weight: normal;
    position: relative;
    text-decoration: none;
}
.sitecatul.treesitemap > li >  ul{position:relative;}
.sitecatul.treesitemap > li > ul::after {
  border-left: 1px solid #d9dadb;
  bottom: 0;
  content: "";
  left: -5px;
  margin-left: 12px;
  position: absolute;
  top: -5px;
}
.treesitemap li.parent > a {
    padding: 0px;
}
.treesitemap li.parent > span:before{
    background-image: url("../images/plus_minus_icons.png");
    background-position: 0 0;
    content: ""; 
    display: inline-block;
    height: 25px;
    vertical-align: -6px;
    width: 25px;
	position:relative;
	z-index:99;
	cursor:pointer
}
.treesitemap li li.parent > span:before{
vertical-align:-7px;
}
.treesitemap li.parent > span.active:before{
  background-position: 25px center;
}

.treesitemap ul li ul {
    border-left: 1px solid #D9DADB;
    display: none;
    margin: 0 0 0 12px;
    overflow: hidden;
    padding: 0 0 0 25px;
}
.treesitemap ul li ul li {
    position: relative;
}
.sitecatul li.cat ul li{position:relative;margin-left: 25px;}
.sitecatul li.cat ul li:after {
    border-bottom: 1px dashed #E2E2E3;
    content: "";
    left: -20px;
    position: absolute;
    top: 12px;
    width: 15px;
}
ul.sitecatul li.cat:nth-of-type(4n+1) {clear: left;}
@media (max-width:1199px) {
ul.sitecatul li.cat {
  width: 33.33%;
}
ul.sitecatul li.cat:nth-of-type(4n+1) {clear: none;}
ul.sitecatul li.cat:nth-of-type(3n+1) {clear: left;}
}
@media (max-width:991px) {
ul.sitecatul li.cat {
  width: 50%;
}
ul.sitecatul li.cat:nth-of-type(3n+1) {clear: none;}
ul.sitecatul li.cat:nth-of-type(2n+1) {clear: left;}
}
@media (max-width:767px) {
ul.sitecatul li.cat {
  width: 100%;
}
}

</style>
<?php
ini_set('display_errors', 1);

use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';


$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManagerr = $bootstrap->getObjectManager();
$categoryFactory = $objectManagerr->get('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
$categoriesArray = $categoryFactory->create()->addAttributeToSelect(['name','url_path'])
->addAttributeToSort('path', 'asc')
->addFieldToFilter('is_active', '1')
->load();?>
<ul class="sitemap">
<?php
foreach ($categoriesArray as $category) {
if (isset($category['name'])) {
$dash='';
for($i=2;$i<=$category['level'];$i++)
$dash.='-- ';
$category['name']=$dash.$category['name'];
$categories[] = $category['name'];
$categoruUrl[] = $category['url_path'];?>
<a href="<?php echo $category['url_path']; ?>"><?php echo "<pre>"; echo $category['name']; ?></a>
</ul>
<?php 
}
}

//echo "<pre>";
//print_r($categories); ?>


