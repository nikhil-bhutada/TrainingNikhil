<?php

namespace TrainingNikhil\CustomerIpRestriction\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

use Magento\Store\Model\ScopeInterface;
class CustomerLogin implements ObserverInterface
{
	protected $messageManager;
	protected $scopeConfig;
 
public function __construct(
	\Magento\Framework\Message\ManagerInterface $messageManager,
	\Magento\Framework\App\Action\Context $context,
    \Magento\Customer\Model\Session $customerSession,
    ScopeConfigInterface $scopeConfig
) {
	 $this->messageManager = $messageManager;
	$this->redirect = $context->getRedirect();
    $this->customerSession = $customerSession;
    $this->scopeConfig = $scopeConfig;
}
 
 
public function getConfigValue() {
    return $this->scopeConfig->getValue("helloworld/general/display_text",ScopeInterface::SCOPE_STORE);
}
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        // echo $customer->getName(); //Get customer name
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
    //ip from share internet
    $ip = $_SERVER['HTTP_CLIENT_IP'];
}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
    //ip pass from proxy
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
    $ip = $_SERVER['REMOTE_ADDR'];
}
 if ($ip != $this->getConfigValue()) {
 	$customerId=$customer->getId();
        if($customerId) {
        $this->customerSession->logout()
             ->setBeforeAuthUrl($this->redirect->getRefererUrl())
             ->setLastCustomerId($customerId);
       $message = "IP Blocked";
       
        return $this->messageManager->addError($message);
    } 
 }
       
        
    }
}