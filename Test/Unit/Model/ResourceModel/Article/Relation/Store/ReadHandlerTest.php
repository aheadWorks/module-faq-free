<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\ResourceModel\Article\Relation\Store;

use Aheadworks\FaqFree\Api\Data\ArticleInterface;
use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Model\ResourceModel\Article\Relation\Store\ReadHandler;
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
     * @param $articleId
     * @return array
     */
    private function prepareLookupStoreIds($articleId)
    {
        $entityConnectionName = 'connection_name';
        $tableName = 'aw_faq_article_store';
        $identifierField = 'article_id';
        $expected = [1, 2, 3, 4];

        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getMetadata')
            ->with(ArticleInterface::class)
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
            ->with('fas.' . $identifierField . ' = :articleId')
            ->willReturnSelf();

        $this->connectionMock
            ->expects($this->once())
            ->method('fetchCol')
            ->with($this->selectMock, ['articleId' => $articleId])
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
        $articleId = 3;

        $storeIds = $this->prepareLookupStoreIds($articleId);

        $this->assertEquals($storeIds, $this->readHandlerObject->lookupStoreIds($articleId));
    }

    /**
     * Load relation
     *
     * @covers  ReadHandler::execute
     * @depends testLookupStoreId
     */
    public function testExecute()
    {
        $articleId = 4;
        $storeIds = $this->prepareLookupStoreIds($articleId);

        /** @var Article|\PHPUnit_Framework_MockObject_MockObject $articleMock */
        $articleMock = $this->createMock(Article::class);

        $articleMock
            ->expects($this->any())
            ->method('getArticleId')
            ->willReturn($articleId);

        $articleMock
            ->expects($this->once())
            ->method('setData')
            ->with('store_ids', $storeIds)
            ->willReturnSelf();

        $this->assertEquals($articleMock, $this->readHandlerObject->execute($articleMock));
    }
}
