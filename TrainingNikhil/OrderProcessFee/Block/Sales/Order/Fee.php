<?php
 namespace TrainingNikhil\OrderProcessFee\Block\Sales\Order;

 class Fee extends \Magento\Framework\View\Element\Template
{
    
    /**
     * @var Order
     */
    protected $_order;
    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->_order;
    }
    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $customfee = new \Magento\Framework\DataObject(
            [
                'code' => 'customfee',
                'strong' => false,
                'value' => $this->_order->getCustomfee(),
                'label' => __('Order Process Fee'),
            ]
        );

        $parent->addTotal($customfee, 'customfee');
        return $this;
    }
}

?>