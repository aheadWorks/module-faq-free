<?php

namespace Aheadworks\FaqFree\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;
use Aheadworks\FaqFree\Model\ResourceModel\Article\Collection as ArticleCollection;
use Aheadworks\FaqFree\Model\ResourceModel\Article as ResourceArticle;
use Aheadworks\FaqFree\Model\ResourceModel\Article\CollectionFactory;
use Aheadworks\FaqFree\Api\Data\ArticleSearchResultsInterfaceFactory;
use Aheadworks\FaqFree\Api\Data\ArticleSearchResultsInterface;
use Aheadworks\FaqFree\Api\Data\ArticleInterfaceFactory;
use Aheadworks\FaqFree\Model\ArticleRepository;
use Aheadworks\FaqFree\Model\Article;
use PHPUnit\Framework\TestCase;
use Aheadworks\FaqFree\Model\Article\Converter as ArticleConverter;

/**
 * Test for ArticleRepository
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ArticleRepositoryTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $articleConverterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultsFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $articleSearchResultsMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $articleFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $articleMock;

    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $articleResourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $articleCollectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $articleCollectionMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filterGroupMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $filterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $sortOrderMock;

    /**
     * Initialize repository
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->articleConverterMock = $this->createMock(ArticleConverter::class);
        $this->articleFactoryMock = $this->createMock(ArticleInterfaceFactory::class);
        $this->articleResourceMock = $this->createMock(ResourceArticle::class);
        $this->articleCollectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $this->articleMock = $this->createMock(Article::class);
        $this->filterGroupMock = $this->createMock(FilterGroup::class);
        $this->sortOrderMock = $this->createMock(SortOrder::class);
        $this->articleSearchResultsMock = $this->createMock(ArticleSearchResultsInterface::class);
        $this->articleCollectionMock = $this->createMock(ArticleCollection::class);
        $this->searchResultsFactoryMock = $this->createMock(ArticleSearchResultsInterfaceFactory::class);
        $this->filterMock = $this->createMock(Filter::class);

        $this->articleRepository = $this->objectManager->getObject(
            ArticleRepository::class,
            [
                'resource' => $this->articleResourceMock,
                'articleConverter' => $this->articleConverterMock,
                'articleFactory' => $this->articleFactoryMock,
                'articleCollectionFactory' => $this->articleCollectionFactoryMock,
                'searchResultsFactory' => $this->searchResultsFactoryMock
            ]
        );
    }

    /**
     * @covers ArticleRepository::getById
     */
    public function testGetArticleById()
    {
        $articleId = 3;

        $this->articleFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->articleMock);
        $this->articleResourceMock
            ->expects($this->once())
            ->method('load')
            ->with($this->articleMock, $articleId)
            ->willReturn($this->articleMock);
        $this->articleMock
            ->expects($this->once())
            ->method('getArticleId')
            ->willReturn($articleId);

        $this->assertEquals($this->articleMock, $this->articleRepository->getById($articleId));
    }

    /**
     * Test throwing Exception during execution of
     * ArticleRepository::getById method
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetArticleByIdException()
    {
        $articleId = 3;

        $this->articleFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->articleMock);
        $this->articleResourceMock
            ->expects($this->once())
            ->method('load')
            ->with($this->articleMock, $articleId)
            ->willReturn($this->articleMock);
        $this->articleMock
            ->expects($this->any())
            ->method('getArticleId')
            ->willReturn(false);

        $this->expectException(\Magento\Framework\Exception\NoSuchEntityException::class);

        $this->articleRepository->getById($articleId);
    }

    /**
     * @covers ArticleRepository::save
     */
    public function testSaveArticle()
    {
        $articleId = 3;

        $this->articleResourceMock
            ->expects($this->once())
            ->method('save')
            ->with($this->articleMock)
            ->willReturn($this->articleMock);
        $this->articleMock
            ->expects($this->once())
            ->method('getArticleId')
            ->willReturn($articleId);

        $this->assertEquals($this->articleMock, $this->articleRepository->save($this->articleMock));
    }

    /**
     * Test throwing Exception during execution of
     * ArticleRepository::save method
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveArticleException()
    {
        $this->articleResourceMock
            ->expects($this->once())
            ->method('save')
            ->with($this->articleMock)
            ->willThrowException(new \Exception());

        $this->expectException(\Magento\Framework\Exception\CouldNotSaveException::class);

        $this->articleRepository->save($this->articleMock);
    }

    /**
     * @covers ArticleRepository::delete
     */
    public function testDeleteArticle()
    {
        $this->articleResourceMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->articleMock)
            ->willReturnSelf();
        $this->assertTrue($this->articleRepository->delete($this->articleMock));
    }

    /**
     * Test throwing Exception during execution of
     * ArticleRepository::delete method
     *
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function testDeleteArticleException()
    {
        $this->articleResourceMock
            ->expects($this->once())
            ->method('delete')
            ->with($this->articleMock)
            ->willThrowException(new \Exception());

        $this->expectException(\Magento\Framework\Exception\CouldNotDeleteException::class);

        $this->articleRepository->delete($this->articleMock);
    }

    /**
     * @covers ArticleRepository::getList
     */
    public function testGetListWithEmptyCollection()
    {
        $articleArray = [];
        $this->articleCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->articleCollectionMock);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn(false);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->willReturn(false);
        $this->articleCollectionMock
            ->expects($this->any())
            ->method('getSize')
            ->willReturn(sizeof($articleArray));
        $this->articleCollectionMock
            ->expects($this->any())
            ->method('getItems')
            ->willReturn($articleArray);
        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->articleSearchResultsMock);
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock)
            ->willReturnSelf();
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setItems')
            ->with([])
            ->willReturnSelf();
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(sizeof($articleArray))
            ->willReturnSelf();
        $this->assertEquals(
            $this->articleSearchResultsMock,
            $this->articleRepository->getList($this->searchCriteriaMock)
        );
    }

    /**
     * Test get list
     *
     * @covers  ArticleRepository::getList
     * @depends testGetListWithEmptyCollection
     */
    public function testGetList()
    {
        $articleArray = ['article_id' => 1, 'store_ids' => 2];
        $this->articleCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->articleCollectionMock);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn(false);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->willReturn(false);
        $this->articleCollectionMock
            ->expects($this->any())
            ->method('getSize')
            ->willReturn(sizeof($articleArray));
        $this->articleCollectionMock
            ->expects($this->any())
            ->method('getItems')
            ->willReturn([$this->articleMock]);

        $this->articleConverterMock
            ->expects($this->once())
            ->method('toDataObject')
            ->with($this->articleMock)
            ->willReturn($this->articleMock);

        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->articleSearchResultsMock);
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock)
            ->willReturnSelf();
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setItems')
            ->with([$this->articleMock])
            ->willReturnSelf();
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(sizeof($articleArray))
            ->willReturnSelf();
        $this->assertEquals(
            $this->articleSearchResultsMock,
            $this->articleRepository->getList($this->searchCriteriaMock)
        );
    }

    /**
     * Test ArticleRepository::getList
     * if sort orders are setted
     *
     * @depends testGetList
     */
    public function testGetListWithEmptyCollectionAndSetSortOrders()
    {
        $articleArray = [];
        $this->articleCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->articleCollectionMock);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn(false);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->willReturn([$this->sortOrderMock]);
        $this->sortOrderMock
            ->expects($this->any())
            ->method('getField')
            ->willReturn('article_id');
        $this->sortOrderMock
            ->expects($this->any())
            ->method('getDirection')
            ->willReturn('asc');
        $this->articleCollectionMock
            ->expects($this->any())
            ->method('getSize')
            ->willReturn(sizeof($articleArray));
        $this->articleCollectionMock
            ->expects($this->any())
            ->method('getItems')
            ->willReturn($articleArray);
        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->articleSearchResultsMock);
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock)
            ->willReturnSelf();
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setItems')
            ->with([])
            ->willReturnSelf();
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(sizeof($articleArray))
            ->willReturnSelf();
        $this->assertEquals(
            $this->articleSearchResultsMock,
            $this->articleRepository->getList($this->searchCriteriaMock)
        );
    }

    /**
     * Test ArticleRepository::getList
     * if filter groups are setted
     *
     * @depends testGetList
     * @depends testGetListWithEmptyCollection
     */
    public function testGetListWithSetFilterGroups()
    {
        $articleArray = [];
        $this->articleCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->articleCollectionMock);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$this->filterGroupMock]);
        $this->filterGroupMock
            ->expects($this->any())
            ->method('getFilters')
            ->willReturn([$this->filterMock]);
        $this->filterMock
            ->expects($this->any())
            ->method('getField')
            ->willReturn('is_enable');
        $this->filterMock
            ->expects($this->any())
            ->method('getConditionType')
            ->willReturn('');
        $this->filterMock
            ->expects($this->any())
            ->method('getValue')
            ->willReturn(true);
        $this->articleCollectionMock
            ->expects($this->once())
            ->method('addFieldToFilter')
            ->with(['is_enable'], [['eq' => true]])
            ->willReturnSelf();
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->willReturn(false);
        $this->articleCollectionMock
            ->expects($this->any())
            ->method('getSize')
            ->willReturn(sizeof($articleArray));
        $this->articleCollectionMock
            ->expects($this->any())
            ->method('getItems')
            ->willReturn($articleArray);
        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->articleSearchResultsMock);
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock)
            ->willReturnSelf();
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setItems')
            ->with([])
            ->willReturnSelf();
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(sizeof($articleArray))
            ->willReturnSelf();
        $this->assertEquals(
            $this->articleSearchResultsMock,
            $this->articleRepository->getList($this->searchCriteriaMock)
        );
    }

    /**
     * Test ArticleRepository::getList
     * if store filter is setted
     *
     * @depends testGetList
     * @depends testGetListWithEmptyCollection
     */
    public function testGetListWithSetStoreIdFilter()
    {
        $articleArray = [];
        $this->articleCollectionFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturn($this->articleCollectionMock);
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getFilterGroups')
            ->willReturn([$this->filterGroupMock]);
        $this->filterGroupMock
            ->expects($this->any())
            ->method('getFilters')
            ->willReturn([$this->filterMock]);
        $this->filterMock
            ->expects($this->any())
            ->method('getField')
            ->willReturn('store_ids');
        $this->filterMock
            ->expects($this->any())
            ->method('getValue')
            ->willReturn(1);
        $this->articleCollectionMock
            ->expects($this->once())
            ->method('addStoreFilter')
            ->with(1)
            ->willReturnSelf();
        $this->searchCriteriaMock
            ->expects($this->once())
            ->method('getSortOrders')
            ->willReturn(false);
        $this->articleCollectionMock
            ->expects($this->any())
            ->method('getSize')
            ->willReturn(sizeof($articleArray));
        $this->articleCollectionMock
            ->expects($this->any())
            ->method('getItems')
            ->willReturn($articleArray);
        $this->searchResultsFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->articleSearchResultsMock);
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->searchCriteriaMock)
            ->willReturnSelf();
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setItems')
            ->with([])
            ->willReturnSelf();
        $this->articleSearchResultsMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(sizeof($articleArray))
            ->willReturnSelf();
        $this->assertEquals(
            $this->articleSearchResultsMock,
            $this->articleRepository->getList($this->searchCriteriaMock)
        );
    }
}
