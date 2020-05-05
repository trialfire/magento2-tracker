<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Capture the remove from cart event.
 */
namespace Trialfire\Tracker\Observer;

class RemoveFromCart implements \Magento\Framework\Event\ObserverInterface {
    
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
        $quoteItem = $observer->getQuoteItem();
        
        $this->tfSessionFactory->create()->pushEvent([
            '$name' => 'removeFromCart',
            'props' => [
                'name' => $quoteItem->getName(),
                'sku' => $quoteItem->getSku()
            ]
        ]);
        
        return true;
    }
    
}
    