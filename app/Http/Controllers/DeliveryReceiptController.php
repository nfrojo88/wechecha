<?php

namespace App\Http\Controllers;

use App\Models\DeliveryReceipt;
use App\Models\PurchaseOrder;
use App\Models\Store;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DeliveryReceiptController extends Controller
{
    public function __construct(private InventoryService $inventoryService) {}

    public function index()
    {
        $receipts = DeliveryReceipt::with(['purchaseOrder', 'store', 'receivedBy'])
            ->latest()->paginate(20);
        return view('procurement.delivery-receipts.index', compact('receipts'));
    }

    public function create()
    {
        $pos    = PurchaseOrder::where('status', '!=', 'cancelled')->get();
        $stores = Store::where('is_active', true)->get();
        return view('procurement.delivery-receipts.create', compact('pos', 'stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id'         => 'required|exists:purchase_orders,id',
            'store_id'                  => 'required|exists:stores,id',
            'received_date'             => 'required|date',
            'challan_no'                => 'nullable|string|max:100',
            'vehicle_no'                => 'nullable|string|max:50',
            'notes'                     => 'nullable|string',
            'items'                     => 'required|array|min:1',
            'items.*.product_id'        => 'required|exists:products,id',
            'items.*.po_item_id'        => 'nullable|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|numeric|min:0.001',
            'items.*.accepted_quantity' => 'required|numeric|min:0',
            'items.*.unit'              => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $no = 'DR-' . date('Ymd') . '-' . str_pad(DeliveryReceipt::count() + 1, 4, '0', STR_PAD_LEFT);

            $dr = DeliveryReceipt::create([
                'dr_no'             => $no,
                'purchase_order_id' => $request->purchase_order_id,
                'received_by'       => Auth::id(),
                'store_id'          => $request->store_id,
                'received_date'     => $request->received_date,
                'notes'             => $request->notes,
                'challan_no'        => $request->challan_no,
                'vehicle_no'        => $request->vehicle_no,
                'status'            => 'verified',
            ]);

            foreach ($request->items as $item) {
                $dr->items()->create([
                    'product_id'        => $item['product_id'],
                    'po_item_id'        => $item['po_item_id'] ?? null,
                    'quantity_received' => $item['quantity_received'],
                    'accepted_quantity' => $item['accepted_quantity'],
                    'rejected_quantity' => $item['quantity_received'] - $item['accepted_quantity'],
                    'unit'              => $item['unit'],
                    'rejection_reason'  => $item['rejection_reason'] ?? null,
                ]);

                // Update inventory via service
                if ($item['accepted_quantity'] > 0) {
                    $this->inventoryService->stockIn(
                        $request->store_id,
                        $item['product_id'],
                        $item['accepted_quantity'],
                        $item['unit_price'] ?? 0,
                        'purchase_receipt',
                        Auth::id(),
                        'delivery_receipt',
                        $dr->id
                    );
                }
            }
        });

        return redirect()->route('delivery-receipts.index')->with('success', 'Delivery Receipt recorded and inventory updated.');
    }

    public function show(DeliveryReceipt $deliveryReceipt)
    {
        $deliveryReceipt->load(['purchaseOrder.supplier', 'store', 'receivedBy', 'items.product']);
        return view('procurement.delivery-receipts.show', compact('deliveryReceipt'));
    }
}
