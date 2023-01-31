<?php

namespace Aheadworks\FaqFree\Test\Unit\Model;

use Aheadworks\FaqFree\Model\Category;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for Category
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Category
     */
    private $categoryObject;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Category
     */
    private $categoryMock;

    /**
     * Initialize model
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->categoryObject = $this->objectManager->getObject(Category::class, [
            'data' => [
                'category_id' => 1,
                'name' => 'lorem ipsim',
                'is_enable' => 3,
                'url_key' => 'http://example.com/',
                'store_ids' => 4,
                'update_at' => '2014-01-01 01:04:06',
                'created_at' => '2014-02-01 01:04:06',
                'num_articles_to_display' => 2,
                'sort_order' => 5,
                'category_icon' => 'Lorem ipsum dolor sit amet.',
                'meta_title' => 'Lorem ipsum dolot sit amet.',
                'meta_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing.',
                'article_list_icon' => 'Maecenas at ligula eu',
            ]
        ]);

        $this->categoryMock = $this->createMock(Category::class);
    }

    /**
     * Get category id
     *
     * @covers Category::getCategoryId
     * @param null|mixed $data - expected value
     */
    public function testGetCategoryId($data = null)
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($data ?: 1);

        $this->assertEquals($this->categoryMock->getCategoryId(), $this->categoryObject->getCategoryId());
    }

    /**
     * Set article id
     *
     * @covers  Category::setCategoryId
     * @depends testGetCategoryId
     */
    public function testSetCategoryId()
    {
        $articleId = 3;

        $this->assertInstanceOf(Category::class, $this->categoryObject->setCategoryId($articleId));
        $this->assertEquals($articleId, $this->categoryObject->getData('category_id'));
        $this->testGetCategoryId($articleId);
    }

    /**
     * Get identities
     *
     * @covers Category::getIdentities
     */
    public function testGetIdentities()
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getIdentities')
            ->willReturn(['faq_category_1']);

        $this->assertEquals($this->categoryMock->getIdentities(), $this->categoryObject->getIdentities());
    }

    /**
     * Get name
     *
     * @covers Category::getName
     * @param null|mixed $data - expected value
     */
    public function testGetName($data = null)
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getName')
            ->willReturn($data ?: 'lorem ipsim');

        $this->assertEquals($this->categoryMock->getName(), $this->categoryObject->getName());
    }

    /**
     * Set name
     *
     * @covers  Category::setName
     * @depends testGetName
     */
    public function testSetName()
    {
        $data = 'Proin in ligula a orci condimentum tempor.';

        $this->assertInstanceOf(Category::class, $this->categoryObject->setName($data));
        $this->assertEquals($data, $this->categoryObject->getData('name'));
        $this->testGetName($data);
    }

    /**
     * Get url key
     *
     * @covers Category::getUrlKey
     * @param null|mixed $data - expected value
     */
    public function testGetUrlKey($data = null)
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getUrlKey')
            ->willReturn($data ?: 'http://example.com/');

        $this->assertEquals($this->categoryMock->getUrlKey(), $this->categoryObject->getUrlKey());
    }

    /**
     * Set url key
     *
     * @covers  Category::setUrlKey
     * @depends testGetUrlKey
     */
    public function testSetUrlKey()
    {
        $data = 'http://example.com/test';

        $this->assertInstanceOf(Category::class, $this->categoryObject->setUrlKey($data));
        $this->assertEquals($data, $this->categoryObject->getData('url_key'));
        $this->testGetUrlKey($data);
    }

    /**
     * Get meta title
     *
     * @covers Category::getMetaTitle
     * @param null|mixed $data - expected value
     */
    public function testGetMetaTitle($data = null)
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getMetaTitle')
            ->willReturn($data ?: 'Lorem ipsum dolot sit amet.');

        $this->assertEquals($this->categoryMock->getMetaTitle(), $this->categoryObject->getMetaTitle());
    }

    /**
     * Set meta title
     *
     * @covers  Category::setMetaTitle
     * @depends testGetMetaTitle
     */
    public function testSetMetaTitle()
    {
        $data = 'Suspendisse rutrum, enim id tristique dapibus';

        $this->assertInstanceOf(Category::class, $this->categoryObject->setMetaTitle($data));
        $this->assertEquals($data, $this->categoryObject->getData('meta_title'));
        $this->testGetMetaTitle($data);
    }

    /**
     * Get meta description
     *
     * @covers Category::getMetaDescription
     * @param null|mixed $data - expected value
     */
    public function testGetMetaDescription($data = null)
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getMetaDescription')
            ->willReturn($data ?: 'Lorem ipsum dolor sit amet, consectetur adipiscing.');

        $this->assertEquals($this->categoryMock->getMetaDescription(), $this->categoryObject->getMetaDescription());
    }

    /**
     * Set meta description
     *
     * @covers  Category::setMetaDescription
     * @depends testGetMetaDescription
     */
    public function testSetMetaDescription()
    {
        $data = 'tortor ligula sodales turpis, vitae cursus enim velit non justo';

        $this->assertInstanceOf(Category::class, $this->categoryObject->setMetaDescription($data));
        $this->assertEquals($data, $this->categoryObject->getData('meta_description'));
        $this->testGetMetaDescription($data);
    }

    /**
     * Get sort order
     *
     * @covers Category::getSortOrder
     * @param null|mixed $data - expected value
     */
    public function testGetSortOrder($data = null)
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getSortOrder')
            ->willReturn($data ?: 5);

        $this->assertEquals($this->categoryMock->getSortOrder(), $this->categoryObject->getSortOrder());
    }

    /**
     * Set
     *
     * @covers  Category::setSortOrder
     * @depends testGetSortOrder
     */
    public function testSetSortOrder()
    {
        $data = 10;

        $this->assertInstanceOf(Category::class, $this->categoryObject->setSortOrder($data));
        $this->assertEquals($data, $this->categoryObject->getData('sort_order'));
        $this->testGetSortOrder($data);
    }

    /**
     * Get store ids
     *
     * @covers Category::getStoreIds
     * @param null|mixed $data - expected value
     */
    public function testGetStoreIds($data = null)
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getStoreIds')
            ->willReturn($data ? [$data] : [4]);

        $this->assertEquals($this->categoryMock->getStoreIds(), $this->categoryObject->getStoreIds());
    }

    /**
     * Set store ids
     *
     * @covers  Category::setStoreIds
     * @depends testGetStoreIds
     */
    public function testSetStoreIds()
    {
        $data = 21;

        $this->assertInstanceOf(Category::class, $this->categoryObject->setStoreIds($data));
        $this->assertEquals($data, $this->categoryObject->getData('store_ids'));
        $this->testGetStoreIds($data);
    }

    /**
     * Get numbers of articles to display
     *
     * @covers Category::getNumArticlesToDisplay
     * @param null|mixed $data - expected value
     */
    public function testGetNumArticlesToDisplay($data = null)
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getNumArticlesToDisplay')
            ->willReturn($data ?: 2);

        $this->assertEquals(
            $this->categoryMock->getNumArticlesToDisplay(),
            $this->categoryObject->getNumArticlesToDisplay()
        );
    }

    /**
     * Set numbers of articles to display
     *
     * @covers  Category::setNumArticlesToDisplay
     * @depends testGetNumArticlesToDisplay
     */
    public function testSetNumArticlesToDisplay()
    {
        $data = 23;

        $this->assertInstanceOf(Category::class, $this->categoryObject->setNumArticlesToDisplay($data));
        $this->assertEquals($data, $this->categoryObject->getData('num_articles_to_display'));
        $this->testGetNumArticlesToDisplay($data);
    }

    /**
     * Get category icon
     *
     * @covers Category::getCategoryIcon
     * @param null|mixed $data - expected value
     */
    public function testGetCategoryIcon($data = null)
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getCategoryIcon')
            ->willReturn($data ?: 'Lorem ipsum dolor sit amet.');

        $this->assertEquals($this->categoryMock->getCategoryIcon(), $this->categoryObject->getCategoryIcon());
    }

    /**
     * Set category icon
     *
     * @covers  Category::setCategoryIcon
     * @depends testGetCategoryIcon
     */
    public function testSetCategoryIcon()
    {
        $data = 'Aliquam laoreet lorem eros, non dignissim est mattis nec.';

        $this->assertInstanceOf(Category::class, $this->categoryObject->setCategoryIcon($data));
        $this->assertEquals($data, $this->categoryObject->getData('category_icon'));
        $this->testGetCategoryIcon($data);
    }

    /**
     * Get article icon list
     *
     * @covers Category::getArticleListIcon
     * @param null|mixed $data - expected value
     */
    public function testGetArticleListIcon($data = null)
    {
        $this->categoryMock
            ->expects($this->once())
            ->method('getArticleListIcon')
            ->willReturn($data ?: 'Maecenas at ligula eu');

        $this->assertEquals($this->categoryMock->getArticleListIcon(), $this->categoryObject->getArticleListIcon());
    }

    /**
     * Set article icon list
     *
     * @covers  Category::setArticleListIcon
     * @depends testGetArticleListIcon
     */
    public function testSetArticleListIcon()
    {
        $data = 'Vestibulum imperdiet enim ut venenatis luctus.';

        $this->assertInstanceOf(Category::class, $this->categoryObject->setArticleListIcon($data));
        $this->assertEquals($data, $this->categoryObject->getData('article_list_icon'));
        $this->testGetArticleListIcon($data);
    }

    /**
     * Get isEnable value
     *
     * @covers Category::getIsEnable
     * @param null|mixed $data - expected value
     */
    public function testGetIsEnable($data = null)
    {
        $this->assertTrue(is_bool($this->categoryObject->getIsEnable()));

        $this->categoryMock
            ->expects($this->once())
            ->method('getIsEnable')
            ->willReturn($data !== null ? $data : true);

        $this->assertEquals($this->categoryMock->getIsEnable(), $this->categoryObject->getIsEnable());
    }

    /**
     * Set isEnabled to true
     *
     * @covers  Category::setIsEnable
     * @depends testGetIsEnable
     */
    public function testSetIsEnabledTrue()
    {
        $data = true;

        $this->assertInstanceOf(Category::class, $this->categoryObject->setIsEnable($data));
        $this->assertEquals($data, $this->categoryObject->getData('is_enable'));
        $this->testGetIsEnable($data);
    }

    /**
     * Set isEnabled to false
     *
     * @covers  Category::setIsEnable
     * @depends testGetIsEnable
     */
    public function testSetIsEnabledFalse()
    {
        $data = false;

        $this->assertInstanceOf(Category::class, $this->categoryObject->setIsEnable($data));
        $this->assertEquals($data, $this->categoryObject->getData('is_enable'));
        $this->testGetIsEnable($data);
    }

    /**
     * Get updated at
     *
     * @covers Category::getUpdatedAt
     * @param null|mixed $data - expected value
     */
    public function testGetUpdatedAt($data = null)
    {
        $data = $data ?: '2014-01-01 01:04:06';

        $this->categoryMock
            ->expects($this->once())
            ->method('getUpdatedAt')
            ->willReturn($data);

        $this->assertEquals($this->categoryMock->getUpdatedAt(), $this->categoryObject->getUpdatedAt());

        $this->assertTrue($this->validateDate($this->categoryObject->getUpdatedAt()));
    }

    /**
     * Set updated at
     *
     * @covers  Category::setUpdatedAt
     * @depends testGetUpdatedAt
     */
    public function testSetUpdatedAt()
    {
        $data = date('Y-m-d H:i:s');

        $this->assertInstanceOf(Category::class, $this->categoryObject->setUpdatedAt($data));
        $this->assertEquals($data, $this->categoryObject->getData('update_at'));
        $this->testGetUpdatedAt($data);
    }

    /**
     * Get created at
     *
     * @covers Category::getCreatedAt
     * @param null|mixed $data - expected value
     */
    public function testGetCreatedAt($data = null)
    {
        $data = $data ?: '2014-02-01 01:04:06';

        $this->categoryMock
            ->expects($this->once())
            ->method('getCreatedAt')
            ->willReturn($data);

        $this->assertEquals($this->categoryMock->getCreatedAt(), $this->categoryObject->getCreatedAt());

        $this->assertTrue($this->validateDate($this->categoryObject->getCreatedAt()));
    }

    /**
     * Set created at
     *
     * @covers  Category::setCreatedAt
     * @depends testGetCreatedAt
     */
    public function testSetCreatedAt()
    {
        $data = date('Y-m-d H:i:s', strtotime('-1 day'));

        $this->assertInstanceOf(Category::class, $this->categoryObject->setCreatedAt($data));
        $this->assertEquals($data, $this->categoryObject->getData('created_at'));
        $this->testGetCreatedAt($data);
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
}
