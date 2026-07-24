<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\Store;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function index()
    {
        $transfers = Transfer::with(['fromStore', 'toStore', 'requestedBy'])
            ->latest()->paginate(20);
        return view('transfers.index', compact('transfers'));
    }

    public function create()
    {
        $stores   = Store::where('is_active', true)->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('transfers.create', compact('stores', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_store_id'       => 'required|exists:stores,id',
            'to_store_id'         => 'required|exists:stores,id|different:from_store_id',
            'required_date'       => 'nullable|date',
            'reason'              => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.product_id'  => 'required|exists:products,id',
            'items.*.quantity'    => 'required|numeric|min:0.001',
            'items.*.unit'        => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($request) {
            $no = 'TR-' . date('Ymd') . '-' . str_pad(Transfer::count() + 1, 4, '0', STR_PAD_LEFT);

            $transfer = Transfer::create([
                'transfer_no'  => $no,
                'from_store_id'=> $request->from_store_id,
                'to_store_id'  => $request->to_store_id,
                'requested_by' => Auth::id(),
                'required_date'=> $request->required_date,
                'reason'       => $request->reason,
                'status'       => 'draft',
            ]);

            foreach ($request->items as $item) {
                $transfer->items()->create([
                    'product_id'          => $item['product_id'],
                    'requested_quantity'  => $item['quantity'],
                    'unit'                => $item['unit'],
                ]);
            }
        });

        return redirect()->route('transfers.index')->with('success', 'Transfer request created.');
    }

    public function show(Transfer $transfer)
    {
        $transfer->load(['fromStore', 'toStore', 'requestedBy', 'approvedBy', 'items.product']);
        return view('transfers.show', compact('transfer'));
    }

    public function approve(Transfer $transfer)
    {
        $transfer->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Transfer approved.');
    }

    public function complete(Transfer $transfer)
    {
        $transfer->update([
            'status'      => 'completed',
            'received_by' => Auth::id(),
            'received_at' => now(),
        ]);
        return back()->with('success', 'Transfer completed.');
    }

    public function reject(Request $request, Transfer $transfer)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $transfer->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'Transfer rejected.');
    }
}
