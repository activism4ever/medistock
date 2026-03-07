@extends('layouts.app')
@section('title','Sale '.$sale->receipt_number) @section('page-title','Sale: '.$sale->receipt_number)
@section('content')
<div class="pt-2 max-w-3xl">
  <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-5">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 mb-6 text-sm">
      <div><p class="text-xs text-gray-400 mb-0.5">Patient</p><p class="font-semibold text-gray-800">{{ $sale->patient_name }}</p>
        @if($sale->patient_id)<p class="text-xs text-gray-400">ID: {{ $sale->patient_id }}</p>@endif</div>
      <div><p class="text-xs text-gray-400 mb-0.5">Department</p><p class="font-semibold text-gray-800">{{ $sale->department->name }}</p></div>
      <div><p class="text-xs text-gray-400 mb-0.5">Dispensed By</p><p class="font-semibold text-gray-800">{{ $sale->soldBy->name }}</p></div>
      <div><p class="text-xs text-gray-400 mb-0.5">Date &amp; Time</p><p class="font-semibold text-gray-800">{{ $sale->created_at->format('d M Y') }}<br><span class="text-gray-400 text-xs">{{ $sale->created_at->format('H:i:s') }}</span></p></div>
    </div>
    <table class="w-full text-sm border-t border-gray-100">
      <thead class="bg-gray-50">
        <tr>
          <th class="text-left py-3 px-3 font-semibold text-gray-600">Item</th>
          <th class="text-right py-3 px-3 font-semibold text-gray-600">Qty</th>
          <th class="text-right py-3 px-3 font-semibold text-gray-600">Unit Price</th>
          <th class="text-right py-3 px-3 font-semibold text-gray-600">Subtotal</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach($sale->items as $item)
        <tr>
          <td class="py-3 px-3">
            <p class="font-medium text-gray-800">{{ $item->batch->medicine->name }}</p>
            <p class="text-xs text-gray-400">Batch: {{ $item->batch->batch_number }}</p>
          </td>
          <td class="py-3 px-3 text-right">{{ $item->quantity }}</td>
          <td class="py-3 px-3 text-right">&#x20A6;{{ number_format($item->selling_price,2) }}</td>
          <td class="py-3 px-3 text-right font-semibold">&#x20A6;{{ number_format($item->selling_price*$item->quantity,2) }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot class="border-t-2 border-gray-800 bg-gray-50">
        <tr>
          <td colspan="3" class="py-3 px-3 font-bold text-right">TOTAL</td>
          <td class="py-3 px-3 font-bold text-right text-lg">&#x20A6;{{ number_format($sale->total_amount,2) }}</td>
        </tr>
        <tr class="border-t border-gray-200">
          <td colspan="3" class="py-2 px-3 text-right text-gray-400 text-xs">Profit</td>
          <td class="py-2 px-3 text-right text-green-600 text-xs font-semibold">&#x20A6;{{ number_format($sale->total_profit,2) }}</td>
        </tr>
      </tfoot>
    </table>
  </div>
  <div class="flex gap-3">
    <a href="{{ route('sales.receipt',$sale) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">&#x1F5A8; Print Receipt</a>
    <a href="{{ route('sales.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-medium">Back to Sales</a>
  </div>
</div>
@endsection
