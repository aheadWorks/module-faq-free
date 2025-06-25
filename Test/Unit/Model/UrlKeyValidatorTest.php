<?php

namespace Aheadworks\FaqFree\Test\Unit\Model;

use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Model\Category;
use Aheadworks\FaqFree\Model\UrlKeyValidator;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\TypeResolver;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for UrlKeyValidator
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class UrlKeyValidatorTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var UrlKeyValidator
     */
    private $urlKeyValidatorObject;

    /**
     * @var MetadataPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metadataPoolMock;

    /**
     * @var ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceConnectionMock;

    /**
     * @var Article|\PHPUnit_Framework_MockObject_MockObject
     */
    private $articleMock;

    /**
     * @var Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryMock;

    /**
     * @var TypeResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $typeResolverMock;

    /**
     * @var EntityMetadataInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $modelMetadataMock;

    /**
     * @var AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionMock;

    /**
     * @var Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $selectMock;

    /**
     * Initialize Model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->metadataPoolMock = $this->createMock(MetadataPool::class);
        $this->typeResolverMock = $this->createMock(TypeResolver::class);
        $this->articleMock = $this->createMock(Article::class);
        $this->categoryMock = $this->createMock(Category::class);
        $this->selectMock = $this->createMock(Select::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->modelMetadataMock = $this->createMock(EntityMetadataInterface::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);

        $this->urlKeyValidatorObject = $this->objectManager->getObject(
            UrlKeyValidator::class,
            [
                'metadataPool' => $this->metadataPoolMock,
                'resourceConnection' => $this->resourceConnectionMock,
                'typeResolver' => $this->typeResolverMock
            ]
        );
    }

    /**
     * Return message about validation
     *
     * @covers UrlKeyValidator::getMessage
     */
    public function testGetMessage()
    {
        $this->assertEquals('This URL key is invalid.', $this->urlKeyValidatorObject->getMessage());
    }

    /**
     * Prepare isUniqueUrlKey
     *
     * @param Article|Category|\PHPUnit_Framework_MockObject_MockObject $modelMock
     * @param string $urlKey
     * @param bool $exist - Exist in database this url or not
     */
    private function prepareIsUniqueUrlKey($modelMock, $urlKey, $exist)
    {
        $this->assertInstanceOf(AbstractModel::class, $modelMock);

        $modelId = 3;
        $entityType = 'entityType';
        $connectionName = 'connectionName';
        $articleTableName = 'aw_faq_article';
        $categoryTableName = 'aw_faq_category';

        if ($modelMock instanceof Article) {
            $idGetter = 'getArticleId';
            $idField = 'article_id';
            $tableName = $articleTableName;
        } elseif ($modelMock instanceof Category) {
            $idGetter = 'getCategoryId';
            $idField = 'category_id';
            $tableName = $categoryTableName;
        } else {
            $this->fail('Provided model can\'t be tested');
            return;
        }

        $modelMock
            ->expects($this->any())
            ->method('getUrlKey')
            ->willReturn($urlKey);

        $this->typeResolverMock
            ->expects($this->once())
            ->method('resolve')
            ->with($modelMock)
            ->willReturn($entityType);

        $this->metadataPoolMock
            ->expects($this->once())
            ->method('getMetadata')
            ->with($entityType)
            ->willReturn($this->modelMetadataMock);

        $this->modelMetadataMock
            ->expects($this->once())
            ->method('getEntityConnectionName')
            ->willReturn($connectionName);

        $this->resourceConnectionMock
            ->expects($this->once())
            ->method('getConnectionByName')
            ->with($connectionName)
            ->willReturn($this->connectionMock);

        $this->modelMetadataMock
            ->expects($this->once())
            ->method('getEntityTable')
            ->willReturn($tableName);

        $this->resourceConnectionMock
            ->expects($this->once())
            ->method('getTableName')
            ->with($tableName)
            ->willReturn($tableName);

        $this->modelMetadataMock
            ->expects($this->any())
            ->method('getIdentifierField')
            ->willReturn($idField);

        $modelMock
            ->expects($this->once())
            ->method($idGetter)
            ->willReturn($modelId);

        $this->connectionMock
            ->expects($this->any())
            ->method('select')
            ->willReturn($this->selectMock);

        $this->selectMock
            ->expects($this->any())
            ->method('from')
            ->withAnyParameters()
            ->willReturn($this->selectMock);

        $this->selectMock
            ->expects($this->any())
            ->method('where')
            ->withAnyParameters()
            ->willReturn($this->selectMock);

        $this->connectionMock
            ->expects($this->any())
            ->method('fetchAll')
            ->with($this->selectMock)
            ->willReturn($exist ? [$modelId => $urlKey] : []);
    }

    /**
     * Validate url key. Url is valid
     * Model: Article
     *
     * @covers UrlKeyValidator::isValid
     * @covers UrlKeyValidator::isUniqueUrlKey
     * @covers UrlKeyValidator::isNumericUrlKey
     * @covers UrlKeyValidator::isValidUrlKey
     */
    public function testValidArticleUrl()
    {
        $urlKey = 'icon_article-25.png';

        $this->prepareIsUniqueUrlKey($this->articleMock, $urlKey, false);
        $this->assertTrue($this->urlKeyValidatorObject->isValid($this->articleMock));
    }

    /**
     * Validate url key. Url already exist in database
     * Model: Article
     *
     * @covers  UrlKeyValidator::isValid
     * @covers  UrlKeyValidator::isUniqueUrlKey
     * @covers  UrlKeyValidator::isNumericUrlKey
     * @covers  UrlKeyValidator::isValidUrlKey
     * @depends testValidArticleUrl
     */
    public function testAlreadyExistArticleUrlKey()
    {
        $urlKey = 'icon_article-25.png';

        $this->prepareIsUniqueUrlKey($this->articleMock, $urlKey, true);
        $this->assertFalse($this->urlKeyValidatorObject->isValid($this->articleMock));

        $messages = [__('The URL key already exists.')];

        $this->assertEquals($messages, $this->urlKeyValidatorObject->getMessages());
    }

    /**
     * Validate url key. Url is numeric
     * Model: Article
     *
     * @covers  UrlKeyValidator::isValid
     * @covers  UrlKeyValidator::isUniqueUrlKey
     * @covers  UrlKeyValidator::isNumericUrlKey
     * @covers  UrlKeyValidator::isValidUrlKey
     * @depends testValidArticleUrl
     */
    public function testArticleHasNumericKey()
    {
        $urlKey = '123456';

        $this->prepareIsUniqueUrlKey($this->articleMock, $urlKey, false);
        $this->assertFalse($this->urlKeyValidatorObject->isValid($this->articleMock));

        $messages = [__('The URL key cannot be made of only numbers.')];

        $this->assertEquals($messages, $this->urlKeyValidatorObject->getMessages());
    }

    /**
     * Validate url key. Url is numeric and exist in database
     * Model: Article
     *
     * @covers  UrlKeyValidator::isValid
     * @covers  UrlKeyValidator::isUniqueUrlKey
     * @covers  UrlKeyValidator::isNumericUrlKey
     * @covers  UrlKeyValidator::isValidUrlKey
     * @depends testValidArticleUrl
     */
    public function testArticleHasNumericKeyAndAlreadyExistArticleUrlKey()
    {
        $urlKey = '123456';

        $this->prepareIsUniqueUrlKey($this->articleMock, $urlKey, true);
        $this->assertFalse($this->urlKeyValidatorObject->isValid($this->articleMock));

        $messages = [
            __('The URL key cannot be made of only numbers.'),
            __('The URL key already exists.')
        ];

        $this->assertEquals($messages, $this->urlKeyValidatorObject->getMessages());
    }

    /**
     * Validate url key. Url contain disallowed symbols and already exist in database
     * Model: Article
     *
     * @covers  UrlKeyValidator::isValid
     * @covers  UrlKeyValidator::isUniqueUrlKey
     * @covers  UrlKeyValidator::isNumericUrlKey
     * @covers  UrlKeyValidator::isValidUrlKey
     * @depends testValidArticleUrl
     */
    public function testInvalidUrlKeyAndAlreadyExistArticleUrlKey()
    {
        $urlKey = '!@#$%^&*()';

        $this->prepareIsUniqueUrlKey($this->articleMock, $urlKey, true);
        $this->assertFalse($this->urlKeyValidatorObject->isValid($this->articleMock));

        $messages = [
            __('The URL key contains capital letters or disallowed symbols.'),
            __('The URL key already exists.')
        ];

        $this->assertEquals($messages, $this->urlKeyValidatorObject->getMessages());
    }

    /**
     * Validate url key. Url is valid
     * Model: Category
     *
     * @covers UrlKeyValidator::isValid
     * @covers UrlKeyValidator::isUniqueUrlKey
     * @covers UrlKeyValidator::isNumericUrlKey
     * @covers UrlKeyValidator::isValidUrlKey
     */
    public function testValidCategoryUrl()
    {
        $urlKey = 'icon_article-25.png';

        $this->prepareIsUniqueUrlKey($this->categoryMock, $urlKey, false);
        $this->assertTrue($this->urlKeyValidatorObject->isValid($this->categoryMock));
    }

    /**
     * Validate url key. Url already exist in database
     * Model: Article
     *
     * @covers  UrlKeyValidator::isValid
     * @covers  UrlKeyValidator::isUniqueUrlKey
     * @covers  UrlKeyValidator::isNumericUrlKey
     * @covers  UrlKeyValidator::isValidUrlKey
     * @depends testValidCategoryUrl
     */
    public function testAlreadyExistCategoryUrlKey()
    {
        $urlKey = 'icon_article-25.png';

        $this->prepareIsUniqueUrlKey($this->categoryMock, $urlKey, true);
        $this->assertFalse($this->urlKeyValidatorObject->isValid($this->categoryMock));

        $messages = [__('The URL key already exists.')];

        $this->assertEquals($messages, $this->urlKeyValidatorObject->getMessages());
    }

    /**
     * Validate url key. Url is numeric
     * Model: Category
     *
     * @covers  UrlKeyValidator::isValid
     * @covers  UrlKeyValidator::isUniqueUrlKey
     * @covers  UrlKeyValidator::isNumericUrlKey
     * @covers  UrlKeyValidator::isValidUrlKey
     * @depends testValidCategoryUrl
     */
    public function testCategoryHasNumericKey()
    {
        $urlKey = '123456';

        $this->prepareIsUniqueUrlKey($this->categoryMock, $urlKey, false);
        $this->assertFalse($this->urlKeyValidatorObject->isValid($this->categoryMock));

        $messages = [__('The URL key cannot be made of only numbers.')];

        $this->assertEquals($messages, $this->urlKeyValidatorObject->getMessages());
    }

    /**
     * Validate url key. Url is numeric and exist in database
     * Model: Category
     *
     * @covers  UrlKeyValidator::isValid
     * @covers  UrlKeyValidator::isUniqueUrlKey
     * @covers  UrlKeyValidator::isNumericUrlKey
     * @covers  UrlKeyValidator::isValidUrlKey
     * @depends testValidCategoryUrl
     */
    public function testCategoryHasNumericKeyAndAlreadyExistCategoryUrlKey()
    {
        $urlKey = '123456';

        $this->prepareIsUniqueUrlKey($this->categoryMock, $urlKey, true);
        $this->assertFalse($this->urlKeyValidatorObject->isValid($this->categoryMock));

        $messages = [
            __('The URL key cannot be made of only numbers.'),
            __('The URL key already exists.')
        ];

        $this->assertEquals($messages, $this->urlKeyValidatorObject->getMessages());
    }

    /**
     * Validate url key. Url contain disallowed symbols and already exist in database
     * Model: Category
     *
     * @covers  UrlKeyValidator::isValid
     * @covers  UrlKeyValidator::isUniqueUrlKey
     * @covers  UrlKeyValidator::isNumericUrlKey
     * @covers  UrlKeyValidator::isValidUrlKey
     * @depends testValidCategoryUrl
     */
    public function testInvalidUrlKeyAndAlreadyExistCategoryUrlKey()
    {
        $urlKey = '!@#$%^&*()';

        $this->prepareIsUniqueUrlKey($this->categoryMock, $urlKey, true);
        $this->assertFalse($this->urlKeyValidatorObject->isValid($this->categoryMock));

        $messages = [
            __('The URL key contains capital letters or disallowed symbols.'),
            __('The URL key already exists.')
        ];

        $this->assertEquals($messages, $this->urlKeyValidatorObject->getMessages());
    }
}
