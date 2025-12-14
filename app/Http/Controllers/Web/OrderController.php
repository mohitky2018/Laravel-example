<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Core\DTOs\OrderData;
use App\Core\Interfaces\UserRepositoryInterface;
use App\Core\Services\OrderService;
use App\Core\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Web Controller for Order CRUD operations.
 * 
 * Handles web-based endpoints for order management.
 * Returns Blade views and redirects with flash messages.
 */
class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly ProductService $productService,
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Display a listing of orders.
     *
     * @return View
     */
    public function index(): View
    {
        $orders = $this->orderService->getPaginatedOrders();

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     *
     * @return View
     */
    public function create(): View
    {
        $users = $this->userRepository->getAll();
        $products = $this->productService->getActiveProducts();
        $statuses = Order::getStatuses();

        return view('orders.create', compact('users', 'products', 'statuses'));
    }

    /**
     * Store a newly created order.
     *
     * @param StoreOrderRequest $request
     * @return RedirectResponse
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        try {
            $orderData = OrderData::fromRequest($request);
            $this->orderService->createOrder($orderData);

            return redirect()
                ->route('orders.index')
                ->with('success', 'Order created successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified order.
     *
     * @param int $id
     * @return View|RedirectResponse
     */
    public function show(int $id): View|RedirectResponse
    {
        $order = $this->orderService->findOrder($id);

        if ($order === null) {
            return redirect()
                ->route('orders.index')
                ->with('error', 'Order not found.');
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Update the order status.
     *
     * @param UpdateOrderRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(UpdateOrderRequest $request, int $id): RedirectResponse
    {
        try {
            $this->orderService->updateOrderStatus($id, $request->input('status'));

            return redirect()
                ->route('orders.show', $id)
                ->with('success', 'Order status updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified order.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->orderService->deleteOrder($id);

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
