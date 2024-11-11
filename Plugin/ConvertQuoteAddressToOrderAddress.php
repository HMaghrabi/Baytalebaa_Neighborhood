<?php

declare(strict_types=1);

namespace Baytalebaa\CustomerNeighborhood\Plugin;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Quote\Model\Quote\Address\ToOrderAddress;
use Magento\Sales\Model\Order\Address as OrderAddress;
use Psr\Log\LoggerInterface;

/**
 * Plugin to handle neighborhood data conversion from quote address to order address
 */
class ConvertQuoteAddressToOrderAddress
{
    private const NEIGHBORHOOD_ATTRIBUTE = 'neighborhood';

    private LoggerInterface $logger;
    private Config $eavConfig;
    private AddressRepositoryInterface $addressRepository;

    public function __construct(
        LoggerInterface $logger,
        Config $eavConfig,
        AddressRepositoryInterface $addressRepository
    ) {
        $this->logger = $logger;
        $this->eavConfig = $eavConfig;
        $this->addressRepository = $addressRepository;
    }

    /**
     * Before plugin for address conversion
     *
     * @param ToOrderAddress $subject
     * @param QuoteAddress $quoteAddress
     * @return array
     */
    public function beforeConvert(
        ToOrderAddress $subject,
        QuoteAddress $quoteAddress
    ): array {
        return [$quoteAddress];
    }

    /**
     * After plugin for address conversion
     *
     * @param ToOrderAddress $subject
     * @param OrderAddress $orderAddress
     * @param QuoteAddress $quoteAddress
     * @return OrderAddress
     */
    public function afterConvert(
        ToOrderAddress $subject,
        OrderAddress $orderAddress,
        QuoteAddress $quoteAddress
    ): OrderAddress {
        try {
            $neighborhood = $this->getNeighborhoodValue($quoteAddress);

            if ($neighborhood) {
                $this->setNeighborhoodToOrderAddress($orderAddress, $neighborhood);
            }
        } catch (\Exception $e) {
            $this->logger->error('Error processing neighborhood data: ' . $e->getMessage(), [
                'quote_address_id' => $quoteAddress->getId(),
                'exception' => $e
            ]);
        }

        return $orderAddress;
    }

    /**
     * Get neighborhood value from various possible sources
     *
     * @param QuoteAddress $quoteAddress
     * @return string|null
     */
    private function getNeighborhoodValue(QuoteAddress $quoteAddress): ?string
    {
        // Try to get from customer address
        if ($quoteAddress->getCustomerAddressId()) {
            try {
                $customerAddress = $this->addressRepository->getById($quoteAddress->getCustomerAddressId());
                if ($customerAddress->getCustomAttribute(self::NEIGHBORHOOD_ATTRIBUTE)) {
                    return $customerAddress->getCustomAttribute(self::NEIGHBORHOOD_ATTRIBUTE)->getValue();
                }
            } catch (\Exception $e) {
                $this->logger->debug('Failed to load customer address: ' . $e->getMessage());
            }
        }

        // Try to get from quote address custom attribute
        if ($quoteAddress->getCustomAttribute(self::NEIGHBORHOOD_ATTRIBUTE)) {
            return $quoteAddress->getCustomAttribute(self::NEIGHBORHOOD_ATTRIBUTE)->getValue();
        }

        // Try to get from quote address data
        if ($neighborhoodData = $quoteAddress->getData(self::NEIGHBORHOOD_ATTRIBUTE)) {
            return $neighborhoodData;
        }

        // Try to get from extension attributes
        if ($quoteAddress->getExtensionAttributes()
            && method_exists($quoteAddress->getExtensionAttributes(), 'getNeighborhood')
        ) {
            return $quoteAddress->getExtensionAttributes()->getNeighborhood();
        }

        return null;
    }

    /**
     * Set neighborhood value to order address
     *
     * @param OrderAddress $orderAddress
     * @param string $neighborhood
     * @return void
     */
    private function setNeighborhoodToOrderAddress(OrderAddress $orderAddress, string $neighborhood): void
    {
        $orderAddress->setData(self::NEIGHBORHOOD_ATTRIBUTE, $neighborhood);

        try {
            $orderAddress->setCustomAttribute(self::NEIGHBORHOOD_ATTRIBUTE, $neighborhood);
        } catch (\Exception $e) {
            $this->logger->debug('Failed to set neighborhood custom attribute: ' . $e->getMessage());
        }
    }
}