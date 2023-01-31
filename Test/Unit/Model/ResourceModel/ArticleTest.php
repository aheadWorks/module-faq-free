<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\ResourceModel;

use Aheadworks\FaqFree\Api\Data\ArticleInterfaceFactory as ArticleFactory;
use Aheadworks\FaqFree\Model\Article as ArticleModel;
use Aheadworks\FaqFree\Model\Article\Validator;
use Aheadworks\FaqFree\Model\ResourceModel\Article;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ArticleTest extends TestCase
{
    /**
     * Main table const
     */
    public const MAIN_TABLE = 'aw_faq_article';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var ArticleFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $articleFactoryMock;

    /**
     * @var ArticleModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $articleMock;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var MetadataPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataPollMock;

    /**
     * @var Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $selectMock;

    /**
     * @var Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextMock;

    /**
     * @var ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceConnectionMock;

    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;

    /**
     * @var Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $validatorMock;

    /**
     * @var Article
     */
    private $articleObject;

    /**
     * Initialize resource model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->articleFactoryMock = $this->createMock(ArticleFactory::class);
        $this->articleMock = $this->createMock(ArticleModel::class);
        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->metadataPollMock = $this->createMock(MetadataPool::class);
        $this->contextMock = $this->createMock(Context::class);
        $this->selectMock = $this->createMock(Select::class);
        $this->validatorMock = $this->createMock(Validator::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);

        $this->contextMock
            ->expects($this->once())
            ->method('getResources')
            ->willReturn($this->resourceConnectionMock);

        $this->resourceConnectionMock
            ->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->connectionMock);

        $this->resourceConnectionMock
            ->expects($this->any())
            ->method('getTableName')
            ->willReturnArgument(0);

        $this->articleObject = $this->objectManager->getObject(
            Article::class,
            [
                'context' => $this->contextMock,
                'metadataPool' => $this->metadataPollMock,
                'entityManager' => $this->entityManagerMock,
                'validator' => $this->validatorMock,
                'articleFactory' => $this->articleFactoryMock
            ]
        );
    }

    /**
     * Return Id of Article by Url-key
     *
     * @covers Article::getIdByUrlKey
     */
    public function testGetIdByUrl()
    {
        $articleId = 3;
        $urlKey = 'url/key';

        $this->connectionMock
            ->expects($this->once())
            ->method('select')
            ->willReturn($this->selectMock);

        $this->selectMock
            ->expects($this->once())
            ->method('from')
            ->with(['cp' => self::MAIN_TABLE])
            ->willReturnSelf();

        $this->selectMock
            ->expects($this->once())
            ->method('where')
            ->with('cp.url_key = ?', $urlKey)
            ->willReturnSelf();

        $this->selectMock
            ->expects($this->once())
            ->method('limit')
            ->with(1)
            ->willReturnSelf();

        $this->connectionMock
            ->expects($this->once())
            ->method('fetchOne')
            ->with($this->selectMock)
            ->willReturn($articleId);

        $this->assertEquals($articleId, $this->articleObject->getIdByUrlKey($urlKey));
    }

    /**
     * Save object data
     *
     * @covers Article::save
     */
    public function testSave()
    {
        $this->entityManagerMock
            ->expects($this->once())
            ->method('save')
            ->with($this->articleMock)
            ->willReturn($this->articleMock);

        $this->assertInstanceOf(Article::class, $this->articleObject->save($this->articleMock));
    }

    /**
     * Delete object
     *
     * @covers Article::delete
     */
    public function testDelete()
    {
        $this->entityManagerMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->articleMock)
            ->willReturn(true);

        $this->assertInstanceOf(Article::class, $this->articleObject->delete($this->articleMock));
    }

    /**
     * Load object
     *
     * @covers Article::load
     */
    public function testLoad()
    {
        $articleId = 3;

        $this->entityManagerMock
            ->expects($this->once())
            ->method('load')
            ->with($this->articleMock, $articleId)
            ->willReturn($this->articleMock);

        $this->assertInstanceOf(Article::class, $this->articleObject->load($this->articleMock, $articleId));
    }

    /**
     * Get validator
     *
     * @covers Article::getValidatorBeforeSave
     */
    public function testGetValidator()
    {
        $this->assertEquals($this->validatorMock, $this->articleObject->getValidationRulesBeforeSave());
    }
}
