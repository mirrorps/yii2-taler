<?php

namespace mirrorps\Yii2Taler\Inventory;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\Inventory\Dto\CategoryCreateRequest;
use Taler\Api\Inventory\Dto\CategoryCreatedResponse;
use Taler\Api\Inventory\Dto\CategoryListResponse;
use Taler\Api\Inventory\Dto\CategoryProductList;
use Taler\Api\Inventory\Dto\FullInventoryDetailsResponse;
use Taler\Api\Inventory\Dto\GetProductsRequest;
use Taler\Api\Inventory\Dto\InventorySummaryResponse;
use Taler\Api\Inventory\Dto\LockRequest;
use Taler\Api\Inventory\Dto\ProductAddDetail;
use Taler\Api\Inventory\Dto\ProductDetail;
use Taler\Api\Inventory\Dto\ProductPatchDetail;
use Taler\Api\Inventory\InventoryClient;
use yii\base\Component;

/**
 * InventoryService - Yii2 service component for the GNU Taler Inventory API.
 */
class InventoryService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var InventoryClient|null Lazily resolved InventoryClient */
    private ?InventoryClient $_inventoryClient = null;

    /**
     * @param Taler $taler The parent Taler component
     * @param array $config Yii2 component config
     */
    public function __construct(Taler $taler, array $config = [])
    {
        $this->_taler = $taler;
        parent::__construct($config);
    }

    /**
     * Returns the underlying InventoryClient, creating it on first access.
     */
    public function getInventoryClient(): InventoryClient
    {
        if ($this->_inventoryClient === null) {
            $this->_inventoryClient = $this->_taler->getClient()->inventory();
        }

        return $this->_inventoryClient;
    }

    public function getCategories(array $headers = []): CategoryListResponse|array
    {
        return $this->getInventoryClient()->getCategories($headers);
    }

    public function getCategoriesAsync(array $headers = []): mixed
    {
        return $this->getInventoryClient()->getCategoriesAsync($headers);
    }

    public function getCategory(int $categoryId, array $headers = []): CategoryProductList|array
    {
        return $this->getInventoryClient()->getCategory($categoryId, $headers);
    }

    public function getCategoryAsync(int $categoryId, array $headers = []): mixed
    {
        return $this->getInventoryClient()->getCategoryAsync($categoryId, $headers);
    }

    public function createCategory(CategoryCreateRequest $request, array $headers = []): CategoryCreatedResponse|array
    {
        return $this->getInventoryClient()->createCategory($request, $headers);
    }

    public function createCategoryAsync(CategoryCreateRequest $request, array $headers = []): mixed
    {
        return $this->getInventoryClient()->createCategoryAsync($request, $headers);
    }

    public function updateCategory(int $categoryId, CategoryCreateRequest $request, array $headers = []): void
    {
        $this->getInventoryClient()->updateCategory($categoryId, $request, $headers);
    }

    public function updateCategoryAsync(int $categoryId, CategoryCreateRequest $request, array $headers = []): mixed
    {
        return $this->getInventoryClient()->updateCategoryAsync($categoryId, $request, $headers);
    }

    public function deleteCategory(int $categoryId, array $headers = []): void
    {
        $this->getInventoryClient()->deleteCategory($categoryId, $headers);
    }

    public function deleteCategoryAsync(int $categoryId, array $headers = []): mixed
    {
        return $this->getInventoryClient()->deleteCategoryAsync($categoryId, $headers);
    }

    public function createProduct(ProductAddDetail $details, array $headers = []): void
    {
        $this->getInventoryClient()->createProduct($details, $headers);
    }

    public function createProductAsync(ProductAddDetail $details, array $headers = []): mixed
    {
        return $this->getInventoryClient()->createProductAsync($details, $headers);
    }

    public function updateProduct(string $productId, ProductPatchDetail $details, array $headers = []): void
    {
        $this->getInventoryClient()->updateProduct($productId, $details, $headers);
    }

    public function updateProductAsync(string $productId, ProductPatchDetail $details, array $headers = []): mixed
    {
        return $this->getInventoryClient()->updateProductAsync($productId, $details, $headers);
    }

    public function getProducts(?GetProductsRequest $request = null, array $headers = []): InventorySummaryResponse|array
    {
        return $this->getInventoryClient()->getProducts($request, $headers);
    }

    public function getProductsAsync(?GetProductsRequest $request = null, array $headers = []): mixed
    {
        return $this->getInventoryClient()->getProductsAsync($request, $headers);
    }

    public function getProduct(string $productId, array $headers = []): ProductDetail|array
    {
        return $this->getInventoryClient()->getProduct($productId, $headers);
    }

    public function getProductAsync(string $productId, array $headers = []): mixed
    {
        return $this->getInventoryClient()->getProductAsync($productId, $headers);
    }

    public function deleteProduct(string $productId, array $headers = []): void
    {
        $this->getInventoryClient()->deleteProduct($productId, $headers);
    }

    public function deleteProductAsync(string $productId, array $headers = []): mixed
    {
        return $this->getInventoryClient()->deleteProductAsync($productId, $headers);
    }

    public function getPos(array $headers = []): FullInventoryDetailsResponse|array
    {
        return $this->getInventoryClient()->getPos($headers);
    }

    public function getPosAsync(array $headers = []): mixed
    {
        return $this->getInventoryClient()->getPosAsync($headers);
    }

    public function lockProduct(string $productId, LockRequest $request, array $headers = []): void
    {
        $this->getInventoryClient()->lockProduct($productId, $request, $headers);
    }

    public function lockProductAsync(string $productId, LockRequest $request, array $headers = []): mixed
    {
        return $this->getInventoryClient()->lockProductAsync($productId, $request, $headers);
    }
}
