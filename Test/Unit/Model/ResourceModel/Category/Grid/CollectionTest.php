<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\ResourceModel\Category\Grid;

use Aheadworks\FaqFree\Model\ResourceModel\Category\Grid\Collection;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for Collection
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CollectionTest extends TestCase
{
    /**
     * Constant for main table
     */
    public const MAIN_TABLE = 'category_table';

    /**
     * @var EntityFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityFactoryMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * @var FetchStrategyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fetchStrategyMock;

    /**
     * @var ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $eventManagerMock;

    /**
     * @var AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapterMock;

    /**
     * @var Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $selectMock;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Collection
     */
    private $collectionObject;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->entityFactoryMock = $this->createMock(EntityFactoryInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->fetchStrategyMock = $this->createMock(FetchStrategyInterface::class);
        $this->eventManagerMock = $this->createMock(ManagerInterface::class);
        $this->selectMock = $this->createMock(Select::class);
        $this->resourceMock = $this->createMock(AbstractDb::class);
        $this->adapterMock = $this->createMock(AdapterInterface::class);

        $this->adapterMock
            ->expects($this->any())
            ->method('select')
            ->willReturn($this->selectMock);

        $this->resourceMock
            ->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->adapterMock);

        $this->resourceMock
            ->expects($this->any())
            ->method('getMainTable')
            ->willReturn(self::MAIN_TABLE);

        $this->resourceMock
            ->expects($this->any())
            ->method('getMainTable')
            ->willReturnArgument(0);

        $this->collectionObject = $this->objectManager->getObject(
            Collection::class,
            [
                'entityFactory' => $this->entityFactoryMock,
                'logger' => $this->loggerMock,
                'fetchStrategy' => $this->fetchStrategyMock,
                'eventManager' => $this->eventManagerMock,
                'resource' => $this->resourceMock,
                'resourceModel' => 'resourceModel',
                'mainTable' => self::MAIN_TABLE
            ]
        );
    }

    /**
     * Set and get aggregations for collection
     *
     * @covers Collection::setAggregations
     * @covers Collection::getAggregations
     */
    public function testGetAndSetAggregations()
    {
        $aggregationMock = $this->createMock(AggregationInterface::class);

        $this->collectionObject->setAggregations($aggregationMock);

        $this->assertEquals($aggregationMock, $this->collectionObject->getAggregations());
    }

    /**
     * getSearchCriteria will return always null
     *
     * @covers Collection::getSearchCriteria
     */
    public function testGetSearchCriteria()
    {
        $this->assertNull($this->collectionObject->getSearchCriteria());
    }

    /**
     * setSearchCriteria doesn't do anything
     *
     * @covers Collection::setSearchCriteria
     */
    public function testSetSearchCriteria()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $this->assertInstanceOf(Collection::class, $this->collectionObject->setSearchCriteria($searchCriteriaMock));
    }

    /**
     * Get total count
     *
     * @covers Collection::getTotalCount
     */
    public function testGetTotalCount()
    {
        $expected = 10;

        $this->adapterMock
            ->expects($this->once())
            ->method('fetchOne')
            ->willReturn($expected);

        $this->assertEquals($expected, $this->collectionObject->getTotalCount());
    }

    /**
     * setSearchCriteria doesn't do anything
     *
     * @covers Collection::setItems
     */
    public function testSetTotalCount()
    {
        $this->assertInstanceOf(Collection::class, $this->collectionObject->setItems([]));
    }
}
