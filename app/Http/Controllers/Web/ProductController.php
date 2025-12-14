<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\DTOs\ProductData;
use App\Core\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Web Controller for Product CRUD operations.
 * 
 * Handles web-based endpoints for product management.
 * Returns Blade views and redirects with flash messages.
 */
class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {
    }

    /**
     * Display a listing of products.
     *
     * @return View
     */
    public function index(): View
    {
        $products = $this->productService->getPaginatedProducts();

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return View
     */
    public function create(): View
    {
        return view('products.create');
    }

    /**
     * Store a newly created product.
     *
     * @param StoreProductRequest $request
     * @return RedirectResponse
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $productData = ProductData::fromRequest($request);
        $this->productService->createProduct($productData);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function show(int $id): View|RedirectResponse
    {
        $product = $this->productService->findProduct($id);

        if ($product === null) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Product not found.');
        }

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function edit(int $id): View|RedirectResponse
    {
        $product = $this->productService->findProduct($id);

        if ($product === null) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Product not found.');
        }

        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product.
     *
     * @param UpdateProductRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $productData = ProductData::fromRequest($request);
        $this->productService->updateProduct($id, $productData);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->productService->deleteProduct($id);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
