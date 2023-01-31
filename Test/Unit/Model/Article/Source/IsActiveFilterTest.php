<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\Article\Source;

use Aheadworks\FaqFree\Model\Article\Source\IsActive;
use Aheadworks\FaqFree\Model\Article\Source\IsActiveFilter;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for IsActiveFilter
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IsActiveFilterTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var IsActive|\PHPUnit_Framework_MockObject_MockObject
     */
    private $isActiveMock;

    /**
     * @var IsActiveFilter
     */
    private $isActiveFilterObject;

    /**
     * Initialize model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->isActiveMock = $this->createMock(IsActive::class);

        $this->isActiveFilterObject = $this->objectManager->getObject(
            IsActiveFilter::class,
            ['isActive' => $this->isActiveMock]
        );
    }

    /**
     * Return array of options as value-label pairs
     *
     * @covers IsActiveFilter::toOptionArray
     */
    public function testToOptionArray()
    {
        $array = [['label' => 'label1', 'value' => 'value1'], ['label' => 'label2', 'value' => 'value2']];

        $this->isActiveMock
            ->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($array);

        $this->assertEquals($array, $this->isActiveFilterObject->toOptionArray());
    }
}
