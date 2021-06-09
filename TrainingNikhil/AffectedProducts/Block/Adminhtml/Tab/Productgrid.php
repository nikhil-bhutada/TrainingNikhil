<?php
namespace TrainingNikhil\AffectedProducts\Block\Adminhtml\Tab;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\Store;
use Magento\CatalogRule\Model\RuleFactory;

class Productgrid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $catalogRule;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context    $context
     * @param \Magento\Backend\Helper\Data               $backendHelper
     * @param \Magento\Catalog\Model\ProductFactory      $productFactory
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Magento\Framework\Module\Manager          $moduleManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Visibility|null                            $visibility
     * @param array                                      $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        RuleFactory $catalogRule,
        Visibility $visibility = null,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->productCollFactory = $productCollFactory;
        $this->coreRegistry = $coreRegistry;
        $this->moduleManager = $moduleManager;
        $this->_storeManager = $storeManager;
        $this->catalogRule = $catalogRule;
        $this->visibility = $visibility ?: ObjectManager::getInstance()->get(Visibility::class);
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * [_construct description]
     * @return [type] [description]
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('aff_products');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setFilterVisibility(false);
        $this->setSaveParametersInSession(true);
    }

    /**
     * [get store id]
     *
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    
    protected function _prepareCollection()
    {
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
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

        $store = $this->_getStore();
        $collection = $this->productFactory->create()->getCollection()->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'attribute_set_id'
        )->addAttributeToSelect(
            'type_id'
        )->addFieldToFilter(
            'entity_id', ['in' => $productIds]
        )->setStore(
            $store
        );

        if ($this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            $collection->joinField(
                'qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );
        }
        if ($store->getId()) {
            $collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                Store::DEFAULT_STORE_ID
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getSearchButtonHtml()
    {
        return '';
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'width' => '50px',
                'index' => 'entity_id',
                'type' => 'number',
                'filter' => false
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-type',
                'column_css_class' => 'col-type',
                'filter' => false
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku',
                'filter' => false
            ]
        );
        $store = $this->_getStore();
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price',
                'filter' => false
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/index/grids', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = array_keys($this->getSelectedProducts());
        return $products;
    }

    /**
     * @return array
     */
    public function getSelectedProducts()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $model = $this->productCollFactory->create()->addFieldToFilter('entity_id', $id);
        $grids = [];
        foreach ($model as $key => $value) {
            $grids[] = $value->getProductId();
        }
        $prodId = [];
        foreach ($grids as $obj) {
            $prodId[$obj] = ['position' => "0"];
        }
        return $prodId;
    }
}