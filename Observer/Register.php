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

class Register implements \Magento\Framework\Event\ObserverInterface
{

    protected $addressFactory;
    protected $tfSessionFactory;
    protected $helper;
  
    public function __construct(
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Trialfire\Tracker\Model\SessionFactory $tfSessionFactory,
        \Trialfire\Tracker\Helper\Data $helper
    ) {
        $this->addressFactory = $addressFactory;
        $this->tfSessionFactory = $tfSessionFactory;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();

        // Collect the default billing address if available.
        $addressProps = [];
        $billingAddressId = $customer->getDefaultBilling();
        if ($billingAddressId) {
            $billingAddress = $this->addressFactory->create()->load($billingAddressId);
            $addressProps = $this->helper->formatAddress($billingAddress);
        }
        
        $this->tfSessionFactory->create()->pushEvent([
            '$name' => 'register',
            'apiToken' => $this->helper->getApiToken(),
            'traits' => array_merge([
                'userId' => $customer->getId(),
                'email' => $customer->getEmail(),
                'firstName' => $customer->getFirstname(),
                'lastName' => $customer->getLastname()
            ], $addressProps)
        ]);
        
        return true;
    }
}
