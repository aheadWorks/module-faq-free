<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\Article;

use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Model\Article\DataProvider;
use Aheadworks\FaqFree\Model\ResourceModel\Article\Collection;
use Aheadworks\FaqFree\Model\ResourceModel\Article\CollectionFactory;
use Magento\Framework\App\Request\DataPersistor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for DataProvider
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProviderTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var DataPersistor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataPersistorMock;

    /**
     * @var Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionMock;

    /**
     * @var array
     */
    private $articleMocks;

    /**
     * @var DataProvider
     */
    private $dataProviderObject;

    /**
     * Initialize model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->dataPersistorMock = $this->createMock(DataPersistor::class);
        $this->collectionMock = $this->objectManager->getCollectionMock(Collection::class, []);

        $this->collectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);

        $this->dataProviderObject = $this->objectManager->getObject(
            DataProvider::class,
            [
                'name' => 'name',
                'primaryFieldName' => 'primary_field_name',
                'requestFieldName' => 'request_field_name',
                'articleCollectionFactory' => $this->collectionFactoryMock,
                'dataPersistor' => $this->dataPersistorMock,
                'meta' => ['meta'],
                'data' => []
            ]
        );

        $this->articleMocks = [];
    }

    /**
     * Prepares Meta
     *
     * @covers DataProvider::prepareMeta
     */
    public function testPrepareMeta()
    {
        $meta = ['field' => 'value'];

        $this->assertEquals($meta, $this->dataProviderObject->prepareMeta($meta));
    }

    /**
     * Prepare article mocks
     *
     * @return array
     */
    private function generateArticleMocks()
    {
        $expected = [];

        for ($i = 0; $i < 3; $i++) {
            $data = ['article_id' => $i];

            $articleMock = $this->createMock(Article::class);

            $articleMock
                ->expects($this->any())
                ->method('getArticleId')
                ->willReturn($data['article_id']);

            $articleMock
                ->expects($this->any())
                ->method('getData')
                ->willReturn($data);

            $this->articleMocks[] = $articleMock;

            $expected[] = $data;
        }

        return $expected;
    }

    /**
     * Get data
     *
     * @covers DataProvider::getData
     */
    public function testGetData()
    {
        $articleMocks = $this->generateArticleMocks();

        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($this->articleMocks);

        $this->dataPersistorMock
            ->expects($this->once())
            ->method('get')
            ->with('faq_article')
            ->willReturn([]);

        $this->assertEquals($articleMocks, $this->dataProviderObject->getData());
        $this->assertEquals($articleMocks, $this->dataProviderObject->getData());
    }

    /**
     * Get data. Data persistor contain article
     *
     * @covers  DataProvider::getData
     * @depends testGetData
     */
    public function testGetDataWithArticleInDataPersistor()
    {
        $articleMocks = $this->generateArticleMocks();

        $data = [
            'article_id' => sizeof($articleMocks) + 1,
        ];

        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn($this->articleMocks);

        $this->dataPersistorMock
            ->expects($this->once())
            ->method('get')
            ->with('faq_article')
            ->willReturn($data);

        $articleMock = $this->createMock(Article::class);

        $articleMock
            ->expects($this->once())
            ->method('setData')
            ->with($data)
            ->willReturnSelf();

        $articleMock
            ->expects($this->once())
            ->method('getArticleId')
            ->willReturn($data['article_id']);

        $articleMock
            ->expects($this->once())
            ->method('getData')
            ->willReturn($data);

        $this->collectionMock
            ->expects($this->once())
            ->method('getNewEmptyItem')
            ->willReturn($articleMock);

        $this->dataPersistorMock
            ->expects($this->once())
            ->method('clear')
            ->with('faq_article');

        $articleMocks[$data['article_id']] = $data;

        $this->assertEquals($articleMocks, $this->dataProviderObject->getData());
        $this->assertEquals($articleMocks, $this->dataProviderObject->getData());
    }
}
