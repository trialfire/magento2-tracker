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

class PlaceOrder implements \Magento\Framework\Event\ObserverInterface
{
    
    protected $order;
    protected $taxItem;
    protected $customerSession;
    protected $addressFactory;
    protected $tfSessionFactory;
    protected $helper;
    
    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Magento\Sales\Model\ResourceModel\Order\Tax\Item $taxItem,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Trialfire\Tracker\Model\SessionFactory $tfSessionFactory,
        \Trialfire\Tracker\Helper\Data $helper
    ) {
        $this->order = $order;
        $this->taxItem = $taxItem;
        $this->customerSession = $customerSession;
        $this->addressFactory = $addressFactory;
        $this->tfSessionFactory = $tfSessionFactory;
        $this->helper = $helper;
    }

    /**
     * Get the identity of the visitor (customer or guest) who placed the order.
     */
    protected function identifyWhoPlacedOrder($tfSession, $order)
    {
        $customerId = null;
        $identityProps = [
            'email' => $order->getCustomerEmail()
        ];

        // Collect the order billing address by default.
        $billingAddress = $order->getBillingAddress();
        if ($this->customerSession->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomerId();
            $customer = $this->customerSession->getCustomer();
            
            // Collect the default billing address when available.
            $billingAddressId = $customer->getDefaultBilling();
            if ($billingAddressId) {
                $billingAddress = $this->addressFactory->create()->load($billingAddressId);
            }
        }
        $addressProps = $this->helper->formatAddress($billingAddress);
        
        return [
            'userId' => $customerId,
            'traits' => array_merge($identityProps, $addressProps)
        ];
    }
        
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $tfSession = $this->tfSessionFactory->create();

        $whoPlaced = null;
        $orderIds = $observer->getEvent()->getOrderIds();
        foreach ($orderIds as $orderId) {
            if (!$tfSession->isRecentlyTrackedOrderId($orderId)) {
                // Do not track this order again in this session.
                $tfSession->pushRecentlyTrackedOrderId($orderId);

                // Load the order.
                $order = $this->order->load($orderId);

                // Track the identity of the customer or guest.
                if ($whoPlaced == null) {
                    $whoPlaced = $this->identifyWhoPlacedOrder($tfSession, $order);
                }
                
                // Track the orders that were placed.
                $items = $order->getAllVisibleItems();
                if (!empty($items)) {
                    $visibleItems = [];
                    foreach ($items as $item) {
                        $visibleItems[] = [
                            'name' => $item->getName(),
                            'sku' => $item->getSku(),
                            'price' => floatval($item->getPrice()),
                            'quantity' => floatval($item->getQtyOrdered())
                        ];
                    }
            
                    $tfSession->pushEvent([
                        '$name' => 'placeOrder',
                        'apiToken' => $this->helper->getApiToken(),
                        'userId' => $whoPlaced['userId'],
                        'traits' => $whoPlaced['traits'],
                        'props' => [
                            'orderId' => $orderId,
                            'quoteId' => $order->getQuoteId(),
                            'subtotal' => floatval($order->getSubTotal()),
                            'total' => floatval($order->getGrandTotal()),
                            'shippingMethod' => $order->getShippingDescription(),
                            'shipping' => floatval($order->getShippingAmount()),
                            'tax' => floatval($order->getTaxAmount()),
                            'discount' => floatval($order->getDiscountAmount()),
                            'coupon' => $order->getCouponCode(),
                            'currency' => $this->helper->getCurrencyCode(),
                            'products' => $visibleItems
                        ]
                    ]);
                }
            }
        }
        
        return true;
    }
}
