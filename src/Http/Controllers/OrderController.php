<?php

namespace Dashboard\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Coderello\SharedData\Facades\SharedData;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Show order list
     *
     * @param Illuminate\Http\Request $request [{status}]
     * @return Illuminate\Support\Facades\View
     */
    public function showOrderList(Request $request)
    {
        // список заказов
        $collection = Order::with(['client', 'products.product', 'status'])
            ->applyFilter($request->only(['q', 'status']))
            ->latest()
            ->paginate();
        $collection->getCollection()->transform(function ($order, $key) {
            if (!$order->client->phone) {
                $order->client->phone = $order->phone;
            }
            $order->setAppends(['amount']);
            return $order;
        });


        // передача данных на frontend
        SharedData::put([
            'collection' => $collection,
        ]);

        return view('dashboard::pages.order.list', [
            'collection' => $collection,
        ]);
    }

    /**
     * Show order page
     *
     * @param mixed $id
     * @return Illuminate\Support\Facades\View
     */
    public function showOrder($id)
    {
        $order = Order::with([
            'status',
            'client',
            'products',
            'products.product',
            'products.product.image',
            'products.product.category',
        ])->findOrNew($id);

        // dump($categories);

        // передача данных на frontend
        SharedData::put([
            'order' => $order,
        ]);

        return view('dashboard::pages.order.item', [
            'order' => $order,
        ]);
    }
}
