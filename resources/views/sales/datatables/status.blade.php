@if($sale->status === 'paid')
    <span class="badge bg-success bg-opacity-10 text-success border border-success">Paid</span>
@else
    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">Void</span>
@endif
