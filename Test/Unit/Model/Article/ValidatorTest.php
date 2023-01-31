<?php

namespace Aheadworks\FaqFree\Test\Unit\Model\Article;

use Aheadworks\FaqFree\Model\Article;
use Aheadworks\FaqFree\Model\Article\Validator;
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
     * Prepare valid Article
     *
     * @param array $skip - Invalid fields
     * @param array $urlKeyValidatorError - Errors from UrkKeyValidator
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return Article|\PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareValidArticle(array $skip = [], array $urlKeyValidatorError = [])
    {
        $articleMock = $this->createMock(Article::class);

        if (in_array('title', $skip)) {
            $articleMock
                ->expects($this->once())
                ->method('getTitle')
                ->willReturn('');
        } else {
            $articleMock
                ->expects($this->once())
                ->method('getTitle')
                ->willReturn('Not empty title');
        }

        if (in_array('url_key', $skip)) {
            $articleMock
                ->expects($this->once())
                ->method('getUrlKey')
                ->willReturn('');
        } else {
            $articleMock
                ->expects($this->once())
                ->method('getUrlKey')
                ->willReturn('http://example.com/valid/url');
        }

        if (in_array('sort_order', $skip)) {
            $articleMock
                ->expects($this->atLeastOnce())
                ->method('getSortOrder')
                ->willReturn('text');
        } else {
            $articleMock
                ->expects($this->atLeastOnce())
                ->method('getSortOrder')
                ->willReturn(5);
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

        return $articleMock;
    }

    /**
     * Return true if article is invalid and expected errors is equals to messages from validator
     *
     * @param array $expectedErrors
     * @return bool
     */
    private function invalidArticle($expectedErrors = [])
    {
        if (in_array('url_key_validator', array_keys($expectedErrors))) {
            $articleMock = $this->prepareValidArticle(
                array_keys($expectedErrors),
                ['url_key_validator' => $expectedErrors['url_key_validator']]
            );
        } else {
            $articleMock = $this->prepareValidArticle(array_keys($expectedErrors));
        }

        $this->assertFalse($this->validatorObject->isValid($articleMock));

        return $expectedErrors == $this->validatorObject->getMessages();
    }

    /**
     * Article is valid
     *
     * @covers Validator::isValid
     */
    public function testValidArticle()
    {
        $articleMock = $this->prepareValidArticle();

        $this->assertTrue($this->validatorObject->isValid($articleMock));
        $this->assertEmpty($this->validatorObject->getMessages());
    }

    /**
     * Article is invalid: title is empty
     *
     * @covers  Validator::isValid
     * @depends testValidArticle
     */
    public function testEmptyTitle()
    {
        $expected = ['title' => __('Title can\'t be empty.')];

        $this->assertTrue($this->invalidArticle($expected));
    }

    /**
     * Article is invalid: url_key is empty
     *
     * @covers  Validator::isValid
     * @depends testValidArticle
     */
    public function testEmptyUrlKey()
    {
        $expected = ['url_key' => __('Url key can\'t be empty.')];

        $this->assertTrue($this->invalidArticle($expected));
    }

    /**
     * Article data is invalid: sort_order is text
     *
     * @covers  Validator::isValid
     * @depends testValidArticle
     */
    public function testTextSortOrder()
    {
        $expected = ['sort_order' => __('Sort order must contain only digits.')];

        $this->assertTrue($this->invalidArticle($expected));
    }

    /**
     * Article data is invalid: url_key
     *
     * @covers  Validator::isValid
     * @depends testValidArticle
     */
    public function testInvalidUrlKey()
    {
        $expected = ['url_key_validator' => 'url key is invalid'];

        $this->assertTrue($this->invalidArticle($expected));
    }

    /**
     * Article is invalid
     * Error "votes_not" is not reachable if votes_yes or total_votes is invalid
     *
     * @covers  Validator::isValid
     * @depends testEmptyTitle
     * @depends testEmptyUrlKey
     * @depends testTextSortOrder
     * @depends testInvalidUrlKey
     */
    public function testInvalidArticle()
    {
        $expected = [
            'title' => __('Title can\'t be empty.'),
            'url_key' => __('Url key can\'t be empty.'),
            'sort_order' => __('Sort order must contain only digits.'),
            'url_key_validator' => 'url key is invalid'
        ];

        $this->assertTrue($this->invalidArticle($expected));
    }
}
