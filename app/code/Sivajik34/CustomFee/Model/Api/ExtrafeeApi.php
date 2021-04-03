<?php
namespace Sivajik34\CustomFee\Model\Api;
use Sivajik34\CustomFee\Api\ExtrafeeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class ExtrafeeApi  extends AbstractHelper implements ExtrafeeInterface
{
    /**
     * Returns greeting message to user
     *
     * @api
     * @param string $name Users name.
     * @return string Greeting message with users name.
     */


    const CONFIG_CUSTOM_IS_ENABLED = 'customfee/customfee/status';
    const CONFIG_CUSTOM_FEE = 'customfee/customfee/customfee_amount';
    const CONFIG_FEE_LABEL = 'customfee/customfee/name';
    const CONFIG_CUSTOM_FEE_SEARCH = 'customfee/customfee/';
    const CONFIG_FEE_LABEL_SEARCH = 'customfee/customfee/';
    const CONFIG_MINIMUM_ORDER_AMOUNT = 'customfee/customfee/minimum_order_amount';


    public function getList($name='') {
        $array=array();
        $array['res']['is_eabled']=0;
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $isEnabled = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_IS_ENABLED, $storeScope);

        $MinimumOrderAmount = $this->scopeConfig->getValue(self::CONFIG_MINIMUM_ORDER_AMOUNT, $storeScope);
        $array['res']['minimum_order_amount']=(int)trim($MinimumOrderAmount);
        $array['res']['is_eabled']=$isEnabled;
        if($array['res']['is_eabled']){
        $array['res']['data']=array();

        for($index=1;$index<=5;$index++){
            if($index==1){
                $fee = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_FEE, $storeScope);     
                $lable = $this->scopeConfig->getValue(self::CONFIG_FEE_LABEL, $storeScope);     
            }else{
                $fee = $this->scopeConfig->getValue(self::CONFIG_CUSTOM_FEE.$index, $storeScope);     
                $lable = $this->scopeConfig->getValue(self::CONFIG_FEE_LABEL.$index, $storeScope);     
            }
            $fee=trim($fee);
            $lable=trim($lable);
            if($lable!==''){
                if(empty($fee))
                    $fee=0;
                $i_array['id']=$index;
                $i_array['fee']=$fee;
                $i_array['lable']=$lable;
                $array['res']['data'][]=$i_array;
            }

        }
        }
        return $array;
            
         
    }
}
