<?php 
namespace Wds\ReviewNotify\Block;
 
class CountryList extends \Magento\Framework\View\Element\Template
{
    protected $directoryBlock;
    protected $_isScopePrivate;
    
    public function __construct(
         	\Magento\Framework\View\Element\Template\Context $context,
         	\Magento\Directory\Block\Data $directoryBlock,
         	array $data = []
        	)
    {
         	parent::__construct($context, $data);
         	$this->_isScopePrivate = true;
         	$this->directoryBlock = $directoryBlock;
    }
 
	public function getCountries()
	{
     	$country = $this->directoryBlock->getCountryHtmlSelect();
 	    return $country;
	}
	public function getRegion()
	{
        $region = $this->directoryBlock->getRegionHtmlSelect();
        return $region;
	}
	 public function getCountryAction()
	{
 	  return $this->getUrl('extension/extension/country', ['_secure' => true]);
	}
}