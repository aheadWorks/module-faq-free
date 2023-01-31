<?php

namespace Aheadworks\FaqFree\Test\Unit\Model;

use Aheadworks\FaqFree\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for Config
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $configObject;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ScopeConfigInterface
     */
    private $configMock;

    /**
     * Initialize model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->configMock = $this->createMock(ScopeConfigInterface::class);

        $this->configObject = $this->objectManager->getObject(
            Config::class,
            ['scopeConfig' => $this->configMock]
        );
    }

    /**
     * Get default number of columns to display
     *
     * @covers Config::getDefaultNumberOfColumnsToDisplay
     */
    public function testGetDefaultNumberOfColumnsToDisplay()
    {
        $result = 1;

        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('faq/general/number_of_columns', 'store', null)
            ->willReturn($result);

        $this->assertEquals($result, $this->configObject->getDefaultNumberOfColumnsToDisplay(null));
    }

    /**
     * Get faq route
     *
     * @covers Config::getFaqRoute
     */
    public function testGetFaqRoute()
    {
        $result = 'route';

        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('faq/general/faq_route', 'website')
            ->willReturn($result);

        $this->assertEquals($result, $this->configObject->getFaqRoute());
    }

    /**
     * Get faq Meta title
     *
     * @covers Config::getFaqMetaTitle
     */
    public function testGetFaqMetaTitle()
    {
        $result = 'meta_title';

        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('faq/general/meta_title', 'store')
            ->willReturn($result);

        $this->assertEquals($result, $this->configObject->getFaqMetaTitle());
    }

    /**
     * Get faq Meta description
     *
     * @covers Config::getFaqMetaDescription
     */
    public function testGetFaqMetaDescription()
    {
        $result = 'meta_description';

        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('faq/general/meta_description', 'store')
            ->willReturn($result);

        $this->assertEquals($result, $this->configObject->getFaqMetaDescription());
    }

    /**
     * Checks if FAQ link in Categories is enabled
     *
     * @covers Config::isNavigationMenuLinkEnabled
     */
    public function testIsNavigationMenuLinkEnabled()
    {
        $result = true;

        $this->configMock
            ->expects($this->once())
            ->method('isSetFlag')
            ->with('faq/general/navigation_menu_link_enabled', 'store')
            ->willReturn($result);

        $this->assertEquals($result, $this->configObject->isNavigationMenuLinkEnabled());
    }

    /**
     * Get customer groups with disabled FAQ
     *
     * @covers Config::isNavigationMenuLinkEnabled
     */
    public function testGetFaqGroups()
    {
        $resultString = '1,3,4';
        $resultArray = ['1', '3', '4'];

        $this->configMock
            ->expects($this->once())
            ->method('getValue')
            ->with('faq/general/groups_with_disabled_faq', 'store')
            ->willReturn($resultString);

        $this->assertEquals($resultArray, $this->configObject->getFaqGroups());
    }
}
