<?php

namespace TrainingNikhil\CronModule\Cron;
use Psr\Log\LoggerInterface;
class GetProducts
{
	protected $_pageFactory;
	protected $_productCollectionFactory;
	protected $_category;
	protected $categoryFactory;
 protected $logger;

public function __construct(
		LoggerInterface $logger,
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Catalog\Api\CategoryLinkManagementInterface $category,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory)
	{
		$this->logger = $logger;
		$this->_pageFactory = $pageFactory;
		$this->_productCollectionFactory = $productCollectionFactory; 
		$this->_category = $category;  
		$this->categoryFactory = $categoryFactory;
		return parent::__construct($context);
	}
	public function execute()
	{

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info(__METHOD__);
		 try {
        $to = date("Y-m-d h:i:s"); // current date
    $from = strtotime('-3 day', strtotime($to));
    $from = date('Y-m-d h:i:s', $from); // 2 days before
	$collection = $this->_productCollectionFactory->create();
    $collection->addAttributeToSelect('*');
    $collection->addFieldToFilter('created_at', array('from'=>$from, 'to'=>$to));
	$categoryId="6";
	$category = $this->categoryFactory->create()->load($categoryId);
	$category=$category->getProductCollection()->addAttributeToSelect('*');
    if (!empty($collection)) {
    	if (sizeof($category) <=10) {	
    	foreach ($collection as $collect) {
    	$categoryIds = array('6');
		$sku = $collect->getSku();		
		$this->_category->assignProductToCategories($sku, $categoryIds);
		$this->logger->debug('Assigned'.$sku);
    	}
    	}
    	else
    	{
    		
    		$this->logger->debug('category has already 10 products');
    	}
    }
    else
    {
$this->logger->debug('No products for CronModule 3 days');
    }
 			} catch (\Exception $e) 
 				{
 					
					$this->logger->critical($e->getMessage());
    			}
				return $this;

	}
}