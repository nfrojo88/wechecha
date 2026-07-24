<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Payment::class);

        $query = Payment::with(['project', 'creator'])->latest('payment_date');

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $payments = $query->paginate(20);
        $projects = Project::all();

        return view('finance.payments.index', compact('payments', 'projects'));
    }

    public function create()
    {
        Gate::authorize('create', Payment::class);
        $projects = Project::where('status', '!=', 'cancelled')->get();
        $types    = Payment::TYPES;
        return view('finance.payments.create', compact('projects', 'types'));
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Payment::class);

        $validated = $request->validate([
            'project_id'       => 'required|exists:projects,id',
            'reference_number' => 'required|string|unique:payments',
            'amount'           => 'required|numeric|min:0.01',
            'payment_date'     => 'required|date',
            'payment_type'     => 'required|in:' . implode(',', array_keys(Payment::TYPES)),
            'description'      => 'nullable|string|max:500',
            'notes'            => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        $payment = Payment::create($validated);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        Gate::authorize('view', $payment);
        $payment->load(['project', 'creator']);
        return view('finance.payments.show', compact('payment'));
    }
}
