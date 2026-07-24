<?php

namespace App\Http\Controllers;

use App\Models\IpcRecord;
use App\Models\SubconAgreement;
use App\Services\Contract\IpcCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class IpcRecordController extends Controller
{
    protected IpcCalculator $ipcCalculator;

    public function __construct(IpcCalculator $ipcCalculator)
    {
        $this->ipcCalculator = $ipcCalculator;
    }
    public function index()
    {
        $ipcs = IpcRecord::with(['agreement.subcontractor'])->latest()->paginate(20);
        return view('procurement.ipc.index', compact('ipcs'));
    }

    public function create(Request $request)
    {
        $agreements = SubconAgreement::where('status', 'active')->get();
        $selectedAgreement = null;
        if ($request->agreement_id) {
            $selectedAgreement = SubconAgreement::with('items')->find($request->agreement_id);
        }
        return view('procurement.ipc.create', compact('agreements', 'selectedAgreement'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agreement_id' => 'required|exists:subcon_agreements,id',
            'period_from'  => 'required|date',
            'period_to'    => 'required|date|after_or_equal:period_from',
            'items'        => 'required|array|min:1',
            'items.*.agreement_item_id' => 'required|exists:subcon_agreement_items,id',
            'items.*.previous_qty'      => 'required|numeric|min:0',
            'items.*.current_qty'       => 'required|numeric|min:0',
            'items.*.unit_rate'         => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $ipc = IpcRecord::create([
                'ipc_no'       => 'IPC-' . date('Ym') . '-' . str_pad(IpcRecord::count() + 1, 3, '0', STR_PAD_LEFT),
                'agreement_id' => $request->agreement_id,
                'period_from'  => $request->period_from,
                'period_to'    => $request->period_to,
                'prepared_by'  => Auth::id(),
                'status'       => 'draft',
            ]);

            $totalGrossCurrent = 0;
            $cumulativeGrossWork = 0;
            
            // Get previous cumulative gross to calculate correct new cumulative
            $previousIpcsGross = IpcRecord::where('subcon_agreement_id', $request->agreement_id)
                ->where('status', 'approved')
                ->sum('gross_amount');

            foreach ($request->items as $item) {
                $cumQty = $item['previous_qty'] + $item['current_qty'];
                $curAmt = $item['current_qty'] * $item['unit_rate'];
                $totalGrossCurrent += $curAmt;

                $ipc->items()->create([
                    'agreement_item_id' => $item['agreement_item_id'],
                    'previous_qty'      => $item['previous_qty'],
                    'current_qty'       => $item['current_qty'],
                    'cumulative_qty'    => $cumQty,
                    'current_amount'    => $curAmt,
                ]);
            }

            $cumulativeGrossWork = $previousIpcsGross + $totalGrossCurrent;
            $agreement = SubconAgreement::find($request->agreement_id);
            
            $calc = $this->ipcCalculator->calculatePayment($agreement, $cumulativeGrossWork);

            $ipc->update([
                'gross_amount'     => $totalGrossCurrent,
                'retention_amount' => $calc['retention_amount'],
                'net_amount'       => $calc['net_amount_to_certify'],
            ]);
        });

        return redirect()->route('ipcs.index')->with('success', 'IPC Draft created.');
    }

    public function show(IpcRecord $ipc)
    {
        $ipc->load(['agreement.subcontractor', 'items.agreementItem', 'preparedBy']);
        return view('procurement.ipc.show', compact('ipc'));
    }
}
