<?php

namespace TrainingNikhil\QtyandStatus\Cron;

class StockUpdate
{

	public function execute()
	{

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info(__METHOD__);
		 try {
        /* Some logic that could throw an Exception */
        	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        	$csvobj = $objectManager->create('Magento\Framework\File\Csv');
        	$loggerobj = $objectManager->create('Psr\Log\LoggerInterface');
        	$productobj = $objectManager->create('Magento\Catalog\Model\Product');
        	$_cacheTypeList = $objectManager->create('Magento\Framework\App\Cache\TypeListInterface');
    		$_cacheFrontendPool = $objectManager->create('Magento\Framework\App\Cache\Frontend\Pool');   
        	$file = '';//location from file to be picked
          	// $DestinationRoot  = '/var/www/html/td.axtrics.com/var/var_import/stock/processed';
           	// if(!is_dir($DestinationRoot))
           	// {
            //   mkdir($DestinationRoot,0777,true);
            // }
			// get current directory path
			// set file pattern
			$file .= "*.csv";
			// copy filenames to array
			$files = array();
			$files = glob($file);
			// sort files by last modified date
			usort($files, function($x, $y) {
			    return filemtime($x) > filemtime($y);
			});
			foreach($files as $item){
			$i = 0;
			$csv = $csvobj->getData($item);
		   	foreach ($csv as $row => $data ) 
		   	{ 
		   	if($productobj->getIdBySku($data[0]) && is_numeric($data[1])) 
		   	{
	          $productRepository = $objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');
	          $stockRegistry = $objectManager->create('Magento\CatalogInventory\Api\StockRegistryInterface');
          		//Load product by SKU
         	  $product = $productRepository->get($data[0]); 
         		//Need to load stock item
	          $stockItem = $stockRegistry->getStockItem($product->getId());
	          $stockItem->setData('qty',$data[1]); //set updated quantity
	          $stockItem->setData('manage_stock',$stockData['manage_stock']);
	          $stockItem->setData('is_in_stock',$stockData['is_in_stock']);
	          $stockItem->setData('use_config_notify_stock_qty',1);
          	  $stockRegistry->updateStockItemBySku($data[0], $stockItem);
		   		}
       		  $i++;
       		// if(sizeof($csv)<=$i){
         //          $file1 = basename($item); 
         //          $DestinationFile = $DestinationRoot."/".$file1; // Create the destination filename 
         //          rename($item, $DestinationFile); // rename the file     
         //          $_cacheTypeList->cleanType('full_page');
		    	  // $cacheFrontend->getBackend()->clean();
      			// 	}
     			}
   			}
 			} catch (\Exception $e) 
 				{
 					$product=false;
					$loggerobj->critical($e->getMessage());
    			}
				return $this;

	}
}