<?php

declare(strict_types=1);

namespace Baytalebaa\CustomerNeighborhood\Plugin;

use Baytalebaa\CustomerNeighborhood\Block\Address\Field\Neighborhood as NeighborhoodBlock;
use Magento\Customer\Block\Address\Edit as Subject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Plugin to add neighborhood field to customer address form
 */
class AddExtraFieldToAddressForm
{
    /**
     * Add neighborhood field to address form HTML
     *
     * @param Subject $subject
     * @param string $html
     * @return string
     * @throws LocalizedException
     */
    public function afterToHtml(Subject $subject, string $html): string
    {
        try {
            $neighborhoodBlock = $this->getChildBlock(
                NeighborhoodBlock::class,
                $subject
            );

            if (!$neighborhoodBlock instanceof BlockInterface) {
                throw new LocalizedException(
                    __('Failed to create neighborhood block.')
                );
            }

            $address = $subject->getAddress();
            if ($address) {
                $neighborhoodBlock->setAddress($address);
            }

            $neighborhoodHtml = $neighborhoodBlock->toHtml();
            return $this->appendBlockBeforeFieldsetEnd($html, $neighborhoodHtml);

        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Error adding neighborhood field to form: %1', $e->getMessage())
            );
        }
    }

    /**
     * Append child HTML before the fieldset end tag
     *
     * @param string $html
     * @param string $childHtml
     * @return string
     * @throws LocalizedException
     */
    private function appendBlockBeforeFieldsetEnd(string $html, string $childHtml): string
    {
        $pattern = '/<\/fieldset>/i';
        $replacement = $childHtml . '</fieldset>';

        $result = preg_replace($pattern, $replacement, $html, 1);
        if ($result === null) {
            throw new LocalizedException(
                __('Error processing form HTML.')
            );
        }

        return $result;
    }

    /**
     * Create and return a child block
     *
     * @param string $blockClass
     * @param Subject $parentBlock
     * @return BlockInterface
     * @throws LocalizedException
     */
    private function getChildBlock(string $blockClass, Subject $parentBlock): BlockInterface
    {
        if (!class_exists($blockClass)) {
            throw new LocalizedException(
                __('Block class %1 does not exist.', $blockClass)
            );
        }

        $layout = $parentBlock->getLayout();
        if (!$layout) {
            throw new LocalizedException(
                __('Parent block layout is not initialized.')
            );
        }

        return $layout->createBlock(
            $blockClass,
            strtolower(basename(str_replace('\\', '/', $blockClass)))
        );
    }
}