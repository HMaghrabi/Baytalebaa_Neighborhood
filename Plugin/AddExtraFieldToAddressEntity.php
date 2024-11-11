<?php

declare(strict_types=1);

namespace Baytalebaa\CustomerNeighborhood\Plugin;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Plugin to add neighborhood field to customer address entity
 */
class AddExtraFieldToAddressEntity
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Add neighborhood custom attribute to address before save
     *
     * @param AddressRepositoryInterface $subject
     * @param AddressInterface $address
     * @return array
     * @throws LocalizedException
     */
    public function beforeSave(
        AddressRepositoryInterface $subject,
        AddressInterface $address
    ): array {
        try {
            $neighborhood = $this->request->getParam('neighborhood');
            if ($neighborhood && is_string($neighborhood)) {
                $neighborhood = trim($neighborhood);
                if (!empty($neighborhood)) {
                    $address->setCustomAttribute('neighborhood', $neighborhood);
                }
            }

            return [$address];
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Error adding neighborhood to address: %1', $e->getMessage())
            );
        }
    }
}