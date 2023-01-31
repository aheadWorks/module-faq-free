<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\Article\Source;

use Aheadworks\FaqFree\Model\Article\Source\IsActive;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for IsActive
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IsActiveTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var IsActive
     */
    private $isActiveObject;

    /**
     * Initialize model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->isActiveObject = $this->objectManager->getObject(IsActive::class);
    }

    /**
     * Get options
     *
     * @covers IsActive::toOptionArray
     */
    public function testToOptionArray()
    {
        $statuses = [['label' => __('Enabled'), 'value' => 1], ['label' => __('Disabled'), 'value' => 0]];

        $this->assertEquals($statuses, $this->isActiveObject->toOptionArray());
    }
}
