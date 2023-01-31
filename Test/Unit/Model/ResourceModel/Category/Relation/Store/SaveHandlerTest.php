<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\ResourceModel\Category\Relation\Store;

use Aheadworks\FaqFree\Api\Data\CategoryInterface;
use Aheadworks\FaqFree\Model\Category;
use Aheadworks\FaqFree\Model\ResourceModel\Category\Relation\Store\SaveHandler;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for SaveHandler
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveHandlerTest extends TestCase
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
     * @var SaveHandler
     */
    private $saveHandlerObject;

    /**
     * @var string
     */
    private $categoryLinkField;

    /**
     * @var string
     */
    private $categoryLinkFieldValue;

    /**
     * @var string
     */
    private $tableName;

    /**
     * Initialize relation
     */
    protected function setUp(): void
    {
        $entityConnectionName = 'connection_name';
        $this->categoryLinkField = 'category_link_field';
        $this->tableName = 'aw_faq_category_store';
        $this->categoryLinkFieldValue = '2';

        $this->objectManager = new ObjectManager($this);

        $this->metadataPoolMock = $this->createMock(MetadataPool::class);
        $this->selectMock = $this->createMock(Select::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->entityMetadataInterfaceMock = $this->createMock(EntityMetadataInterface::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);

        $this->metadataPoolMock
            ->expects($this->any())
            ->method('getMetadata')
            ->with(CategoryInterface::class)
            ->willReturn($this->entityMetadataInterfaceMock);

        $this->entityMetadataInterfaceMock
            ->expects($this->any())
            ->method('getEntityConnectionName')
            ->willReturn($entityConnectionName);

        $this->resourceConnectionMock
            ->expects($this->any())
            ->method('getConnectionByName')
            ->with($entityConnectionName)
            ->willReturn($this->connectionMock);

        $this->entityMetadataInterfaceMock
            ->expects($this->any())
            ->method('getLinkField')
            ->willReturn($this->categoryLinkField);

        $this->resourceConnectionMock
            ->expects($this->any())
            ->method('getTableName')
            ->with($this->tableName)
            ->willReturnArgument(0);

        $this->saveHandlerObject = $this->objectManager->getObject(
            SaveHandler::class,
            ['metadataPool' => $this->metadataPoolMock, 'resourceConnection' => $this->resourceConnectionMock]
        );
    }

    /**
     * Preparing for execution lookupStoreIds
     *
     * @param $categoryId
     * @param array $expectedStoreIds
     * @return array
     */
    private function prepareLookupStoreIds($categoryId, $expectedStoreIds = [1, 2, 3])
    {
        $identifierField = 'category_id';

        $this->connectionMock
            ->expects($this->once())
            ->method('select')
            ->willReturn($this->selectMock);

        $this->selectMock
            ->expects($this->once())
            ->method('from')
            ->with(['fas' => $this->tableName], 'store_ids')
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
            ->willReturn($expectedStoreIds);

        return $expectedStoreIds;
    }

    /**
     * Prepare atricle mock
     *
     * @param $categoryId
     * @param array $storeIds
     * @return Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareCategoryMock($categoryId, $storeIds = [1, 2, 3])
    {
        $categoryMock = $this->createMock(Category::class);

        $categoryMock
            ->expects($this->any())
            ->method('getCategoryId')
            ->willReturn($categoryId);

        $categoryMock
            ->expects($this->any())
            ->method('getStoreIds')
            ->willReturn($storeIds);

        $categoryMock
            ->expects($this->any())
            ->method('getData')
            ->with($this->categoryLinkField)
            ->willReturn($this->categoryLinkFieldValue);

        return $categoryMock;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @covers SaveHandler::lookupStoreIds
     */
    public function testLookupStoreId()
    {
        $categoryId = 3;

        $storeIds = $this->prepareLookupStoreIds($categoryId);

        $this->assertEquals($storeIds, $this->saveHandlerObject->lookupStoreIds($categoryId));
    }

    /**
     * Delete exist relations
     *
     * @covers  SaveHandler::execute
     * @depends testLookupStoreId
     */
    public function testDeleteRelation()
    {
        $categoryId = 3;
        $newStores = [1, 2];
        $oldStores = [1, 2, 3, 4, 5];
        $categoryMock = $this->prepareCategoryMock($categoryId, $newStores);

        $this->prepareLookupStoreIds($categoryId, $oldStores);

        $expectedWhere = [
            $this->categoryLinkField . ' = ?' => $this->categoryLinkFieldValue,
            'store_ids IN (?)' => array_diff($oldStores, $newStores)
        ];

        $this->connectionMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->tableName, $expectedWhere)
            ->willReturn(1);

        $this->connectionMock
            ->expects($this->never())
            ->method('insertMultiple');

        $this->assertEquals($categoryMock, $this->saveHandlerObject->execute($categoryMock));
    }

    /**
     * Insert new relations
     *
     * @covers  SaveHandler::execute
     * @depends testLookupStoreId
     */
    public function testInsertRelation()
    {
        $categoryId = 3;
        $newStores = [1, 2, 3, 5, 6];
        $oldStores = [1, 2, 5];
        $data = [];
        $categoryMock = $this->prepareCategoryMock($categoryId, $newStores);

        $this->prepareLookupStoreIds($categoryId, $oldStores);

        $this->connectionMock
            ->expects($this->never())
            ->method('delete');

        foreach (array_diff($newStores, $oldStores) as $storeId) {
            $data[] = [
                $this->categoryLinkField => (int)$this->categoryLinkFieldValue,
                'store_ids' => $storeId
            ];
        }

        $this->connectionMock
            ->expects($this->once())
            ->method('insertMultiple')
            ->with($this->tableName, $data);

        $this->assertEquals($categoryMock, $this->saveHandlerObject->execute($categoryMock));
    }

    /**
     * Insert and delete relations
     *
     * @covers  SaveHandler::execute
     * @depends testLookupStoreId
     * @depends testInsertRelation
     * @depends testDeleteRelation
     */
    public function testInsertAndDeleteRelation()
    {
        $categoryId = 3;
        $newStores = [1, 2, 3];
        $oldStores = [4, 5, 6];
        $data = [];
        $categoryMock = $this->prepareCategoryMock($categoryId, $newStores);

        $this->prepareLookupStoreIds($categoryId, $oldStores);

        $expectedWhere = [
            $this->categoryLinkField . ' = ?' => $this->categoryLinkFieldValue,
            'store_ids IN (?)' => array_diff($oldStores, $newStores)
        ];

        $this->connectionMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->tableName, $expectedWhere)
            ->willReturn(1);

        foreach (array_diff($newStores, $oldStores) as $storeId) {
            $data[] = [
                $this->categoryLinkField => (int)$this->categoryLinkFieldValue,
                'store_ids' => $storeId
            ];
        }

        $this->connectionMock
            ->expects($this->once())
            ->method('insertMultiple')
            ->with($this->tableName, $data);

        $this->assertEquals($categoryMock, $this->saveHandlerObject->execute($categoryMock));
    }

    /**
     * Relations was not changed
     *
     * @covers  SaveHandler::execute
     * @depends testLookupStoreId
     * @depends testInsertRelation
     * @depends testDeleteRelation
     */
    public function testNotChangedRelations()
    {
        $categoryId = 3;
        $newStores = [1, 2, 3];
        $oldStores = [1, 2, 3];
        $categoryMock = $this->prepareCategoryMock($categoryId, $newStores);

        $this->prepareLookupStoreIds($categoryId, $oldStores);

        $this->connectionMock
            ->expects($this->never())
            ->method('delete');

        $this->connectionMock
            ->expects($this->never())
            ->method('insertMultiple');

        $this->assertEquals($categoryMock, $this->saveHandlerObject->execute($categoryMock));
    }
}
