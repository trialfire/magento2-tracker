<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Capture the subscribe to newsletter event.
 */
namespace Trialfire\Tracker\Observer;

class Newsletter implements \Magento\Framework\Event\ObserverInterface {
    
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
        $email = $observer->getEvent()->getSubscriber()->getSubscriberEmail();
        
        $this->tfSessionFactory->create()->pushEvent([
            '$name' => 'newsletter',
            'props' => [
                'email' => $email
            ]
        ]);

        return true;
    }
    
}
    