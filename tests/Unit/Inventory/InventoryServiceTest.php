<?php

namespace mirrorps\Yii2Taler\Tests\Unit\Inventory;

use mirrorps\Yii2Taler\Inventory\InventoryService;
use mirrorps\Yii2Taler\Taler;
use PHPUnit\Framework\TestCase;
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
use Taler\Api\Dto\RelativeTime;
use Taler\Taler as TalerClient;

class InventoryServiceTest extends TestCase
{
    private Taler $taler;
    private InventoryClient $inventoryClient;
    private InventoryService $service;

    protected function setUp(): void
    {
        $this->inventoryClient = $this->createMock(InventoryClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('inventory')->willReturn($this->inventoryClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new InventoryService($this->taler);
    }

    public function testTalerInventoriesReturnsServiceAndMemoizes(): void
    {
        $component = new Taler(['baseUrl' => 'https://example.com']);

        $this->assertInstanceOf(InventoryService::class, $component->inventories());
        $this->assertSame($component->inventories(), $component->inventories());
    }

    public function testGetInventoryClientReturnsSameInstance(): void
    {
        $this->assertSame($this->service->getInventoryClient(), $this->service->getInventoryClient());
    }

    public function testGetCategoriesDelegatesToClient(): void
    {
        $response = $this->createMock(CategoryListResponse::class);
        $headers = ['X-Test' => '1'];

        $this->inventoryClient
            ->expects($this->once())
            ->method('getCategories')
            ->with($headers)
            ->willReturn($response);

        $this->assertSame($response, $this->service->getCategories($headers));
    }

    public function testGetCategoryDelegatesToClient(): void
    {
        $response = $this->createMock(CategoryProductList::class);

        $this->inventoryClient
            ->expects($this->once())
            ->method('getCategory')
            ->with(7, [])
            ->willReturn($response);

        $this->assertSame($response, $this->service->getCategory(7));
    }

    public function testCreateCategoryDelegatesToClient(): void
    {
        $request = new CategoryCreateRequest(name: 'Food');
        $response = $this->createMock(CategoryCreatedResponse::class);

        $this->inventoryClient
            ->expects($this->once())
            ->method('createCategory')
            ->with($request, [])
            ->willReturn($response);

        $this->assertSame($response, $this->service->createCategory($request));
    }

    public function testUpdateAndDeleteCategoryDelegateToClient(): void
    {
        $request = new CategoryCreateRequest(name: 'Food Updated');

        $this->inventoryClient
            ->expects($this->once())
            ->method('updateCategory')
            ->with(3, $request, []);

        $this->inventoryClient
            ->expects($this->once())
            ->method('deleteCategory')
            ->with(3, []);

        $this->service->updateCategory(3, $request);
        $this->service->deleteCategory(3);
    }

    public function testProductMutationsDelegateToClient(): void
    {
        $createDetails = new ProductAddDetail(
            product_id: 'p1',
            description: 'Fresh coffee',
            unit: 'piece',
            price: 'KUDOS:1.00',
            total_stock: 10,
            product_name: 'Coffee'
        );
        $patchDetails = new ProductPatchDetail(
            description: 'Fresh coffee XL',
            unit: 'piece',
            price: 'KUDOS:1.20',
            total_stock: 11,
            product_name: 'Coffee XL'
        );

        $this->inventoryClient
            ->expects($this->once())
            ->method('createProduct')
            ->with($createDetails, []);

        $this->inventoryClient
            ->expects($this->once())
            ->method('updateProduct')
            ->with('p1', $patchDetails, []);

        $this->inventoryClient
            ->expects($this->once())
            ->method('deleteProduct')
            ->with('p1', []);

        $this->service->createProduct($createDetails);
        $this->service->updateProduct('p1', $patchDetails);
        $this->service->deleteProduct('p1');
    }

    public function testGetProductsAndGetProductDelegateToClient(): void
    {
        $productsResponse = $this->createMock(InventorySummaryResponse::class);
        $productResponse = $this->createMock(ProductDetail::class);
        $request = new GetProductsRequest(limit: 5);

        $this->inventoryClient
            ->expects($this->once())
            ->method('getProducts')
            ->with($request, [])
            ->willReturn($productsResponse);

        $this->inventoryClient
            ->expects($this->once())
            ->method('getProduct')
            ->with('coffee', [])
            ->willReturn($productResponse);

        $this->assertSame($productsResponse, $this->service->getProducts($request));
        $this->assertSame($productResponse, $this->service->getProduct('coffee'));
    }

    public function testGetPosAndLockProductDelegateToClient(): void
    {
        $posResponse = $this->createMock(FullInventoryDetailsResponse::class);
        $lockRequest = new LockRequest(
            lock_uuid: '123e4567-e89b-12d3-a456-426614174000',
            duration: new RelativeTime(10_000_000),
            quantity: 1
        );

        $this->inventoryClient
            ->expects($this->once())
            ->method('getPos')
            ->with([])
            ->willReturn($posResponse);

        $this->inventoryClient
            ->expects($this->once())
            ->method('lockProduct')
            ->with('coffee', $lockRequest, []);

        $this->assertSame($posResponse, $this->service->getPos());
        $this->service->lockProduct('coffee', $lockRequest);
    }

    public function testAsyncMethodsDelegateToClient(): void
    {
        $categoryRequest = new CategoryCreateRequest(name: 'Food');
        $createDetails = new ProductAddDetail(
            product_id: 'p1',
            description: 'Fresh coffee',
            unit: 'piece',
            price: 'KUDOS:1.00',
            total_stock: 10,
            product_name: 'Coffee'
        );
        $patchDetails = new ProductPatchDetail(
            description: 'Fresh coffee XL',
            unit: 'piece',
            price: 'KUDOS:1.20',
            total_stock: 11,
            product_name: 'Coffee XL'
        );
        $productsRequest = new GetProductsRequest(limit: 5);
        $lockRequest = new LockRequest(
            lock_uuid: '123e4567-e89b-12d3-a456-426614174000',
            duration: new RelativeTime(10_000_000),
            quantity: 1
        );

        $this->inventoryClient->expects($this->once())->method('getCategoriesAsync')->with([])->willReturn('p1');
        $this->inventoryClient->expects($this->once())->method('getCategoryAsync')->with(1, [])->willReturn('p2');
        $this->inventoryClient->expects($this->once())->method('createCategoryAsync')->with($categoryRequest, [])->willReturn('p3');
        $this->inventoryClient->expects($this->once())->method('updateCategoryAsync')->with(1, $categoryRequest, [])->willReturn('p4');
        $this->inventoryClient->expects($this->once())->method('deleteCategoryAsync')->with(1, [])->willReturn('p5');
        $this->inventoryClient->expects($this->once())->method('createProductAsync')->with($createDetails, [])->willReturn('p6');
        $this->inventoryClient->expects($this->once())->method('updateProductAsync')->with('p1', $patchDetails, [])->willReturn('p7');
        $this->inventoryClient->expects($this->once())->method('getProductsAsync')->with($productsRequest, [])->willReturn('p8');
        $this->inventoryClient->expects($this->once())->method('getProductAsync')->with('p1', [])->willReturn('p9');
        $this->inventoryClient->expects($this->once())->method('deleteProductAsync')->with('p1', [])->willReturn('p10');
        $this->inventoryClient->expects($this->once())->method('getPosAsync')->with([])->willReturn('p11');
        $this->inventoryClient->expects($this->once())->method('lockProductAsync')->with('p1', $lockRequest, [])->willReturn('p12');

        $this->assertSame('p1', $this->service->getCategoriesAsync());
        $this->assertSame('p2', $this->service->getCategoryAsync(1));
        $this->assertSame('p3', $this->service->createCategoryAsync($categoryRequest));
        $this->assertSame('p4', $this->service->updateCategoryAsync(1, $categoryRequest));
        $this->assertSame('p5', $this->service->deleteCategoryAsync(1));
        $this->assertSame('p6', $this->service->createProductAsync($createDetails));
        $this->assertSame('p7', $this->service->updateProductAsync('p1', $patchDetails));
        $this->assertSame('p8', $this->service->getProductsAsync($productsRequest));
        $this->assertSame('p9', $this->service->getProductAsync('p1'));
        $this->assertSame('p10', $this->service->deleteProductAsync('p1'));
        $this->assertSame('p11', $this->service->getPosAsync());
        $this->assertSame('p12', $this->service->lockProductAsync('p1', $lockRequest));
    }
}
