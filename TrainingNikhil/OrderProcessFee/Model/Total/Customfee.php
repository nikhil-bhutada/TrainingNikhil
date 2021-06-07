<?php
namespace TrainingNikhil\OrderProcessFee\Model\Total;

class Customfee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    protected $quoteValidator = null; 

    protected $_helper;

    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator,
        \TrainingNikhil\OrderProcessFee\Helper\Data $helper)
    {
        $this->quoteValidator = $quoteValidator;
        $this->_helper = $helper;
    }


  public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }

        $status = $this->_helper->getConfig('sales/order_fees/additional_fee_status');

        if($status == 1){

        $order_fee = $this->_helper->getConfig('sales/order_fees/order_fee');
        $subTotal = $total->getSubtotal();

        $addfee = (float)$subTotal*$order_fee/100;

        $exist_amount = 0; //$quote->getFee(); 
        $customfee = $addfee; //Excellence_Fee_Model_Fee::getFee();
        $balance = $customfee - $exist_amount;

        $total->setTotalAmount('customfee', $balance);
        $total->setBaseTotalAmount('customfee', $balance);
        $quote->setCustomfee($balance);
        $quote->setBaseCustomfee($balance);

        //$total->setGrandTotal($total->getGrandTotal() + $balance);
        $total->setBaseGrandTotal($total->getBaseGrandTotal());

        }

        return $this;
    } 

    protected function clearValues(Address\Total $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array|null
     */
    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $order_fee = $this->_helper->getConfig('sales/order_fees/order_fee');
        $subTotal = $total->getSubtotal();

        $addfee = $subTotal*$order_fee/100;
        return [
            'code' => 'customfee',
            'title' => 'Order Process Fee',
            'value' => $addfee
        ];
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Order Process Fee');
    }
}
?>