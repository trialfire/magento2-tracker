<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Capture the add to cart event.
 */
namespace Trialfire\Tracker\Observer;

class AddToCart implements \Magento\Framework\Event\ObserverInterface {
    
    protected $productRepository;
    protected $tfSessionFactory;
    protected $helper;
    
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Trialfire\Tracker\Model\SessionFactory $tfSessionFactory,
        \Trialfire\Tracker\Helper\Data $helper
    ) {
        $this->productRepository = $productRepository;
        $this->tfSessionFactory = $tfSessionFactory;
        $this->helper = $helper;
    }
        
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $items = $observer->getItems();

        // Product type constants.
        $typeConfigurable = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
        $typeBundle = \Magento\Bundle\Model\Product\Type::TYPE_CODE;

        $visibleItems = [];
        foreach ($items as $item) {
            $product = $item->getProduct();
            if ($product->getTypeId() == $typeConfigurable) {
                // This is a configurable product.
                // Ignore these types of products.
            } else
            if ($product->getTypeId() == $typeBundle) {
                // This is a bundled product.
                // Record that the bundle is in the cart but do not include the final price.
                $visibleItems[] = [
                    'name' => $item->getName(),
                    'sku' =>  $this->productRepository->getById($item->getProductId())->getSku()
                ];
            } else {
                // This is a simple product.
                // May or may not be included as part of a configured, grouped, or bundled product.
                $parentItem = $item->getParentItem();
                $parentIsConfigurable = $parentItem && ($parentItem->getProductType() == $typeConfigurable);
                $quantity = $parentIsConfigurable ? $parentItem->getQtyToAdd() : $item->getQtyToAdd();
                $visibleItems[] = [
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'price' => floatval($item->getProduct()->getFinalPrice()),
                    'quantity' => floatval($quantity)
                ];
            }
        }
        
        $this->tfSessionFactory->create()->pushEvent([
            '$name' => 'addToCart',
            'props' => [
                'currency' => $this->helper->getCurrencyCode(),
                'products' => $visibleItems
            ]
        ]);
        
        return true;
    }
    
}
    