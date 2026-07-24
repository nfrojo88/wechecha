@extends('layouts.app')
@section('title', 'New Journal Entry')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="fas fa-book me-2"></i>New Journal Entry</h1>
        <a href="{{ route('journal-entries.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <form action="{{ route('journal-entries.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-9">
                <div class="card shadow-sm mb-3">
                    <div class="card-header fw-semibold">Entry Details</div>
                    <div class="card-body row g-3">
                        <div class="col-md-6"><label class="form-label">Date *</label><input type="date" name="entry_date" class="form-control" value="{{ today()->toDateString() }}" required></div>
                        <div class="col-md-6"><label class="form-label">Reference</label><input type="text" name="reference" class="form-control"></div>
                        <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between fw-semibold">
                        <span>Lines</span>
                        <button type="button" class="btn btn-sm btn-success" id="addLine"><i class="fas fa-plus"></i> Add Line</button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead class="table-light"><tr><th>Account</th><th>Description</th><th>Debit</th><th>Credit</th><th></th></tr></thead>
                            <tbody id="linesBody">
                                @for($i=0; $i<2; $i++)
                                <tr>
                                    <td><select name="lines[{{$i}}][coa_id]" class="form-select" required><option value="">-- Select --</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>@endforeach</select></td>
                                    <td><input type="text" name="lines[{{$i}}][description]" class="form-control"></td>
                                    <td><input type="number" name="lines[{{$i}}][debit]" class="form-control debit" min="0" step="0.01" value="0"></td>
                                    <td><input type="number" name="lines[{{$i}}][credit]" class="form-control credit" min="0" step="0.01" value="0"></td>
                                    <td>@if($i>1)<button type="button" class="btn btn-sm btn-outline-danger rm"><i class="fas fa-times"></i></button>@endif</td>
                                </tr>
                                @endfor
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr><td colspan="2" class="text-end">Total:</td><td id="totDeb">0.00</td><td id="totCred">0.00</td><td></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body"><button type="submit" class="btn btn-primary w-100">Post Entry</button></div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
let lidx=2;
document.getElementById('addLine').onclick=function(){
    document.getElementById('linesBody').insertAdjacentHTML('beforeend', `<tr><td><select name="lines[${lidx}][coa_id]" class="form-select" required><option value="">-- Select --</option>@foreach($accounts as $a)<option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }}</option>@endforeach</select></td><td><input type="text" name="lines[${lidx}][description]" class="form-control"></td><td><input type="number" name="lines[${lidx}][debit]" class="form-control debit" min="0" step="0.01" value="0"></td><td><input type="number" name="lines[${lidx}][credit]" class="form-control credit" min="0" step="0.01" value="0"></td><td><button type="button" class="btn btn-sm btn-outline-danger rm"><i class="fas fa-times"></i></button></td></tr>`);
    lidx++;
};
document.addEventListener('click', e=>{if(e.target.closest('.rm')){e.target.closest('tr').remove(); calcTot();} });
document.addEventListener('input', e=>{if(e.target.classList.contains('debit') || e.target.classList.contains('credit')) calcTot();});
function calcTot(){
    let d=0, c=0;
    document.querySelectorAll('.debit').forEach(el=>d+=parseFloat(el.value||0));
    document.querySelectorAll('.credit').forEach(el=>c+=parseFloat(el.value||0));
    document.getElementById('totDeb').innerText = d.toFixed(2);
    document.getElementById('totCred').innerText = c.toFixed(2);
}
</script>
@endsection
