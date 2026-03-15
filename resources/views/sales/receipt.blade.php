<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Receipt {{ $sale->receipt_number }}</title>
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
      <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 mb-2">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
        </svg>
      </div>
      <p class="font-bold text-lg leading-tight">MEDISTOCK POS</p>
      <p class="text-gray-500 text-xs">Hospital Medicine System</p>
      <p class="text-gray-500 text-xs">{{ $sale->department->name }}</p>
    </div>

    {{-- Info --}}
    <div class="text-xs space-y-1 mb-4 border-b border-dashed border-gray-300 pb-4">
      <div class="flex justify-between"><span class="text-gray-500">Receipt #</span><span class="font-bold">{{ $sale->receipt_number }}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Date</span><span>{{ $sale->created_at->format('d/m/Y H:i') }}</span></div>
      <div class="flex justify-between"><span class="text-gray-500">Patient</span><span class="font-semibold">{{ $sale->patient_name }}</span></div>
      @if($sale->patient_id)<div class="flex justify-between"><span class="text-gray-500">Patient ID</span><span>{{ $sale->patient_id }}</span></div>@endif
      <div class="flex justify-between"><span class="text-gray-500">Dispensed by</span><span>{{ $sale->soldBy->name }}</span></div>
@if($sale->drawer_number)
<div class="flex justify-between"><span class="text-gray-500">Drawer</span><span class="font-semibold text-yellow-600">🗄 Drawer {{ $sale->drawer_number }}</span></div>
@endif
    </div>

    {{-- Items --}}
    <table class="w-full text-xs mb-4">
      <thead><tr class="border-b border-gray-200">
        <th class="text-left py-1.5">Item</th>
        <th class="text-center py-1.5">Qty</th>
        <th class="text-right py-1.5">Price</th>
        <th class="text-right py-1.5">Total</th>
      </tr></thead>
      <tbody>
        @foreach($sale->items as $item)
        <tr class="border-b border-dashed border-gray-100">
          <td class="py-1.5 pr-2 leading-tight">{{ $item->batch->medicine->name }}</td>
          <td class="py-1.5 text-center">{{ $item->quantity }}</td>
          <td class="py-1.5 text-right">{{ number_format($item->selling_price,2) }}</td>
          <td class="py-1.5 text-right font-semibold">{{ number_format($item->selling_price*$item->quantity,2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="border-t-2 border-gray-900 pt-2 mb-4">
      <div class="flex justify-between font-bold text-base">
        <span>TOTAL</span>
        <span>&#x20A6;{{ number_format($sale->total_amount,2) }}</span>
      </div>
    </div>

    <div class="text-center text-xs text-gray-400 border-t border-dashed border-gray-300 pt-4">
      <p class="font-medium">Thank you for your visit.</p>
      <p>Please take medications as prescribed.</p>
      <p class="mt-2">{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
  </div>

  <div class="no-print fixed bottom-6 right-6 flex gap-3">
    <button onclick="window.print()"
      class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-xl shadow-blue-500/20">
      &#x1F5A8; Print
    </button>
    <a href="{{ route('sales.index') }}"
      class="bg-white hover:bg-gray-50 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-medium shadow-xl border border-gray-200">
      Back
    </a>
  </div>
</body>
</html>
