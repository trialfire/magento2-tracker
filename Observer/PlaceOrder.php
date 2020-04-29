<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Capture the order placed event.
 */
namespace Trialfire\Tracker\Observer;

class PlaceOrder implements \Magento\Framework\Event\ObserverInterface {
    
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
        $tfSession = $this->tfSessionFactory->create();
        $orderIds = $observer->getEvent()->getOrderIds();
        foreach ($orderIds as $orderId) {
            if (!$tfSession->isRecentlyTrackedOrderId($orderId)) {
                // Do not track this order again in this session.
                $tfSession->pushRecentlyTrackedOrderId($orderId);

                //$order = $this->order->load($orderid);

                $tfSession->pushEvent([
                    '$name' => 'placeOrder',
                    'orderId' => $orderId
                ]);
            }
        }
        
        return true;
    }
    
}
    