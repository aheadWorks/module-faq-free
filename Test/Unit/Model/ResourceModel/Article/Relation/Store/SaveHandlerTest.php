<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\ResourceModel\Article\Relation\Store;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Model\ResourceModel\Article\Relation\Store\SaveHandler;
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
    private $articleLinkField;

    /**
     * @var string
     */
    private $articleLinkFieldValue;

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
        $this->articleLinkField = 'article_link_field';
        $this->tableName = 'aw_faq_article_store';
        $this->articleLinkFieldValue = '2';

        $this->objectManager = new ObjectManager($this);

        $this->metadataPoolMock = $this->createMock(MetadataPool::class);
        $this->selectMock = $this->createMock(Select::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->entityMetadataInterfaceMock = $this->createMock(EntityMetadataInterface::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);

        $this->metadataPoolMock
            ->expects($this->any())
            ->method('getMetadata')
            ->with(ArticleInterface::class)
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
            ->willReturn($this->articleLinkField);

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
     * @param $articleId
     * @param array $expectedStoreIds
     * @return array
     */
    private function prepareLookupStoreIds($articleId, $expectedStoreIds = [1, 2, 3])
    {
        $identifierField = 'article_id';

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
            ->with('fas.' . $identifierField . ' = :articleId')
            ->willReturnSelf();

        $this->connectionMock
            ->expects($this->once())
            ->method('fetchCol')
            ->with($this->selectMock, ['articleId' => $articleId])
            ->willReturn($expectedStoreIds);

        return $expectedStoreIds;
    }

    /**
     * Prepare atricle mock
     *
     * @param $articleId
     * @param array $storeIds
     * @return Article|\PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareArticleMock($articleId, $storeIds = [1, 2, 3])
    {
        $articleMock = $this->createMock(Article::class);

        $articleMock
            ->expects($this->any())
            ->method('getArticleId')
            ->willReturn($articleId);

        $articleMock
            ->expects($this->any())
            ->method('getStoreIds')
            ->willReturn($storeIds);

        $articleMock
            ->expects($this->any())
            ->method('getData')
            ->with($this->articleLinkField)
            ->willReturn($this->articleLinkFieldValue);

        return $articleMock;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @covers SaveHandler::lookupStoreIds
     */
    public function testLookupStoreId()
    {
        $articleId = 3;

        $storeIds = $this->prepareLookupStoreIds($articleId);

        $this->assertEquals($storeIds, $this->saveHandlerObject->lookupStoreIds($articleId));
    }

    /**
     * Delete exist relations
     *
     * @covers  SaveHandler::execute
     * @depends testLookupStoreId
     */
    public function testDeleteRelation()
    {
        $articleId = 3;
        $newStores = [1, 2];
        $oldStores = [1, 2, 3, 4, 5];
        $articleMock = $this->prepareArticleMock($articleId, $newStores);

        $this->prepareLookupStoreIds($articleId, $oldStores);

        $expectedWhere = [
            $this->articleLinkField . ' = ?' => $this->articleLinkFieldValue,
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

        $this->assertEquals($articleMock, $this->saveHandlerObject->execute($articleMock));
    }

    /**
     * Insert new relations
     *
     * @covers  SaveHandler::execute
     * @depends testLookupStoreId
     */
    public function testInsertRelation()
    {
        $articleId = 3;
        $newStores = [1, 2, 3, 5, 6];
        $oldStores = [1, 2, 5];
        $data = [];
        $articleMock = $this->prepareArticleMock($articleId, $newStores);

        $this->prepareLookupStoreIds($articleId, $oldStores);

        $this->connectionMock
            ->expects($this->never())
            ->method('delete');

        foreach (array_diff($newStores, $oldStores) as $storeId) {
            $data[] = [
                $this->articleLinkField => (int)$this->articleLinkFieldValue,
                'store_ids' => $storeId
            ];
        }

        $this->connectionMock
            ->expects($this->once())
            ->method('insertMultiple')
            ->with($this->tableName, $data);

        $this->assertEquals($articleMock, $this->saveHandlerObject->execute($articleMock));
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
        $articleId = 3;
        $newStores = [1, 2, 3];
        $oldStores = [4, 5, 6];
        $data = [];
        $articleMock = $this->prepareArticleMock($articleId, $newStores);

        $this->prepareLookupStoreIds($articleId, $oldStores);

        $expectedWhere = [
            $this->articleLinkField . ' = ?' => $this->articleLinkFieldValue,
            'store_ids IN (?)' => array_diff($oldStores, $newStores)
        ];

        $this->connectionMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->tableName, $expectedWhere)
            ->willReturn(1);

        foreach (array_diff($newStores, $oldStores) as $storeId) {
            $data[] = [
                $this->articleLinkField => (int)$this->articleLinkFieldValue,
                'store_ids' => $storeId
            ];
        }

        $this->connectionMock
            ->expects($this->once())
            ->method('insertMultiple')
            ->with($this->tableName, $data);

        $this->assertEquals($articleMock, $this->saveHandlerObject->execute($articleMock));
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
        $articleId = 3;
        $newStores = [1, 2, 3];
        $oldStores = [1, 2, 3];
        $articleMock = $this->prepareArticleMock($articleId, $newStores);

        $this->prepareLookupStoreIds($articleId, $oldStores);

        $this->connectionMock
            ->expects($this->never())
            ->method('delete');

        $this->connectionMock
            ->expects($this->never())
            ->method('insertMultiple');

        $this->assertEquals($articleMock, $this->saveHandlerObject->execute($articleMock));
    }
}
