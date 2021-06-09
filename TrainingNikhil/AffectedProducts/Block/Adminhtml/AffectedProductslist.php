<?php

namespace TrainingNikhil\AffectedProducts\Block\Adminhtml;

use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogRule\Model\RuleFactory;

class AffectedProductslist extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'products/affected_productslist.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $catalogRule;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                           $context
     * @param \Magento\Framework\Registry                                       $registry
     * @param \Magento\Framework\Json\EncoderInterface                          $jsonEncoder
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory
     * @param \TrainingNikhil\AffectedProducts\Model\Rule $rule
     * @param array                                                             $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productFactory,
        StoreManagerInterface $storeManager,
        RuleFactory $catalogRule,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->storeManager = $storeManager;
        $this->catalogRule = $catalogRule;
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'TrainingNikhil\AffectedProducts\Block\Adminhtml\Tab\Productgrid',
                'category.product.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {           
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $resultProductIds = [];
        $catalogRuleCollection = $this->catalogRule->create()->getCollection();
        $catalogRuleCollection->addIsActiveFilter(1);
        foreach ($catalogRuleCollection as $catalogRule) {
            $productIdsAccToRule = $catalogRule->getMatchingProductIds();
            foreach ($productIdsAccToRule as $productId => $ruleProductArray) {
                if (!empty($ruleProductArray[$websiteId])) {
                    array_push($resultProductIds, $productId);
                }
            }
        }      
        $productIds = implode(',', $resultProductIds);
        $productFactory = $this->productFactory->create();
        $productFactory->addFieldToFilter('entity_id', ['in' => $productIds]);
        $result = [];
        if (!empty($productFactory->getData())) {
            return $this->jsonEncoder->encode($productFactory->getData());
        }
        return '{}';
    }

    public function getItem()
    {
        return $this->registry->registry('my_item');
    }
}