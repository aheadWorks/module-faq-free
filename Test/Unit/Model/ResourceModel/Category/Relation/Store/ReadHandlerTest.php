<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\ResourceModel\Category\Relation\Store;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\Category;
use Aheadworks\FaqFree\Model\ResourceModel\Category\Relation\Store\ReadHandler;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for ReadHandler
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ReadHandlerTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var EntityMetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityMetadataInterfaceMock;

    /**
     * @var MetadataPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataPoolMock;

    /**
     * @var ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceConnectionMock;

    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;

    /**
     * @var Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $selectMock;

    /**
     * @var ReadHandler
     */
    private $readHandlerObject;

    /**
     * Initialize relation
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->metadataPoolMock = $this->createMock(MetadataPool::class);
        $this->selectMock = $this->createMock(Select::class);
        $this->entityMetadataInterfaceMock = $this->createMock(EntityMetadataInterface::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);

        $this->readHandlerObject = $this->objectManager->getObject(
            ReadHandler::class,
            ['metadataPool' => $this->metadataPoolMock, 'resourceConnection' => $this->resourceConnectionMock]
        );
    }

    /**
     * Preparing for execution lookupStoreIds
     *
     * @param $categoryId
     * @return array
     */
    private function prepareLookupStoreIds($categoryId)
    {
        $entityConnectionName = 'connection_name';
        $tableName = 'aw_faq_category_store';
        $identifierField = 'category_id';
        $expected = [1, 2, 3, 4];

        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getMetadata')
            ->with(CategoryInterface::class)
            ->willReturn($this->entityMetadataInterfaceMock);

        $this->entityMetadataInterfaceMock
            ->expects($this->once())
            ->method('getEntityConnectionName')
            ->willReturn($entityConnectionName);

        $this->resourceConnectionMock
            ->expects($this->once())
            ->method('getConnectionByName')
            ->with($entityConnectionName)
            ->willReturn($this->connectionMock);

        $this->connectionMock
            ->expects($this->once())
            ->method('select')
            ->willReturn($this->selectMock);

        $this->resourceConnectionMock
            ->expects($this->once())
            ->method('getTableName')
            ->with($tableName)
            ->willReturnArgument(0);

        $this->selectMock
            ->expects($this->once())
            ->method('from')
            ->with(['fas' => $tableName], 'store_ids')
            ->willReturnSelf();

        $this->entityMetadataInterfaceMock
            ->expects($this->once())
            ->method('getIdentifierField')
            ->willReturn($identifierField);

        $this->selectMock
            ->expects($this->once())
            ->method('where')
            ->with('fas.' . $identifierField . ' = :categoryId')
            ->willReturnSelf();

        $this->connectionMock
            ->expects($this->once())
            ->method('fetchCol')
            ->with($this->selectMock, ['categoryId' => $categoryId])
            ->willReturn($expected);

        return $expected;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @covers ReadHandler::lookupStoreIds
     */
    public function testLookupStoreId()
    {
        $categoryId = 3;

        $storeIds = $this->prepareLookupStoreIds($categoryId);

        $this->assertEquals($storeIds, $this->readHandlerObject->lookupStoreIds($categoryId));
    }

    /**
     * Load relation
     *
     * @covers  ReadHandler::execute
     * @depends testLookupStoreId
     */
    public function testExecute()
    {
        $categoryId = 4;

        /** @var Category|\PHPUnit_Framework_MockObject_MockObject $categoryMock */
        $categoryMock = $this->createMock(Category::class);

        $categoryMock
            ->expects($this->any())
            ->method('getCategoryId')
            ->willReturn($categoryId);

        $storeIds = $this->prepareLookupStoreIds($categoryId);

        $categoryMock
            ->expects($this->once())
            ->method('setData')
            ->with('store_ids', $storeIds)
            ->willReturnSelf();

        $this->assertEquals($categoryMock, $this->readHandlerObject->execute($categoryMock));
    }
}
