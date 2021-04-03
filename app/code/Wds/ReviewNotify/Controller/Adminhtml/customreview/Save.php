<?php
namespace Wds\ReviewNotify\Controller\Adminhtml\customreview;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Model\Auth\Session;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
   protected $_storeManager;
   protected $_productManager;
   protected $_transportBuilder;
   protected $_scopeConfigInterface;
   protected $_stateInterface;
   protected $_filesystem;
   protected $authSession;
   protected $helper;
   
   

   public function __construct(Action\Context $context,
  			\Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wds\ReviewNotify\Helper\Data $helper
        )
    {
        $this->_storeManager = $storeManager;
        $this->authSession = $authSession;
        $this->helper=$helper;
        parent::__construct($context);
        
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */



	
    public function execute(){
      $data = $this->getRequest()->getPostValue();



      

		  /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
      $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $admin_user_id= $this->authSession->getUser()->getId();
            $model = $this->_objectManager->create('Wds\ReviewNotify\Model\Customreview');
	    	    $id = $this->getRequest()->getParam('id');
            $data['store_id'] = 0;
            $model2 = $model->getCollection();


            $serch_data= $model2->addFieldToFilter('product_id',$data['product_id'])
                                ->addFieldToFilter('status',0)
                                ->addFieldToFilter('customer_email',$data['customer_email']);
            if ($id)
              $serch_data->addFieldToFilter('id', array('neq' =>$id));


            
            if($serch_data->getSize()){

				$this->messageManager->addError(__('Record is already exists'));
				 $this->_getSession()->setFormData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                exit;
	     	    }
            if ($id) {
                $model->load($id);
            }else{
                $data['dt']=date('Y-m-d H:i:s');
            }

            $data['mo_dt']=date('Y-m-d H:i:s');
            $data['admin_id']=$admin_user_id;
            $model->setData($data);

            try {
                $model->save();
                $data['tracking']=$model->getId();    
                $this->helper->sendEmail($data);

                $this->messageManager->addSuccess(__('The Review has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Review.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
