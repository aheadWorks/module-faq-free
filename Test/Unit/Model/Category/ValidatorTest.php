<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\Category;

use Aheadworks\FaqFree\Model\Category;
use Aheadworks\FaqFree\Model\Category\Validator;
use Aheadworks\FaqFree\Model\UrlKeyValidator;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for Validator
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ValidatorTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var UrlKeyValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlKeyValidatorMock;

    /**
     * @var Validator
     */
    private $validatorObject;

    /**
     * Initialize validator
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->urlKeyValidatorMock = $this->createMock(UrlKeyValidator::class);

        $this->validatorObject = $this->objectManager->getObject(
            Validator::class,
            ['urlKeyValidator' => $this->urlKeyValidatorMock]
        );
    }

    /**
     * Prepare valid Category
     *
     * @param array $skip - Invalid fields
     * @param array $urlKeyValidatorError - Errors from UrkKeyValidator
     *
     * @return Category|\PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareValidCategory(array $skip = [], array $urlKeyValidatorError = [])
    {
        $categoryMock = $this->createMock(Category::class);

        if (in_array('title', $skip)) {
            $categoryMock
                ->expects($this->once())
                ->method('getName')
                ->willReturn('');
        } else {
            $categoryMock
                ->expects($this->once())
                ->method('getName')
                ->willReturn('Not empty title');
        }

        if (in_array('url_key', $skip)) {
            $categoryMock
                ->expects($this->once())
                ->method('getUrlKey')
                ->willReturn('');
        } else {
            $categoryMock
                ->expects($this->once())
                ->method('getUrlKey')
                ->willReturn('http://example.com/valid/url');
        }

        if (in_array('sort_order', $skip)) {
            $categoryMock
                ->expects($this->atLeastOnce())
                ->method('getSortOrder')
                ->willReturn('text');
        } else {
            $categoryMock
                ->expects($this->atLeastOnce())
                ->method('getSortOrder')
                ->willReturn(5);
        }

        if (in_array('num_articles', $skip)) {
            $categoryMock
                ->expects($this->any())
                ->method('getNumArticlesToDisplay')
                ->willReturn('text');
        } else {
            $categoryMock
                ->expects($this->any())
                ->method('getNumArticlesToDisplay')
                ->willReturn(10);
        }

        if (in_array('url_key_validator', $skip)) {
            $this->assertNotEmpty($urlKeyValidatorError);

            $this->urlKeyValidatorMock
                ->expects($this->once())
                ->method('isValid')
                ->willReturn(false);

            $this->urlKeyValidatorMock
                ->expects($this->once())
                ->method('getMessages')
                ->willReturn($urlKeyValidatorError);
        } else {
            $this->urlKeyValidatorMock
                ->expects($this->once())
                ->method('isValid')
                ->willReturn(true);
            $this->urlKeyValidatorMock
                ->expects($this->never())
                ->method('getMessages');
        }

        return $categoryMock;
    }

    /**
     * Return true if category is invalid and expected errors is equals to messages from validator
     *
     * @param array $expectedErrors
     * @return bool
     */
    private function invalidCategory($expectedErrors = [])
    {
        if (in_array('url_key_validator', array_keys($expectedErrors))) {
            $categoryMock = $this->prepareValidCategory(array_keys($expectedErrors), [
                'url_key_validator' => $expectedErrors['url_key_validator']
            ]);
        } else {
            $categoryMock = $this->prepareValidCategory(array_keys($expectedErrors));
        }

        $this->assertFalse($this->validatorObject->isValid($categoryMock));

        return $expectedErrors == $this->validatorObject->getMessages();
    }

    /**
     * Category is valid
     *
     * @covers Validator::isValid
     */
    public function testValidCategory()
    {
        $categoryMock = $this->prepareValidCategory();

        $this->assertTrue($this->validatorObject->isValid($categoryMock));
        $this->assertEmpty($this->validatorObject->getMessages());
    }

    /**
     * Category is invalid: title is empty
     *
     * @covers  Validator::isValid
     * @depends testValidCategory
     */
    public function testEmptyTitle()
    {
        $expected = ['title' => __('Title can\'t be empty.')];

        $this->assertTrue($this->invalidCategory($expected));
    }

    /**
     * Category is invalid: url_key is empty
     *
     * @covers  Validator::isValid
     * @depends testValidCategory
     */
    public function testEmptyUrlKey()
    {
        $expected = ['url_key' => __('Url key can\'t be empty.')];

        $this->assertTrue($this->invalidCategory($expected));
    }

    /**
     * Category data is invalid: sort_order is text
     *
     * @covers  Validator::isValid
     * @depends testValidCategory
     */
    public function testTextSortOrder()
    {
        $expected = ['sort_order' => __('Sort order must contain only digits.')];

        $this->assertTrue($this->invalidCategory($expected));
    }

    /**
     * Category data is invalid: num_articles is text
     *
     * @covers  Validator::isValid
     * @depends testValidCategory
     */
    public function testTextNumArticles()
    {
        $expected = ['num_articles' => __('Number of articles to display must contain only digits.')];

        $this->assertTrue($this->invalidCategory($expected));
    }

    /**
     * Category data is invalid: url_key
     *
     * @covers  Validator::isValid
     * @depends testValidCategory
     */
    public function testInvalidUrlKey()
    {
        $expected = ['url_key_validator' => 'url key is invalid'];

        $this->assertTrue($this->invalidCategory($expected));
    }
}
