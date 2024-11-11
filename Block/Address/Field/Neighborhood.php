<?php

declare(strict_types=1);

namespace Baytalebaa\CustomerNeighborhood\Block\Address\Field;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\View\Element\Template;

class Neighborhood extends Template
{
    /**
     * Path to template file
     *
     * @var string
     */
    protected $_template = 'address/edit/field/neighborhood.phtml';

    /**
     * Customer address
     *
     * @var AddressInterface|null
     */
    private $_address;

    /**
     * Get the neighborhood value from the address
     *
     * @return string
     */
    public function getNeighborhoodValue(): string
    {
        $address = $this->getAddress();
        if ($address === null) {
            return '';
        }

        $neighborhoodValue = $address->getCustomAttribute('neighborhood');
        if (!$neighborhoodValue instanceof AttributeInterface) {
            return '';
        }

        return (string) $neighborhoodValue->getValue();
    }

    /**
     * Get the associated address
     *
     * @return AddressInterface|null
     */
    public function getAddress(): ?AddressInterface
    {
        return $this->_address;
    }

    /**
     * Set the associated address
     *
     * @param AddressInterface $address
     * @return void
     */
    public function setAddress(AddressInterface $address): void
    {
        $this->_address = $address;
    }
}
