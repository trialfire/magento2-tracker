<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Capture the add to wishlist event.
 */
namespace Trialfire\Tracker\Observer;

class AddToWishlist implements \Magento\Framework\Event\ObserverInterface {
    
    protected $tfSessionFactory;
    protected $helper;
    
    public function __construct(
        \Trialfire\Tracker\Model\SessionFactory $tfSessionFactory,
        \Trialfire\Tracker\Helper\Data $helper
    ) {
        $this->tfSessionFactory = $tfSessionFactory;
        $this->helper = $helper;
    }
        
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $product = $observer->getItem()->getProduct();
        
        $this->tfSessionFactory->create()->pushEvent([
            '$name' => 'addToWishlist',
            'props' => [
                'sku' => $product->getSku(),
                'name' => $product->getName()
            ]
        ]);
        
        return true;
    }
    
}
    