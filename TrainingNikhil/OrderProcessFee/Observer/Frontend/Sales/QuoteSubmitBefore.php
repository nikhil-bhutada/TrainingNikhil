<?php
namespace TrainingNikhil\OrderProcessFee\Observer\Frontend\Sales;

class QuoteSubmitBefore implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        try {
           
            $quote = $observer->getQuote();
            $order = $observer->getOrder();
            $order->setData('customfee', $quote->getData('customfee'));

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}
?>