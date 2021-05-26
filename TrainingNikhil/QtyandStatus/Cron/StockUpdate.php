<?php

namespace TrainingNikhil\QtyandStatus\Cron;
use \Psr\Log\LoggerInterface;
use Magento\Framework\File\Csv;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
class StockUpdate
{
	 protected $logger;

  public function __construct(LoggerInterface $logger,
Csv $csv,
Product $product,
TypeListInterface $typelist,
Pool $pool,
ProductRepositoryInterface $productrepo,
StockRegistryInterface $stock
  ) {

    $this->logger = $logger;
    $this->csv = $csv;
    $this->product = $product;
    $this->typelist = $typelist;
    $this->pool = $pool;
    $this->productrepo = $productrepo;
    $this->stock = $stock;

  }

  /**

    * Write to system.log

    *

    * @return void

  */

	public function execute()
	{

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info(_METHOD_);
		 try {
        /* Some logic that could throw an Exception */
        	
        	$file = '/var/www/html/td.axtrics.com/var/var_import/stock/'; // location of file 
          	$DestinationRoot  = '/var/www/html/td.axtrics.com/var/var_import/stock/processed'; /// location of file after processed
           	if(!is_dir($DestinationRoot))
           	{
              mkdir($DestinationRoot,0777,true);
            }
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
			$csv = $this->csv->getData($item);
		   	foreach ($csv as $row => $data ) 
		   	{ 
		   	if($this->product->getIdBySku($data[0]) && is_numeric($data[1])) 
		   	{
          		//Load product by SKU
         	  $product = $this->productrepo->get($data[0]); 
         		//Need to load stock item
	          $stockItem = $this->stock->getStockItem($product->getId());
	          $stockItem->setData('qty',$data[1]); //set updated quantity
	          //$stockItem->setData('manage_stock',$stockData['manage_stock']);
	          //$stockItem->setData('is_in_stock',$stockData['is_in_stock']);
	          //$stockItem->setData('use_config_notify_stock_qty',1);
          	  $this->stock->updateStockItemBySku($data[0], $stockItem);
		   		}
       		  $i++;
       		if(sizeof($csv)<=$i){
                  $file1 = basename($item); 
                  $DestinationFile = $DestinationRoot."/".$file1; // Create the destination filename 
                  rename($item, $DestinationFile); // rename the file     
                  $this->typelist->cleanType('full_page');
		    	  $this->pool->getBackend()->clean();
      				}
     			}
   			}
 			} catch (\Exception $e) 
 				{
 					$product=false;
					 $this->logger->critical($e->getMessage());
    			}
				return $this;

	}
}