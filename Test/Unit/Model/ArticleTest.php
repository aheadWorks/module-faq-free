<?php

namespace Aheadworks\FaqFree\Test\Unit\Model;

use Aheadworks\FaqFree\Model\Article;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for Article
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ArticleTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Article
     */
    private $articleObject;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Article
     */
    private $articleMock;

    /**
     * Initialize model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->articleObject = $this->objectManager->getObject(Article::class, [
            'data' => [
                'article_id' => 1,
                'category_id' => 2,
                'views_count' => 3,
                'store_ids' => 4,
                'is_enable' => 1,
                'updated_at' => '2014-01-01 01:04:06',
                'created_at' => '2014-02-01 01:04:06',
                'title' => 'title',
                'url_key' => 'http://example.com/',
                'content' => 'Lorem ipsum dolor sit amet.',
                'meta_title' => 'Lorem ipsum dolot sit amet.',
                'meta_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing.',
                'sort_order' => 7
            ]
        ]);

        $this->articleMock = $this->createMock(Article::class);
    }

    /**
     * Get article id
     *
     * @covers Article::getArticleId
     * @param null|mixed $data - expected value
     */
    public function testGetArticleId($data = null)
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getArticleId')
            ->willReturn($data ?: 1);

        $this->assertEquals($this->articleMock->getArticleId(), $this->articleObject->getArticleId());
    }

    /**
     * Set article id
     *
     * @covers  Article::setArticleId
     * @depends testGetArticleId
     */
    public function testSetArticleId()
    {
        $articleId = 3;

        $this->assertInstanceOf(Article::class, $this->articleObject->setArticleId($articleId));
        $this->assertEquals($articleId, $this->articleObject->getData('article_id'));
        $this->testGetArticleId($articleId);
    }

    /**
     * Get identities
     *
     * @covers Article::getIdentities
     */
    public function testGetIdentities()
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getIdentities')
            ->willReturn(['faq_article_1']);

        $this->assertEquals($this->articleMock->getIdentities(), $this->articleObject->getIdentities());
    }

    /**
     * Get category id
     *
     * @covers Article::getCategoryId
     * @param null|mixed $data - expected value
     */
    public function testGetCategoryId($data = null)
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($data ?: 2);

        $this->assertEquals($this->articleMock->getCategoryId(), $this->articleObject->getCategoryId());
    }

    /**
     * Set category id
     *
     * @covers  Article::setCategoryId
     * @depends testGetCategoryId
     */
    public function testSetCategoryId()
    {
        $data = 10;

        $this->assertInstanceOf(Article::class, $this->articleObject->setCategoryId($data));
        $this->assertEquals($data, $this->articleObject->getData('category_id'));
        $this->testGetCategoryId($data);
    }

    /**
     * Get view count
     *
     * @covers Article::getViewCount
     * @param null $data - expected value
     */
    public function testGetViewCount($data = null)
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getViewCount')
            ->willReturn($data ?: 3);

        $this->assertEquals($this->articleMock->getViewCount(), $this->articleObject->getViewCount());
    }

    /**
     * Set view count
     *
     * @covers  Article::setViewsCount
     * @depends testGetViewCount
     */
    public function testSetViewsCount()
    {
        $data = 25;

        $this->assertInstanceOf(Article::class, $this->articleObject->setViewsCount($data));
        $this->assertEquals($data, $this->articleObject->getData('views_count'));
        $this->testGetViewCount($data);
    }

    /**
     * Get store ids
     *
     * @covers Article::getStoreIds
     * @param null|mixed $data - expected value
     */
    public function testGetStoreIds($data = null)
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getStoreIds')
            ->willReturn($data ? [$data] : [4]);

        $this->assertEquals($this->articleMock->getStoreIds(), $this->articleObject->getStoreIds());
    }

    /**
     * Set store ids
     *
     * @covers  Article::setStoreIds
     * @depends testGetStoreIds
     */
    public function testSetStoreIds()
    {
        $data = 21;

        $this->assertInstanceOf(Article::class, $this->articleObject->setStoreIds($data));
        $this->assertEquals($data, $this->articleObject->getData('store_ids'));
        $this->testGetStoreIds($data);
    }

    /**
     * Get isEnabled
     *
     * @covers Article::getIsEnable
     * @param null|mixed $data - expected value
     */
    public function testGetIsEnable($data = null)
    {
        $this->assertTrue(is_bool($this->articleObject->getIsEnable()));

        $this->articleMock
            ->expects($this->once())
            ->method('getIsEnable')
            ->willReturn($data !== null ? $data : true);

        $this->assertEquals($this->articleMock->getIsEnable(), $this->articleObject->getIsEnable());
    }

    /**
     * Set isEnabled to true
     *
     * @covers  Article::setIsEnable
     * @depends testGetIsEnable
     */
    public function testSetIsEnabledTrue()
    {
        $data = true;

        $this->assertInstanceOf(Article::class, $this->articleObject->setIsEnable($data));
        $this->assertEquals($data, $this->articleObject->getData('is_enable'));
        $this->testGetIsEnable($data);
    }

    /**
     * Set isEnabled to false
     *
     * @covers  Article::setIsEnable
     * @depends testGetIsEnable
     * @depends testSetIsEnabledTrue
     */
    public function testSetIsEnabledFalse()
    {
        $data = false;

        $this->assertInstanceOf(Article::class, $this->articleObject->setIsEnable($data));
        $this->assertEquals($data, $this->articleObject->getData('is_enable'));
        $this->testGetIsEnable($data);
    }

    /**
     * Date validator
     *
     * @see https://secure.php.net/manual/en/function.checkdate.php
     * @param $date
     * @param string $format
     * @return bool
     */
    private function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Get updated at
     *
     * @covers Article::getUpdatedAt
     * @param null|mixed $data - expected value
     */
    public function testGetUpdatedAt($data = null)
    {
        $data = $data ?: '2014-01-01 01:04:06';

        $this->articleMock
            ->expects($this->once())
            ->method('getUpdatedAt')
            ->willReturn($data);

        $this->assertEquals($this->articleMock->getUpdatedAt(), $this->articleObject->getUpdatedAt());

        $this->assertTrue($this->validateDate($this->articleObject->getUpdatedAt()));
    }

    /**
     * Set updated at
     *
     * @covers  Article::setUpdatedAt
     * @depends testGetUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $data = date('Y-m-d H:i:s');

        $this->assertInstanceOf(Article::class, $this->articleObject->setUpdatedAt($data));
        $this->assertEquals($data, $this->articleObject->getData('updated_at'));
        $this->testGetUpdatedAt($data);
    }

    /**
     * Get created at
     *
     * @covers Article::getCreatedAt
     * @param null|mixed $data - expected value
     */
    public function testGetCreatedAt($data = null)
    {
        $data = $data ?: '2014-02-01 01:04:06';

        $this->articleMock
            ->expects($this->once())
            ->method('getCreatedAt')
            ->willReturn($data);

        $this->assertEquals($this->articleMock->getCreatedAt(), $this->articleObject->getCreatedAt());

        $this->assertTrue($this->validateDate($this->articleObject->getCreatedAt()));
    }

    /**
     * Set created at
     *
     * @covers  Article::setCreatedAt
     * @depends testGetCreatedAt
     */
    public function testSetCreatedAt()
    {
        $data = date('Y-m-d H:i:s', strtotime('-1 day'));

        $this->assertInstanceOf(Article::class, $this->articleObject->setCreatedAt($data));
        $this->assertEquals($data, $this->articleObject->getData('created_at'));
        $this->testGetCreatedAt($data);
    }

    /**
     * Get title
     *
     * @covers Article::getTitle
     * @param null|mixed $data - expected value
     */
    public function testGetTitle($data = null)
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getTitle')
            ->willReturn($data ?: 'title');

        $this->assertEquals($this->articleMock->getTitle(), $this->articleObject->getTitle());
    }

    /**
     * Set title
     *
     * @covers  Article::setTitle
     * @depends testGetTitle
     */
    public function testSetTitle()
    {
        $title = 'Lorem ipsum dolot sit amet, consectetur adipiscing.';

        $this->assertInstanceOf(Article::class, $this->articleObject->setTitle($title));
        $this->assertEquals($title, $this->articleObject->getData('title'));
        $this->testGetTitle($title);
    }

    /**
     * Get url key
     *
     * @covers Article::getUrlKey
     * @param null|mixed $data - expected value
     */
    public function testGetUrlKey($data = null)
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getUrlKey')
            ->willReturn($data ?: 'http://example.com/');

        $this->assertEquals($this->articleMock->getUrlKey(), $this->articleObject->getUrlKey());
    }

    /**
     * Set url key
     *
     * @covers  Article::setUrlKey
     * @depends testGetUrlKey
     */
    public function testSetUrlKey()
    {
        $data = 'http://example.com/test';

        $this->assertInstanceOf(Article::class, $this->articleObject->setUrlKey($data));
        $this->assertEquals($data, $this->articleObject->getData('url_key'));
        $this->testGetUrlKey($data);
    }

    /**
     * Get content
     *
     * @covers Article::getContent
     * @param null|mixed $data - expected value
     */
    public function testGetContent($data = null)
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($data ?: 'Lorem ipsum dolor sit amet.');

        $this->assertEquals($this->articleMock->getContent(), $this->articleObject->getContent());
    }

    /**
     * Set Content
     *
     * @covers  Article::setContent
     * @depends testGetContent
     */
    public function testSetContent()
    {
        $data = 'Fusce convallis ligula eleifend, laoreet nunc in, mattis risus.';

        $this->assertInstanceOf(Article::class, $this->articleObject->setContent($data));
        $this->assertEquals($data, $this->articleObject->getData('content'));
        $this->testGetContent($data);
    }

    /**
     * Get meta title
     *
     * @covers Article::getMetaTitle
     * @param null|mixed $data - expected value
     */
    public function testGetMetaTitle($data = null)
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getMetaTitle')
            ->willReturn($data ?: 'Lorem ipsum dolot sit amet.');

        $this->assertEquals($this->articleMock->getMetaTitle(), $this->articleObject->getMetaTitle());
    }

    /**
     * Set meta title
     *
     * @covers  Article::setMetaTitle
     * @depends testGetMetaTitle
     */
    public function testSetMetaTitle()
    {
        $data = 'Suspendisse rutrum, enim id tristique dapibus';

        $this->assertInstanceOf(Article::class, $this->articleObject->setMetaTitle($data));
        $this->assertEquals($data, $this->articleObject->getData('meta_title'));
        $this->testGetMetaTitle($data);
    }

    /**
     * Get meta description
     *
     * @covers Article::getMetaDescription
     * @param null|mixed $data - expected value
     */
    public function testGetMetaDescription($data = null)
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getMetaDescription')
            ->willReturn($data ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing.');

        $this->assertEquals($this->articleMock->getMetaDescription(), $this->articleObject->getMetaDescription());
    }

    /**
     * Set meta description
     *
     * @covers  Article::setMetaDescription
     * @depends testGetMetaDescription
     */
    public function testSetMetaDescription()
    {
        $data = 'tortor ligula sodales turpis, vitae cursus enim velit non justo';

        $this->assertInstanceOf(Article::class, $this->articleObject->setMetaDescription($data));
        $this->assertEquals($data, $this->articleObject->getData('meta_description'));
        $this->testGetMetaDescription($data);
    }

    /**
     * Get sort order
     *
     * @covers Article::getSortOrder
     * @param null|mixed $data - expected value
     */
    public function testGetSortOrder($data = null)
    {
        $this->articleMock
            ->expects($this->once())
            ->method('getSortOrder')
            ->willReturn($data ?: 7);

        $this->assertEquals($this->articleMock->getSortOrder(), $this->articleObject->getSortOrder());
    }

    /**
     * Set sort order
     *
     * @covers  Article::setSortOrder
     * @depends testGetSortOrder
     */
    public function testSetSortOrder()
    {
        $data = 10;

        $this->assertInstanceOf(Article::class, $this->articleObject->setSortOrder($data));
        $this->assertEquals($data, $this->articleObject->getData('sort_order'));
        $this->testGetSortOrder($data);
    }
}
