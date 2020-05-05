<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Capture the started checkout event.
 */
namespace Trialfire\Tracker\Observer;

class StartCheckout implements \Magento\Framework\Event\ObserverInterface {
    
    protected $checkoutSessionFactory;
    protected $tfSessionFactory;
    protected $helper;
    
    public function __construct(
        \Magento\Checkout\Model\SessionFactory $checkoutSessionFactory,
        \Trialfire\Tracker\Model\SessionFactory $tfSessionFactory,
        \Trialfire\Tracker\Helper\Data $helper
    ) {
        $this->checkoutSessionFactory = $checkoutSessionFactory;
        $this->tfSessionFactory = $tfSessionFactory;
        $this->helper = $helper;
    }
        
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $checkout = $this->checkoutSessionFactory->create();

        $quote = $checkout->getQuote();
        $items = $quote->getAllVisibleItems();
        if (!empty($items)) {
            $visibleItems = [];
            foreach ($items as $item) {
                $visibleItems[] = [
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'price' => floatval($item->getPrice()),
                    'quantity' => floatval($item->getQty())
                ];
            }
    
            $this->tfSessionFactory->create()->pushEvent([
                '$name' => 'startCheckout',
                'props' => [
                    'quoteId' => $quote->getId(),
                    'total' => floatval($quote->getGrandTotal()),
                    'coupon' => $quote->getCouponCode(),
                    'currency' => $this->helper->getCurrencyCode(),
                    'products' => $visibleItems
                ]
            ]);    
        }
        
        return true;
    }
    
}
    