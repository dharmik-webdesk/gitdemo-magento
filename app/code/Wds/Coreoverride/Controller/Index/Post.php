<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Wds\Coreoverride\Controller\Index;

use Magento\Contact\Model\ConfigInterface;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;

class Post extends \Magento\Contact\Controller\Index\Post
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var MailInterface
     */
    private $mail;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param ConfigInterface $contactsConfig
     * @param MailInterface $mail
     * @param DataPersistorInterface $dataPersistor
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ConfigInterface $contactsConfig,
        MailInterface $mail,
        DataPersistorInterface $dataPersistor,
        LoggerInterface $logger = null
    ) {
        $this->context = $context;
        $this->mail = $mail;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        parent::__construct($context, $contactsConfig, $mail, $dataPersistor, $logger);
        
    }
    public function execute()
    {
       
        if (!$this->isPostRequest()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

         $g_recaptcha_response = $this->getRequest()->getPost('g-recaptcha-response');
       // $helper_Formvalidater = $this->helper('Wds\Coreoverride\Helper\Formvalidater');

        
        $check_rquest_url = $this->varify_request_url();


        $google_captcha_validator = $this->verify_google_captcha($g_recaptcha_response);
        if($google_captcha_validator == false){
            $this->messageManager->addErrorMessage("Captcha is not valid, Please try again.");
            $this->dataPersistor->set('contact_us', $this->getRequest()->getParams());
            header("Location: " . $_SERVER["HTTP_REFERER"]);exit;
        } 
       
        try {
            $this->sendEmail($this->validatedParams());
            $this->messageManager->addSuccessMessage(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );
            $this->dataPersistor->clear('contact_us');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('contact_us', $this->getRequest()->getParams());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
            $this->dataPersistor->set('contact_us', $this->getRequest()->getParams());
        }
        return $this->resultRedirectFactory->create()->setPath('contact/index');
    }

    /**
     * @param array $post Post data from contact form
     * @return void
     */
    private function sendEmail($post)
    {
        $this->mail->send(
            $post['email'],
            ['data' => new DataObject($post)]
        );
    }

    /**
     * @return bool
     */
    private function isPostRequest()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        return !empty($request->getPostValue());
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('name')) === '') {
            throw new LocalizedException(__('Name is missing'));
        }
        if (trim($request->getParam('comment')) === '') {
            throw new LocalizedException(__('Comment is missing'));
        }
        if (false === \strpos($request->getParam('email'), '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }
        if (trim($request->getParam('hideit')) !== '') {
            throw new \Exception();
        }

        return $request->getParams();
    }
    public function verify_google_captcha($gcaptcha_response, $secret = "6LcG7EoUAAAAAIbilYDmA0bhf8CMza82Q2307Gvq"){

          //open a new curl connection
          $ch = curl_init();
          $url = "https://www.google.com/recaptcha/api/siteverify";
          $fields = array(
            "secret" => $secret,
            "response" => $gcaptcha_response,
          );

          $fields_string = "";
          foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
          
          rtrim($fields_string, '&');
          
          //set the url, number of POST vars, POST data
          curl_setopt($ch,CURLOPT_URL, $url);
          curl_setopt($ch,CURLOPT_POST, count($fields));
          curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          
          //execute post request
          $result = curl_exec($ch);
          
          //close connection
          curl_close($ch);
          $json_data = json_decode($result);

          return $json_data->success;
          
          //If the captcha was declined, redirect the user to the same page with the query var "captcha" set to "not-entered"
            /*  if($json_data->success == false) {
                $redirect_to = $_SERVER["HTTP_REFERER"];
                    if(!strpos($redirect_to, "?")):
                      $redirect_to.="?captcha=not-entered";
                    else:
                      $redirect_to.="&captcha=not-entered";
                    endif;
                    header("Location: " . $redirect_to);
                    exit;
              } */
        }
        public function varify_request_url(){
            $url = parse_url($_SERVER["HTTP_REFERER"]);
            
            if(trim($url['host'])!= "www.compressorworld.com"){
                header("Location: " . $_SERVER["HTTP_REFERER"]);exit;
            }
            return;
        }
}
