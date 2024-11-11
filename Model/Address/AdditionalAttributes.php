<?php

declare(strict_types=1);

namespace Baytalebaa\CustomerNeighborhood\Model\Address;

use Magento\Customer\Api\Data\AddressExtensionInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class AdditionalAttributes
 * Handles additional address attributes for customer neighborhood
 */
class AdditionalAttributes extends AbstractSimpleObject implements AddressExtensionInterface
{
    /**
     * Set neighborhood value
     *
     * @param string|null $neighborhood The neighborhood value to set
     * @return void
     */
    public function setNeighborhood(?string $neighborhood): void
    {
        $this->setData('neighborhood', $neighborhood);
    }

    /**
     * Get neighborhood value
     *
     * @return string|null
     */
    public function getNeighborhood(): ?string
    {
        return $this->_get('neighborhood');
    }
}