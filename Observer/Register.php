<?php
/**
 * @category    Trialfire
 * @package     Trialfire_Tracker
 * @author      Mark Lieberman <mark@trialfire.com>
 * @copyright   Copyright (c) Trialfire
 * 
 * Capture the customer registration event.
 */
namespace Trialfire\Tracker\Observer;

class Register implements \Magento\Framework\Event\ObserverInterface {

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
        $customer = $observer->getEvent()->getCustomer();
        if ($customer) {
            $this->tfSessionFactory->create()->pushEvent([
                '$name' => 'register',
                'userId' => $customer->getId(),
                'email' => $customer->getEmail(),
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName()
            ]);
        }
        
        return true;
    }

}
