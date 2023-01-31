<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\Category\Source;

use Aheadworks\FaqFree\Model\Category\Source\IsEnable;
use Aheadworks\FaqFree\Model\Category\Source\IsEnableFilter;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for IsEnableFilter
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class IsEnableFilterTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var IsEnable|\PHPUnit_Framework_MockObject_MockObject
     */
    private $isEnableMock;

    /**
     * @var IsEnableFilter
     */
    private $isEnableFilterObject;

    /**
     * Initialize model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->isEnableMock = $this->createMock(IsEnable::class);

        $this->isEnableFilterObject = $this->objectManager->getObject(
            IsEnableFilter::class,
            ['isEnable' => $this->isEnableMock]
        );
    }

    /**
     * Return array of options as value-label pairs
     *
     * @covers IsEnableFilter::toOptionArray
     */
    public function testToOptionArray()
    {
        $array = [['label' => 'label1', 'value' => 'value1'], ['label' => 'label2', 'value' => 'value2']];

        $this->isEnableMock
            ->expects($this->once())
            ->method('toOptionArray')
            ->willReturn($array);

        $this->assertEquals($array, $this->isEnableFilterObject->toOptionArray());
    }
}
