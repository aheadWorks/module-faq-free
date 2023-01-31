<?php
namespace Aheadworks\FaqFree\Model\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;

class Locator
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param Registry $registry
     * @param ProductRepositoryInterface $productRepository
     * @param RequestInterface $request
     */
    public function __construct(
        Registry $registry,
        ProductRepositoryInterface $productRepository,
        RequestInterface $request
    ) {
        $this->registry = $registry;
        $this->productRepository = $productRepository;
        $this->request = $request;
    }

    /**
     * Locate product
     *
     * @return ProductInterface|Product
     * @throws NoSuchEntityException
     * @throws NotFoundException
     */
    public function getProduct()
    {
        $product = $this->registry->registry('product');
        if (!$product) {
            $productId = $this->request->getParam('id');
            if ($productId) {
                $product = $this->productRepository->getById($productId);
            }
        }
        if (!$product) {
            throw new NotFoundException(__('The product was not located'));
        }
        return $product;
    }
}
