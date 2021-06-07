<?php
/**
 * Copyright © Unyscape Infocom Pvt. Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);
    
namespace TrainingNikhil\OrderProcessFee\Rewrite\Magento\Sales\Block\Adminhtml\Order;

class Totals extends \Magento\Sales\Block\Adminhtml\Order\Totals
{
      /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        
        $order = $this->getSource();
        
       
        $this->_totals['customfee'] = new \Magento\Framework\DataObject(
            [
                'code' => 'customfee',
                'value' => $order->getCustomfee(),
                'base_value' => $order->getBaseCustomfee(),
                'label' => 'Order Process Fee',
       

     ]
        );
        return $this;
    }

}