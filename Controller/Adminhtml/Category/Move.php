<?php
namespace Aheadworks\FaqFree\Controller\Adminhtml\Category;

use Aheadworks\FaqFree\Api\CategoryRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\DataObject;

class Move extends Action
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var JsonFactory
     */
    private $jsonResultFactory;

    /**
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->categoryRepository = $categoryRepository;
        $this->jsonResultFactory = $jsonFactory;
    }

    /**
     * Move Category to other parent
     *
     * @return Json
     */
    public function execute()
    {
        /** @var Json $result */
        $resultJson = $this->jsonResultFactory->create();
        $categoriesData = $this->getRequest()->getParam('nodes_data', []);

        $result = ['success' => false];
        if (!empty($categoriesData)) {
            try {
                foreach ($categoriesData as $categoryData) {
                    $categoryDataObject = new DataObject($categoryData);
                    $targetId = (int)$categoryDataObject->getTargetId();
                    $parentId = (int)$categoryDataObject->getParentId();
                    $sortOrder = (int)$categoryDataObject->getSortOrder();
                    $path = $categoryDataObject->getPath();
                    $category = $this->categoryRepository->getById($targetId);
                    $category
                        ->setParentId($parentId)
                        ->setSortOrder($sortOrder)
                        ->setPath($path);
                    $this->categoryRepository->save($category);
                }
                $result['success'] = true;
            } catch (\Exception $exception) {
                $result['message'] = $exception->getMessage();
            }
        } else {
            $result['message'] = __('No categories data provided.');
        }

        return $resultJson->setData($result);
    }
}
