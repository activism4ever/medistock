<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Receipt {{ $invoice->invoice_number }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @media print { .no-print{display:none} body{background:white} }
    body { font-family: 'Courier New', monospace; }
  </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-start justify-center py-8 px-4">
  <div class="bg-white w-72 shadow-2xl rounded-2xl p-6">

    {{-- Header --}}
    <div class="text-center mb-4 border-b border-dashed border-gray-300 pb-4">
      <div class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $invoice->isInsurance() ? 'bg-green-600' : 'bg-blue-600' }} mb-2">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
        </svg>
      </div>
      <p class="font-bold text-lg leading-tight">MEDISTOCK POS</p>
      <p class="text-gray-500 text-xs">Hospital Medicine System</p>
      @if($invoice->isInsurance())
      <div class="mt-2 bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full inline-block">
        🏥 INSURANCE SALE
      </div>
      @endif
    </div>

    {{-- Patient Info --}}
    <div class="text-xs space-y-1 mb-4 border-b border-dashed border-gray-300 pb-4">
      <div class="flex justify-between"><span class="text-gray-500">Receipt #</span><span class="font-bold">{{ $invoice->invoice_number }}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Date</span><span>{{ $invoice->paid_at?->format('d/m/Y H:i') }}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Patient</span><span class="font-semibold">{{ $invoice->patient_name }}</span></div>
      @if($invoice->patient_id)
      <div class="flex justify-between"><span class="text-gray-500">Patient ID</span><span>{{ $invoice->patient_id }}</span></div>
      @endif
      <div class="flex justify-between"><span class="text-gray-500">Cashier</span><span>{{ $invoice->cashier?->name }}</span></div>
      @if($invoice->drawer_number)
      <div class="flex justify-between"><span class="text-gray-500">Drawer</span><span class="text-yellow-600 font-semibold">🗄 Drawer {{ $invoice->drawer_number }}</span></div>
      @endif
    </div>

    {{-- Insurance Details — only ONE block --}}
    @if($invoice->isInsurance())
    <div class="text-xs space-y-1 mb-4 border-b border-dashed border-gray-300 pb-4 bg-green-50 rounded-xl px-3 py-3">
      <p class="font-bold text-green-700 text-xs mb-2 uppercase tracking-wide">Insurance Details</p>
      <div class="flex justify-between"><span class="text-gray-500">Scheme</span><span class="font-bold text-green-700">{{ $invoice->insuranceScheme?->name }}</span></div>
      @if($invoice->sector)
      <div class="flex justify-between">
        <span class="text-gray-500">Program</span>
        <span class="font-bold {{ $invoice->isInformal() ? 'text-purple-600' : 'text-green-700' }}">
          {{ $invoice->isInformal() ? 'Informal Sector' : 'Formal Sector' }}
        </span>
      </div>
      @endif
      <div class="flex justify-between"><span class="text-gray-500">Enrolee Name</span><span class="font-semibold">{{ $invoice->enrolee_name }}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Enrolee ID</span><span class="font-mono">{{ $invoice->enrolee_id }}</span></div>
    </div>
    @endif

    {{-- Items --}}
    <table class="w-full text-xs mb-4">
      <thead><tr class="border-b border-gray-200">
        <th class="text-left py-1.5">Item</th>
        <th class="text-center py-1.5">Qty</th>
        <th class="text-right py-1.5">Price</th>
        <th class="text-right py-1.5">Total</th>
      </tr></thead>
      <tbody>
        @foreach($invoice->items as $item)
        <tr class="border-b border-dashed border-gray-100">
          <td class="py-1.5 pr-2 leading-tight">{{ $item->batch->medicine->name }}</td>
          <td class="py-1.5 text-center">{{ $item->quantity }}</td>
          <td class="py-1.5 text-right">{{ number_format($item->selling_price,2) }}</td>
          <td class="py-1.5 text-right font-semibold">{{ number_format($item->subtotal,2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Totals --}}
    @if($invoice->isInsurance())
    <div class="border-t border-gray-200 pt-2 mb-4 space-y-1.5 text-xs">
      <div class="flex justify-between text-gray-600">
        <span>Total Drug Cost</span>
        <span>&#x20A6;{{ number_format($invoice->total_amount,2) }}</span>
      </div>
      @if($invoice->isInformal())
      <div class="flex justify-between text-purple-600">
        <span>{{ $invoice->insuranceScheme?->name }} Covers (100%)</span>
        <span>&#x20A6;{{ number_format($invoice->total_amount,2) }}</span>
      </div>
      <div class="flex justify-between text-blue-600 font-bold border-t-2 border-gray-900 pt-2 text-sm">
        <span>PATIENT PAID (0%)</span>
        <span>&#x20A6;0.00</span>
      </div>
      @else
      <div class="flex justify-between text-green-600">
        <span>{{ $invoice->insuranceScheme?->name }} Covers (90%)</span>
        <span>&#x20A6;{{ number_format($invoice->insurance_amount,2) }}</span>
      </div>
      <div class="flex justify-between text-red-600 font-bold border-t-2 border-gray-900 pt-2 text-sm">
        <span>PATIENT PAID (10%)</span>
        <span>&#x20A6;{{ number_format($invoice->copayment_amount,2) }}</span>
      </div>
      @endif
    </div>
    @else
    <div class="border-t-2 border-gray-900 pt-2 mb-4">
      <div class="flex justify-between font-bold text-base">
        <span>TOTAL PAID</span>
        <span>&#x20A6;{{ number_format($invoice->total_amount,2) }}</span>
      </div>
    </div>
    @endif

    <div class="text-center text-xs text-gray-400 border-t border-dashed border-gray-300 pt-4">
      <p class="font-medium">Payment received. Please collect</p>
      <p>your drugs from the pharmacy.</p>
      <p class="mt-2">{{ $invoice->paid_at?->format('d/m/Y H:i:s') }}</p>
    </div>
  </div>

  <div class="no-print fixed bottom-6 right-6 flex gap-3">
    <button onclick="window.print()"
      class="{{ $invoice->isInsurance() ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-xl">
      🖨 Print
    </button>
    <a href="{{ route('cashier.dashboard') }}"
      class="bg-white hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-medium shadow-xl border border-gray-200">
      Back
    </a>
  </div>
</body>
</html>