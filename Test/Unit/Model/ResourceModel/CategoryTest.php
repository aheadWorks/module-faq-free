<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\ResourceModel;

use Aheadworks\FaqFree\Api\Data\CategoryInterfaceFactory as CategoryFactory;
use Aheadworks\FaqFree\Model\Category as CategoryModel;
use Aheadworks\FaqFree\Model\Category\Validator;
use Aheadworks\FaqFree\Model\ResourceModel\Category;
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
class CategoryTest extends TestCase
{
    /**
     * Main table const
     */
    public const MAIN_TABLE = 'aw_faq_category';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var CategoryFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryFactoryMock;

    /**
     * @var CategoryModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryMock;

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
     * @var Category
     */
    private $categoryObject;

    /**
     * Initialize resource model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->categoryFactoryMock = $this->createMock(CategoryFactory::class);
        $this->categoryMock = $this->createMock(CategoryModel::class);
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

        $this->categoryObject = $this->objectManager->getObject(
            Category::class,
            [
                'context' => $this->contextMock,
                'metadataPool' => $this->metadataPollMock,
                'entityManager' => $this->entityManagerMock,
                'validator' => $this->validatorMock,
                'categoryFactory' => $this->categoryFactoryMock
            ]
        );
    }

    /**
     * Return Id of Category by Url-key
     *
     * @covers Category::getIdByUrlKey
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

        $this->assertEquals($articleId, $this->categoryObject->getIdByUrlKey($urlKey));
    }

    /**
     * Save object data
     *
     * @covers Category::save
     */
    public function testSave()
    {
        $this->entityManagerMock
            ->expects($this->once())
            ->method('save')
            ->with($this->categoryMock)
            ->willReturn($this->categoryMock);

        $this->assertInstanceOf(Category::class, $this->categoryObject->save($this->categoryMock));
    }

    /**
     * Delete object
     *
     * @covers Category::delete
     */
    public function testDelete()
    {
        $this->entityManagerMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->categoryMock)
            ->willReturn(true);

        $this->assertInstanceOf(Category::class, $this->categoryObject->delete($this->categoryMock));
    }

    /**
     * Load object
     *
     * @covers Category::load
     */
    public function testLoad()
    {
        $categoryId = 3;

        $this->entityManagerMock
            ->expects($this->once())
            ->method('load')
            ->with($this->categoryMock, $categoryId)
            ->willReturn($this->categoryMock);

        $this->assertInstanceOf(Category::class, $this->categoryObject->load($this->categoryMock, $categoryId));
    }

    /**
     * Get validator
     *
     * @covers Category::getValidatorBeforeSave
     */
    public function testGetValidator()
    {
        $this->assertEquals($this->validatorMock, $this->categoryObject->getValidationRulesBeforeSave());
    }
}
