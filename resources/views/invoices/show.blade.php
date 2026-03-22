@extends('layouts.app')
@section('title','Invoice') @section('page-title','Invoice Details')
@section('content')
<div class="pt-2 max-w-2xl">

  @if(session('success'))
  <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
  @endif

  <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-5">

    {{-- Header --}}
    <div class="flex justify-between items-start mb-5">
      <div>
        <p class="text-xs text-gray-400 mb-1">Invoice Number</p>
        <p class="text-xl font-bold font-mono text-blue-600">{{ $invoice->invoice_number }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $invoice->created_at->format('d M Y H:i') }}</p>
      </div>
      @php
        $colors = ['pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700','dispensed'=>'bg-blue-100 text-blue-700','cancelled'=>'bg-red-100 text-red-600'];
        $labels = ['pending'=>'⏳ Pending','paid'=>'✅ Paid — Ready to Dispense','dispensed'=>'✔ Dispensed','cancelled'=>'✕ Cancelled'];
      @endphp
      <span class="text-sm px-4 py-1.5 rounded-full font-semibold {{ $colors[$invoice->status] }}">
        {{ $labels[$invoice->status] }}
      </span>
    </div>

    {{-- Patient & Cashier Info --}}
    <div class="grid grid-cols-2 gap-4 mb-5 p-4 bg-gray-50 rounded-xl text-sm">
      <div>
        <p class="text-xs text-gray-400 mb-0.5">Patient Name</p>
        <p class="font-semibold text-gray-800">{{ $invoice->patient_name }}</p>
      </div>
      @if($invoice->patient_id)
      <div>
        <p class="text-xs text-gray-400 mb-0.5">Patient ID</p>
        <p class="font-semibold text-gray-800">{{ $invoice->patient_id }}</p>
      </div>
      @endif
      <div>
        <p class="text-xs text-gray-400 mb-0.5">Cashier</p>
        <p class="font-semibold text-gray-800">{{ $invoice->cashier?->name ?? '—' }}</p>
      </div>
      <div>
        <p class="text-xs text-gray-400 mb-0.5">Drawer</p>
        <p class="font-semibold text-gray-800">
          @if($invoice->drawer_number)
            🗄 Drawer {{ $invoice->drawer_number }}
          @else — @endif
        </p>
      </div>
      @if($invoice->paid_at)
      <div>
        <p class="text-xs text-gray-400 mb-0.5">Paid At</p>
        <p class="font-semibold text-green-700">{{ $invoice->paid_at->format('d M Y H:i') }}</p>
      </div>
      @endif
      @if($invoice->dispensed_at)
      <div>
        <p class="text-xs text-gray-400 mb-0.5">Dispensed At</p>
        <p class="font-semibold text-blue-700">{{ $invoice->dispensed_at->format('d M Y H:i') }}</p>
      </div>
      @endif
    </div>

    {{-- Insurance Details --}}
    @if($invoice->isInsurance())
    <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl text-sm">
      <p class="text-xs font-bold text-green-700 uppercase tracking-wide mb-2">Insurance Details</p>
      <div class="grid grid-cols-3 gap-3">
        <div><p class="text-xs text-gray-400">Scheme</p><p class="font-bold text-green-700">{{ $invoice->insuranceScheme?->name }}</p></div>
        <div><p class="text-xs text-gray-400">Enrolee Name</p><p class="font-semibold">{{ $invoice->enrolee_name }}</p></div>
        <div><p class="text-xs text-gray-400">Enrolee ID</p><p class="font-mono">{{ $invoice->enrolee_id }}</p></div>
      </div>
    </div>
    @endif

    {{-- Items Table --}}
    <table class="w-full text-sm mb-5">
      <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Medicine</th>
          <th class="text-center px-4 py-2.5 font-semibold text-gray-600">Qty</th>
          <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Unit Price</th>
          <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Subtotal</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach($invoice->items as $item)
        <tr>
          <td class="px-4 py-2.5">{{ $item->batch->medicine->name }}</td>
          <td class="px-4 py-2.5 text-center">{{ $item->quantity }}</td>
          <td class="px-4 py-2.5 text-right">&#x20A6;{{ number_format($item->selling_price,2) }}</td>
          <td class="px-4 py-2.5 text-right font-semibold">&#x20A6;{{ number_format($item->subtotal,2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Totals --}}
    <div class="border-t border-gray-200 pt-4 space-y-2 text-sm">
      @if($invoice->isInsurance())
      <div class="flex justify-between text-gray-600"><span>Total Drug Cost</span><span>&#x20A6;{{ number_format($invoice->total_amount,2) }}</span></div>
      <div class="flex justify-between text-green-600"><span>Insurance Covers (90%)</span><span>&#x20A6;{{ number_format($invoice->insurance_amount,2) }}</span></div>
      <div class="flex justify-between font-bold text-red-600 text-base border-t pt-2"><span>Patient Pays (10%)</span><span>&#x20A6;{{ number_format($invoice->copayment_amount,2) }}</span></div>
      @else
      <div class="flex justify-between font-bold text-gray-800 text-base"><span>TOTAL</span><span>&#x20A6;{{ number_format($invoice->total_amount,2) }}</span></div>
      @endif
    </div>

    {{-- Actions --}}
    <div class="mt-5 flex gap-3">
      @if($invoice->isPaid())
      <form action="{{ route('invoices.dispense', $invoice) }}" method="POST"
        onsubmit="return confirm('Confirm dispensing drugs for {{ $invoice->patient_name }}?')">
        @csrf @method('PATCH')
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">
          ✔ Mark as Dispensed
        </button>
      </form>
      @endif
      <a href="{{ route('invoices.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-medium">
        Back
      </a>
    </div>
  </div>
</div>
@endsection