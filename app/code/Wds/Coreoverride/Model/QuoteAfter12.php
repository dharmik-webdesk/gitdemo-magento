<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wds\Coreoverride\Model;

class QuoteAfter
{
    

    /*public function __construct(
        \Magento\Framework\App\Request\Http $request
    ){
        $this->request = $request;
    }
    
    public function afterValidateMinimumAmount()
    {
        $actionName = $this->request->getActionName();
       
        if($actionName === 'delete') {
            return true;
        }
    } */
    
    CONST DELETE_ACTION_NAME = 'delete';
	 CONST ADD_ACTION_NAME = 'add';
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(\Magento\Framework\App\RequestInterface $request)
    {
        $this->request = $request;
    }


    public function afterValidateMinimumAmount()
    {
        $actionName = $this->request->getActionName();
        if($actionName === self::DELETE_ACTION_NAME) {    
            return true;
        }
		/* if($actionName === self::ADD_ACTION_NAME) {
            return true;
        }  */
		
    }
}



