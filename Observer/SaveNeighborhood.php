<?php

declare(strict_types=1);

namespace Baytalebaa\CustomerNeighborhood\Observer;

use Magento\Customer\Model\Address;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\AddressFactory;

/**
 * Observer for saving neighborhood information to order address
 */
class SaveNeighborhood implements ObserverInterface
{
    /**
     * @var AddressFactory
     */
    private AddressFactory $orderAddressFactory;

    /**
     * @param AddressFactory $orderAddressFactory
     */
    public function __construct(
        AddressFactory $orderAddressFactory
    ) {
        $this->orderAddressFactory = $orderAddressFactory;
    }

    /**
     * Execute observer to save neighborhood information
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        try {
            /** @var Address $customerAddress */
            $customerAddress = $observer->getCustomerAddress();
            if (!$customerAddress) {
                throw new LocalizedException(__('Customer address not found in observer.'));
            }

            $neighborhood = $customerAddress->getNeighborhood();
            if (!$neighborhood) {
                return;
            }

            $orderAddress = $this->orderAddressFactory->create();
            $orderAddress->setNeighborhood($neighborhood);

            // Note: Additional implementation needed:
            // 1. Load existing order address if needed:
            // $orderAddress->load($orderId);

            // 2. Set other required address fields
            // $orderAddress->setData(...);

            // 3. Save the address
            // $orderAddress->save();

        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Error saving neighborhood information: %1', $e->getMessage())
            );
        }
    }
}