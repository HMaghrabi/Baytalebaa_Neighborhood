<?php

declare(strict_types=1);

namespace Baytalebaa\CustomerNeighborhood\Plugin;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Quote\Model\QuoteRepository;

/**
 * Plugin to handle neighborhood data during shipping information save
 */
class ShippingInformationManagementPlugin
{
    private const NEIGHBORHOOD_ATTRIBUTE = 'neighborhood';

    private QuoteRepository $quoteRepository;

    public function __construct(
        QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Before plugin for saving address information
     * Transfers neighborhood data from extension attributes to addresses
     *
     * @param ShippingInformationManagement $subject
     * @param int|string $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return array
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
                                      $cartId,
        ShippingInformationInterface $addressInformation
    ): array {
        $shippingAddress = $addressInformation->getShippingAddress();
        $extAttributes = $shippingAddress->getExtensionAttributes();

        if ($this->hasNeighborhoodAttribute($extAttributes)) {
            $neighborhood = $extAttributes->getNeighborhood();
            $this->setNeighborhoodToAddresses($addressInformation, $neighborhood);
        }

        return [$cartId, $addressInformation];
    }

    /**
     * Check if extension attributes contain neighborhood data
     *
     * @param mixed $extAttributes
     * @return bool
     */
    private function hasNeighborhoodAttribute($extAttributes): bool
    {
        return $extAttributes
            && method_exists($extAttributes, 'getNeighborhood')
            && $extAttributes->getNeighborhood();
    }

    /**
     * Set neighborhood value to shipping and billing addresses
     *
     * @param ShippingInformationInterface $addressInformation
     * @param string $neighborhood
     * @return void
     */
    private function setNeighborhoodToAddresses(
        ShippingInformationInterface $addressInformation,
        string $neighborhood
    ): void {
        // Set for shipping address
        $addressInformation->getShippingAddress()
            ->setData(self::NEIGHBORHOOD_ATTRIBUTE, $neighborhood);

        // Set for billing address if available
        $billingAddress = $addressInformation->getBillingAddress();
        if ($billingAddress !== null) {
            $billingAddress->setData(self::NEIGHBORHOOD_ATTRIBUTE, $neighborhood);
        }
    }
}