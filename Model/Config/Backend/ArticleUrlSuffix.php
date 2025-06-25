<?php
namespace Aheadworks\FaqFree\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Validator\AbstractValidator;
use Magento\Framework\Validator\Exception as ValidatorException;

class ArticleUrlSuffix extends Value
{
    /**
     * @var AbstractValidator
     */
    private $validator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractValidator $validator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractValidator $validator,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->validator = $validator;
    }

    /**
     * Validate article url suffix
     */
    public function validateBeforeSave()
    {
        $value = $this->getValue();
        if (!$this->validator->isValid($value)) {
            throw new ValidatorException(__('Article URL suffix contains disallowed characters'));
        }

        return parent::validateBeforeSave();
    }
}
