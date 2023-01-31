<?php

namespace Aheadworks\FaqFree\Test\Unit\Model;

use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Model\Category;
use Aheadworks\FaqFree\Model\CategoryRepository;
use Aheadworks\FaqFree\Model\Config;
use Aheadworks\FaqFree\Model\Url;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for UrtTest
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UrlTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlMock;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var CategoryRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRepositoryMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var Store|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeMock;

    /**
     * @var Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryMock;

    /**
     * @var Article|\PHPUnit_Framework_MockObject_MockObject
     */
    private $articleMock;

    /**
     * @var Url
     */
    private $urlObject;

    /**
     * Initialize model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->urlMock = $this->createMock(UrlInterface::class);
        $this->configMock = $this->createMock(Config::class);
        $this->categoryRepositoryMock = $this->createMock(CategoryRepository::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->storeMock = $this->createMock(Store::class);
        $this->articleMock = $this->createMock(Article::class);
        $this->categoryMock = $this->createMock(Category::class);

        $this->urlObject = $this->objectManager->getObject(
            Url::class,
            [
                'url' => $this->urlMock,
                'config' => $this->configMock,
                'categoryRepository' => $this->categoryRepositoryMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * Retrieve Store URL
     *
     * @covers Url::getBaseUrl
     */
    public function testGetBaseUrl()
    {
        $url = 'http://example.com/';

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock
            ->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn($url);

        $this->assertEquals($url, $this->urlObject->getBaseUrl());

        return $url;
    }

    /**
     * Retrieve FAQ route name
     *
     * @covers Url::getFaqRoute
     */
    public function testGetFaqRoute()
    {
        $this->configMock
            ->expects($this->once())
            ->method('getFaqRoute')
            ->willReturn('faq_route');

        $this->assertEquals('faq_route', $this->urlObject->getFaqRoute());
    }

    /**
     * Retrieve FAQ base url
     *
     * @covers  Url::getFaqHomeUrl()
     * @depends testGetBaseUrl
     * @depends testGetFaqRoute
     */
    public function testGetHomeUrl()
    {
        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock
            ->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn('http://example.com/');

        $this->configMock
            ->expects($this->once())
            ->method('getFaqRoute')
            ->willReturn('faq_route');

        $this->assertEquals('http://example.com/faq_route', $this->urlObject->getFaqHomeUrl());
    }

    /**
     * Retrieve FAQ category route
     *
     * @covers  Url::getCategoryUrl
     * @depends testGetFaqRoute
     */
    public function testGetCategoryRoute()
    {
        $this->configMock
            ->expects($this->once())
            ->method('getFaqRoute')
            ->willReturn('faq_route');

        $this->categoryMock
            ->expects($this->once())
            ->method('getUrlKey')
            ->willReturn('url_key');

        $this->assertEquals('faq_route/url_key', $this->urlObject->getCategoryRoute($this->categoryMock));
    }

    /**
     * Retrieve FAQ article route
     *
     * @covers  Url::getArticleRoute
     * @depends testGetFaqRoute
     * @depends testGetCategoryRoute
     */
    public function testGetArticleRoute()
    {
        $categoryId = 3;

        $this->configMock
            ->expects($this->once())
            ->method('getFaqRoute')
            ->willReturn('faq_route');

        $this->categoryMock
            ->expects($this->once())
            ->method('getUrlKey')
            ->willReturn('url_key');

        $this->articleMock
            ->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($categoryId);

        $this->articleMock
            ->expects($this->once())
            ->method('getUrlKey')
            ->willReturn('article_url');

        $this->categoryRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($categoryId)
            ->willReturn($this->categoryMock);

        $this->assertEquals('faq_route/url_key/article_url', $this->urlObject->getArticleRoute($this->articleMock));
    }

    /**
     * Retrieve FAQ category url
     *
     * @covers  Url::getCategoryUrl
     * @depends testGetBaseUrl
     * @depends testGetCategoryRoute
     */
    public function testGetCategoryUrl()
    {
        $url = 'http://example.com/';

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock
            ->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn($url);

        $this->configMock
            ->expects($this->once())
            ->method('getFaqRoute')
            ->willReturn('faq_route');

        $this->categoryMock
            ->expects($this->once())
            ->method('getUrlKey')
            ->willReturn('url_key');

        $this->assertEquals($url . 'faq_route/url_key', $this->urlObject->getCategoryUrl($this->categoryMock));
    }

    /**
     * Retrieve FAQ article url
     *
     * @param bool $isArticleCategoryEnabled
     * @param string $expectedArticleUrl
     * @param string $categoryUrlKey
     * @param string $articleUrlKey
     * @dataProvider getArticleUrlDataProvider
     * @covers  Url::getArticleUrl
     * @depends testGetBaseUrl
     * @depends testGetCategoryRoute
     * @depends testGetCategoryUrl
     */
    public function testGetArticleUrl($isArticleCategoryEnabled, $expectedArticleUrl, $categoryUrlKey, $articleUrlKey)
    {
        $categoryId = 3;
        $url = 'http://example.com/';
        $fullExpectedArticleUrl = $url . $expectedArticleUrl;

        $this->articleMock
            ->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($categoryId);

        $this->articleMock
            ->expects($this->once())
            ->method('getUrlKey')
            ->willReturn($articleUrlKey);

        $this->categoryRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($categoryId)
            ->willReturn($this->categoryMock);

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock
            ->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn($url);

        $this->configMock
            ->expects($this->once())
            ->method('getFaqRoute')
            ->willReturn('faq_route');

        $this->categoryMock
            ->expects($this->atMost(1))
            ->method('getUrlKey')
            ->willReturn($categoryUrlKey);

        $this->categoryMock
            ->expects($this->once())
            ->method('getIsEnable')
            ->willReturn($isArticleCategoryEnabled);

        $this->assertEquals($fullExpectedArticleUrl, $this->urlObject->getArticleUrl($this->articleMock));
    }

    /**
     * @return array[]
     */
    public function getArticleUrlDataProvider()
    {
        return [
            [
                true,
                'faq_route/category_url_key/article_url_key',
                'category_url_key',
                'article_url_key',
            ],
            [
                false,
                'faq_route/article_url_key',
                'category_url_key',
                'article_url_key',
            ]
        ];
    }

    /**
     * Get url of category image icon
     *
     * @covers Url::getCategoryIconUrl
     */
    public function testGetCategoryIconUrl()
    {
        $url = 'http://example.com/media/';
        $mediaName = 'icon.png';

        $this->categoryMock
            ->expects($this->once())
            ->method('getCategoryIcon')
            ->willReturn($mediaName);

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock
            ->expects($this->once())
            ->method('getBaseUrl')
            ->with('media')
            ->willReturn($url);

        $this->assertEquals($url . 'faq/' . $mediaName, $this->urlObject->getCategoryIconUrl($this->categoryMock));
    }

    /**
     * Get url of category image icon
     *
     * @covers Url::getCategoryIconUrl
     */
    public function testGetArticleListIconUrl()
    {
        $url = 'http://example.com/media/';
        $mediaName = 'icon.png';

        $this->categoryMock
            ->expects($this->once())
            ->method('getArticleListIcon')
            ->willReturn($mediaName);

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock
            ->expects($this->once())
            ->method('getBaseUrl')
            ->with('media')
            ->willReturn($url);

        $this->assertEquals($url . 'faq/' . $mediaName, $this->urlObject->getArticleListIconUrl($this->categoryMock));
    }
}
