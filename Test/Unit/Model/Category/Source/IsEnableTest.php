<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\Category\Source;

use Aheadworks\FaqFree\Model\Category\Source\IsEnable;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for IsEnable
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IsEnableTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var IsEnable
     */
    private $isEnableObject;

    /**
     * Initialize model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->isEnableObject = $this->objectManager->getObject(IsEnable::class);
    }

    /**
     * Get options
     *
     * @covers IsEnable::toOptionArray
     */
    public function testToOptionArray()
    {
        $statuses = [['label' => __('Enabled'), 'value' => 1], ['label' => __('Disabled'), 'value' => 0]];

        $this->assertEquals($statuses, $this->isEnableObject->toOptionArray());
    }
}
