<?php

namespace Aheadworks\FaqFree\Controller\Adminhtml\Category;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Aheadworks\FaqFree\Model\ImageUploader;

/**
 * FAQ category image upload controller
 */
class Upload extends AbstractAction
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @param Context $context
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    /**
     * Upload file controller action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $files = $this->getRequest()->getFiles()->toArray();
        try {
            $result = $this->imageUploader->saveFileToTmpDir(key($files));
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
