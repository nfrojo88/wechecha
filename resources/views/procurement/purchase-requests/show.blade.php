@extends('layouts.app')
@section('title', 'PR: ' . $purchaseRequest->pr_no)

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice text-primary me-2"></i>{{ $purchaseRequest->pr_no }}</h1>
            <p class="text-muted small mb-0">Project: <strong>{{ $purchaseRequest->project?->name ?? 'N/A' }}</strong> | Channel: <strong>{{ $purchaseRequest->materialRequest?->source ?? 'Direct PR' }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('procurement.my-queue') }}" class="btn btn-outline-primary"><i class="fas fa-tasks me-1"></i>My Queue</a>
            <a href="{{ route('purchase-requests.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back List</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        <!-- Left Panel: Summary & Details -->
        <div class="col-lg-4">
            <!-- PR Overview Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold py-3 border-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>Lifecycle Summary
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-borderless mb-0 align-middle">
                        <tr><th width="40%" class="ps-3 text-muted">Status</th><td><span class="badge bg-{{ \App\Models\PurchaseRequest::statusBadgeClass($purchaseRequest->status) }}">{{ $purchaseRequest->status_label }}</span></td></tr>
                        <tr><th class="ps-3 text-muted">Current Role Owner</th><td><span class="badge bg-secondary bg-opacity-10 text-dark"><i class="fas fa-user-tag me-1"></i>{{ ucfirst(str_replace('_', ' ', $purchaseRequest->current_owner_role ?? 'Completed')) }}</span></td></tr>
                        <tr><th class="ps-3 text-muted">Priority</th><td><span class="badge bg-{{ $purchaseRequest->priority === 'urgent' ? 'danger' : ($purchaseRequest->priority === 'high' ? 'warning' : 'secondary') }}">{{ ucfirst($purchaseRequest->priority) }}</span></td></tr>
                        <tr><th class="ps-3 text-muted">Requested By</th><td>{{ $purchaseRequest->requestedBy?->name ?? 'N/A' }}</td></tr>
                        <tr><th class="ps-3 text-muted">Required Date</th><td>{{ optional($purchaseRequest->required_date)->format('M d, Y') ?? '-' }}</td></tr>
                        <tr><th class="ps-3 text-muted">Justification</th><td>{{ $purchaseRequest->justification ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>

            <!-- Lifecycle Action Box (Interactive per Stage) -->
            <div class="card border-primary shadow-sm mb-4">
                <div class="card-header bg-primary text-white font-weight-bold py-3">
                    <i class="fas fa-cogs me-2"></i>Stage Action Controls
                </div>
                <div class="card-body">
                    <!-- STAGE 1 / DRAFT: Submit to Store Manager -->
                    @if($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_DRAFT)
                        <form action="{{ route('purchase-requests.submit', $purchaseRequest) }}" method="POST">
                            @csrf
                            <p class="small text-muted mb-2">Submit draft to Store Manager for stock check.</p>
                            <button class="btn btn-primary btn-sm w-100"><i class="fas fa-paper-plane me-1"></i> Submit to Store Manager</button>
                        </form>

                    <!-- STAGE 2: Store Manager Review (Transfer vs Send to PR) -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_STORE_REVIEW)
                        <div class="d-grid gap-2">
                            <form action="{{ route('purchase-requests.send-to-pm', $purchaseRequest) }}" method="POST">
                                @csrf
                                <button class="btn btn-success btn-sm w-100 mb-2"><i class="fas fa-share me-1"></i> Send to Procurement Manager</button>
                            </form>
                            <a href="{{ route('transfers.create') }}" class="btn btn-outline-primary btn-sm w-100"><i class="fas fa-exchange-alt me-1"></i> Initiate Store Transfer</a>
                        </div>

                    <!-- STAGE 3: Procurement Manager Triage -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_PROC_MANAGER)
                        <div class="d-grid gap-2">
                            <form action="{{ route('purchase-requests.send-to-proc-team', $purchaseRequest) }}" method="POST">
                                @csrf
                                <button class="btn btn-primary btn-sm w-100 mb-2"><i class="fas fa-user-check me-1"></i> Send to Procurement Team for Sourcing</button>
                            </form>
                            <button class="btn btn-outline-danger btn-sm w-100" data-bs-toggle="collapse" data-bs-target="#sendBackStoreForm">
                                <i class="fas fa-undo me-1"></i> Send Back to Store Manager
                            </button>
                            <div class="collapse mt-2" id="sendBackStoreForm">
                                <form action="{{ route('purchase-requests.send-back-to-store', $purchaseRequest) }}" method="POST">
                                    @csrf
                                    <textarea name="reason" class="form-control form-control-sm mb-2" placeholder="Reason for sending back..." required></textarea>
                                    <button class="btn btn-danger btn-sm w-100">Confirm Send Back</button>
                                </form>
                            </div>
                        </div>

                    <!-- STAGE 4: Procurement Team Sourcing (Direct Buy vs Proforma) -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_PROC_TEAM)
                        <h6 class="font-weight-bold">Select Sourcing Path:</h6>
                        <ul class="nav nav-pills nav-justified mb-3" id="sourcingTab" role="tablist">
                            <li class="nav-item"><button class="nav-link active btn-sm" data-bs-toggle="tab" data-bs-target="#tabDirect">Direct Buy</button></li>
                            <li class="nav-item"><button class="nav-link btn-sm" data-bs-toggle="tab" data-bs-target="#tabProforma">Proforma</button></li>
                        </ul>
                        <div class="tab-content" id="sourcingTabContent">
                            <div class="tab-pane fade show active" id="tabDirect">
                                <form action="{{ route('purchase-requests.submit-direct-buy', $purchaseRequest) }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label small">Direct Purchase Amount (ETB)</label>
                                        <input type="number" step="0.01" name="amount" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Notes</label>
                                        <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
                                    </div>
                                    <button class="btn btn-success btn-sm w-100">Submit Direct Buy</button>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="tabProforma">
                                <form action="{{ route('purchase-requests.submit-proformas', $purchaseRequest) }}" method="POST">
                                    @csrf
                                    <p class="small text-muted mb-2">Upload quotes in the Proforma Invoices tab on the right, then click submit.</p>
                                    <button class="btn btn-primary btn-sm w-100">Submit Proformas to PM</button>
                                </form>
                            </div>
                        </div>

                    <!-- STAGE 5a: Marketing Review -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_MARKETING)
                        <form action="{{ route('purchase-requests.add-marketing-variance', $purchaseRequest) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small">Direct Amount Submitted</label>
                                <input type="text" class="form-control form-control-sm bg-light" value="{{ number_format($purchaseRequest->direct_buy_amount, 2) }} ETB" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Current Market Price Benchmark (ETB)</label>
                                <input type="number" step="0.01" name="market_price" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Variance Notes</label>
                                <textarea name="variance_notes" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <button class="btn btn-primary btn-sm w-100">Record Variance & Send to GM</button>
                        </form>

                    <!-- STAGE 5b: PM Proforma Selection -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_PROFORMA_SELECTION)
                        <form action="{{ route('purchase-requests.select-proformas', $purchaseRequest) }}" method="POST">
                            @csrf
                            <p class="small text-muted mb-2">Select proformas from the table on the right to forward to GM.</p>
                            <button class="btn btn-primary btn-sm w-100">Send Selected Proformas to GM</button>
                        </form>

                    <!-- STAGE 6: GM Decision -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_GM)
                        <form action="{{ route('purchase-requests.gm-decide', $purchaseRequest) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small font-weight-bold">Decision</label>
                                <select name="decision" class="form-select form-select-sm" id="gmDecisionSelect" required>
                                    <option value="">-- Choose Decision --</option>
                                    <option value="approve">Approve</option>
                                    <option value="reject">Reject</option>
                                    <option value="send_back">Send Back to PM</option>
                                </select>
                            </div>
                            <div class="mb-2 d-none" id="paymentMethodDiv">
                                <label class="form-label small font-weight-bold">Payment Route</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="pay_and_buy">Pay & Buy (Cash/Bank)</option>
                                    <option value="buy_by_credit">Buy by Credit</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Notes / Reason</label>
                                <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <button class="btn btn-danger btn-sm w-100">Submit GM Decision</button>
                        </form>

                    <!-- STAGE 7a: Finance Credit Authorization -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_FINANCE)
                        <form action="{{ route('purchase-requests.finance-credit-approve', $purchaseRequest) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small font-weight-bold">Select Chart of Account (COA)</label>
                                <select name="coa_account_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select COA --</option>
                                    @foreach($coaAccounts as $coa)
                                        <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }} (Bal: {{ number_format($coa->current_balance, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small font-weight-bold">Credit Amount</label>
                                <input type="number" step="0.01" name="amount" class="form-control form-control-sm" value="{{ $purchaseRequest->direct_buy_amount }}" required>
                            </div>
                            <button class="btn btn-info text-white btn-sm w-100">Authorize Credit Line</button>
                        </form>

                    <!-- STAGE 7b: Finance Head Payment Assignment -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_PAYMENT && !$purchaseRequest->payment?->assigned_finance_staff_id)
                        <form action="{{ route('purchase-requests.assign-payment', $purchaseRequest) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small font-weight-bold">Select Funding Account (COA)</label>
                                <select name="coa_account_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select COA --</option>
                                    @foreach($coaAccounts as $coa)
                                        <option value="{{ $coa->id }}">{{ $coa->code }} - {{ $coa->name }} (Bal: {{ number_format($coa->current_balance, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small font-weight-bold">Payment Amount</label>
                                <input type="number" step="0.01" name="amount" class="form-control form-control-sm" value="{{ $purchaseRequest->direct_buy_amount }}" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small font-weight-bold">Assign Finance Staff</label>
                                <select name="staff_user_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Staff Member --</option>
                                    @foreach($financeStaff as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-primary btn-sm w-100">Assign Payment Task</button>
                        </form>

                    <!-- STAGE 7b: Finance Staff Execute Payment -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_PAYMENT && $purchaseRequest->payment?->assigned_finance_staff_id)
                        <form action="{{ route('purchase-requests.execute-payment', $purchaseRequest) }}" method="POST">
                            @csrf
                            <div class="alert alert-info py-2 small mb-2">
                                Amount: <strong>{{ number_format($purchaseRequest->payment->amount, 2) }} ETB</strong><br>
                                Account: <strong>{{ $purchaseRequest->payment->coaAccount?->name }}</strong>
                            </div>
                            <button class="btn btn-success btn-sm w-100"><i class="fas fa-check-double me-1"></i> Confirm & Execute Payment</button>
                        </form>

                    <!-- STAGE 8: Upload Receipt -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_RECEIPT_UPLOAD)
                        <form action="{{ route('purchase-requests.upload-receipt', $purchaseRequest) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small font-weight-bold">Upload Purchase Receipt (PDF/Image)</label>
                                <input type="file" name="receipt_file" class="form-control form-control-sm" required>
                            </div>
                            <button class="btn btn-primary btn-sm w-100"><i class="fas fa-upload me-1"></i> Upload Receipt</button>
                        </form>

                    <!-- STAGE 8: Verify Receipt -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_RECEIPT_VERIFY)
                        <form action="{{ route('purchase-requests.verify-receipt', $purchaseRequest) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small font-weight-bold">Verification Decision</label>
                                <select name="verification_status" class="form-select form-select-sm" required>
                                    <option value="verified">Verify & Approve Receipt</option>
                                    <option value="rejected">Reject Receipt</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Notes</label>
                                <textarea name="verification_notes" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <button class="btn btn-success btn-sm w-100">Submit Verification</button>
                        </form>

                    <!-- STAGE 9: Book Driver (General Service) -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_DRIVER)
                        <form action="{{ route('purchase-requests.book-driver', $purchaseRequest) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label class="form-label small font-weight-bold">Select Driver (HR Employee Master)</label>
                                <select name="driver_employee_id" class="form-select form-select-sm" required>
                                    <option value="">-- Select Driver --</option>
                                    @foreach($drivers as $d)
                                        <option value="{{ $d->id }}">{{ $d->full_name }} ({{ $d->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Vehicle Plate Number</label>
                                <input type="text" name="vehicle_number" class="form-control form-control-sm">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Scheduled Delivery Time</label>
                                <input type="datetime-local" name="scheduled_at" class="form-control form-control-sm">
                            </div>
                            <button class="btn btn-info text-white btn-sm w-100"><i class="fas fa-truck me-1"></i> Book Driver</button>
                        </form>

                    <!-- STAGE 9 Final: Store Manager Final Intake -->
                    @elseif($purchaseRequest->status === \App\Models\PurchaseRequest::STATUS_PENDING_STORE_REVIEW && $purchaseRequest->driverBooking)
                        <form action="{{ route('purchase-requests.store-intake', $purchaseRequest) }}" method="POST">
                            @csrf
                            <p class="small text-muted mb-2">Driver <strong>{{ $purchaseRequest->driverBooking->driver?->full_name }}</strong> has arrived. Perform final intake.</p>
                            <button class="btn btn-success btn-sm w-100"><i class="fas fa-box-open me-1"></i> Complete Final Intake</button>
                        </form>

                    @else
                        <div class="alert alert-secondary py-2 small mb-0">No immediate control action required at this stage.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Panel: Items, Stock, Proformas, Audit Trail -->
        <div class="col-lg-8">
            <!-- Items Table -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold py-3 border-0">Requested Items</div>
                <div class="card-body p-0">
                    <table class="table align-middle mb-0">
                        <thead class="table-light"><tr><th>Product</th><th>Qty Requested</th><th>Unit</th><th>Est. Unit Cost</th><th>Est. Total</th></tr></thead>
                        <tbody>
                            @foreach($purchaseRequest->items as $item)
                            <tr>
                                <td><strong>{{ $item->product?->name ?? 'Item #' . $item->product_id }}</strong></td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>{{ number_format($item->estimated_unit_cost ?? 0, 2) }}</td>
                                <td>{{ number_format($item->estimated_total ?? 0, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Uploaded Purchase Receipt (Visible to Requester & All Hierarchy Roles) -->
            @if($purchaseRequest->receipt)
                @php
                    $receiptUrl = \App\Services\FileUploadService::url($purchaseRequest->receipt->file_path);
                    $ext = strtolower(pathinfo($purchaseRequest->receipt->file_path, PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                    $isPdf = $ext === 'pdf';
                    $vStatus = $purchaseRequest->receipt->verification_status ?? 'pending';
                    $vBadge = match($vStatus) {
                        'verified' => ['class' => 'bg-success', 'label' => 'Verified & Approved by Finance', 'icon' => 'fa-circle-check'],
                        'rejected' => ['class' => 'bg-danger', 'label' => 'Receipt Rejected', 'icon' => 'fa-circle-xmark'],
                        default    => ['class' => 'bg-warning text-dark', 'label' => 'Pending Finance Verification', 'icon' => 'fa-clock'],
                    };
                @endphp
                <div class="card border-0 shadow-sm mb-4 border-start border-4 border-success">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h6 class="font-weight-bold text-dark mb-0">
                                <i class="fas fa-receipt text-success me-2"></i>Official Purchase Receipt / Payment Proof
                            </h6>
                            <small class="text-muted">
                                Uploaded by <strong>{{ $purchaseRequest->receipt->uploadedBy->name ?? 'Procurement Officer' }}</strong>
                                on {{ $purchaseRequest->receipt->created_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ $vBadge['class'] }} px-2 py-1">
                                <i class="fas {{ $vBadge['icon'] }} me-1"></i>{{ $vBadge['label'] }}
                            </span>
                            @if($receiptUrl)
                                <a href="{{ $receiptUrl }}" class="btn btn-sm btn-outline-primary shadow-sm" target="_blank" download>
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                                <a href="{{ $receiptUrl }}" class="btn btn-sm btn-primary shadow-sm" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i> Full View
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-3">
                        @if($purchaseRequest->receipt->verification_notes)
                            <div class="alert alert-light border small py-2 px-3 mb-3">
                                <strong>Verification Notes:</strong> {{ $purchaseRequest->receipt->verification_notes }}
                                @if($purchaseRequest->receipt->verifiedBy)
                                    <span class="text-muted">(by {{ $purchaseRequest->receipt->verifiedBy->name }} on {{ $purchaseRequest->receipt->verified_at?->format('M d, Y H:i') }})</span>
                                @endif
                            </div>
                        @endif

                        {{-- Inline Receipt Preview --}}
                        @if($receiptUrl)
                            @if($isImage)
                                <div class="text-center p-2 bg-light rounded border">
                                    <a href="{{ $receiptUrl }}" target="_blank" title="Click to view full image">
                                        <img src="{{ $receiptUrl }}" alt="Purchase Receipt" class="img-fluid rounded shadow-sm" style="max-height: 420px; object-fit: contain; cursor: zoom-in;">
                                    </a>
                                    <div class="text-muted small mt-2"><i class="fas fa-search-plus me-1"></i> Click image to open high-resolution view</div>
                                </div>
                            @elseif($isPdf)
                                <div class="border rounded bg-light overflow-hidden" style="height: 480px;">
                                    <iframe src="{{ $receiptUrl }}" width="100%" height="100%" style="border: none;">
                                        <div class="p-3 text-center text-muted">
                                            PDF preview not supported in browser. <a href="{{ $receiptUrl }}" target="_blank" class="btn btn-sm btn-primary mt-2">Download PDF</a>
                                        </div>
                                    </iframe>
                                </div>
                            @else
                                <div class="p-3 bg-light rounded border text-center">
                                    <i class="fas fa-file-alt fa-3x text-secondary mb-2"></i>
                                    <div class="fw-bold">{{ basename($purchaseRequest->receipt->file_path) }}</div>
                                    <a href="{{ $receiptUrl }}" class="btn btn-sm btn-primary mt-2" target="_blank">View / Download Document</a>
                                </div>
                            @endif
                        @else
                            <div class="text-muted small italic p-2 bg-light rounded">Receipt file path recorded, but media link is currently unavailable.</div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Cross-Store Stock Availability View -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold py-3 border-0">
                    <i class="fas fa-warehouse text-warning me-2"></i>Real-time Cross-Store Inventory View
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light"><tr><th>Product</th><th>Store Name</th><th>Qty Available</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach($purchaseRequest->items as $item)
                                @php $stocks = $stockAvailability[$item->product_id] ?? collect(); @endphp
                                @forelse($stocks as $st)
                                <tr>
                                    <td>{{ $item->product?->name ?? 'Item #' . $item->product_id }}</td>
                                    <td>{{ $st->store?->name ?? 'N/A' }}</td>
                                    <td><strong class="text-success">{{ $st->quantity_on_hand }}</strong> {{ $item->unit }}</td>
                                    <td><span class="badge bg-success">In Stock</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td>{{ $item->product?->name ?? 'Item #' . $item->product_id }}</td>
                                    <td colspan="3" class="text-muted italic">No stock available across any stores.</td>
                                </tr>
                                @endforelse
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Audit Trail / Workflow History Logs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white font-weight-bold py-3 border-0">
                    <i class="fas fa-history text-info me-2"></i>Workflow Audit Trail (Full Hand-off Log)
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($purchaseRequest->workflowLogs as $log)
                        <div class="list-group-item p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                                <small class="text-muted">{{ $log->created_at->format('M d, Y H:i:s') }}</small>
                            </div>
                            <div class="small">
                                <strong>{{ $log->actor?->name }}</strong> ({{ ucfirst(str_replace('_', ' ', $log->actor_role)) }}) moved stage to 
                                <span class="badge bg-info text-dark">{{ \App\Models\PurchaseRequest::statusLabels()[$log->to_stage] ?? $log->to_stage }}</span>
                            </div>
                            @if($log->notes)
                            <div class="small text-muted mt-1 bg-light p-2 rounded">"{{ $log->notes }}"</div>
                            @endif
                        </div>
                        @empty
                        <div class="p-3 text-muted text-center small">No workflow logs recorded yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gmSelect = document.getElementById('gmDecisionSelect');
    const payDiv   = document.getElementById('paymentMethodDiv');
    if (gmSelect && payDiv) {
        gmSelect.addEventListener('change', function() {
            if (this.value === 'approve') {
                payDiv.classList.remove('d-none');
            } else {
                payDiv.classList.add('d-none');
            }
        });
    }
});
</script>
@endsection
