<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Capture the default billing address update event.
 */
namespace Trialfire\Tracker\Observer;

class Address implements \Magento\Framework\Event\ObserverInterface {
    
    protected $customerSession;
    protected $tfSessionFactory;
    protected $helper;
    
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Trialfire\Tracker\Model\SessionFactory $tfSessionFactory,
        \Trialfire\Tracker\Helper\Data $helper
    ) {
        $this->customerSession = $customerSession;
        $this->tfSessionFactory = $tfSessionFactory;
        $this->helper = $helper;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $customerAddress = $observer->getCustomerAddress();
        
        // Do not track the address if not logged in.
        if (!$this->customerSession->isLoggedIn()) {
            return true;
        }

        // Ensure the address belongs to the logged in customer.
        if ($this->customerSession->getCustomerId() != $customerAddress->getCustomerId()) {
            return true;
        }

        // Only track the default billing address.
        if ($customerAddress->getIsDefaultBilling()) {
            $this->tfSessionFactory->create()->pushEvent([
                '$name' => 'billingAddress',
                'userId' => $customerAddress->getCustomerId(),
                'props' => $this->helper->formatAddress($customerAddress)
            ]);
        }

        return true;
    }
        
}
        