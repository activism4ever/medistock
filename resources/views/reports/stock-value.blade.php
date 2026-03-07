@extends('layouts.app')
@section('title','Stock Value') @section('page-title','Stock Value Report')
@section('content')
<div class="pt-2">
  <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-6 mb-5 text-white">
    <p class="text-blue-200 text-sm mb-1">Total Stock Value (at cost)</p>
    <p class="text-4xl font-bold">&#x20A6;{{ number_format($totalValue,2) }}</p>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Medicine</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Batch #</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Expiry</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Buy Price</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Remaining</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Stock Value</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @foreach($batches->sortByDesc(fn($b)=>$b->quantity_remaining*$b->purchase_price) as $b)
        <tr class="hover:bg-gray-50/50">
          <td class="px-5 py-3 font-medium text-gray-800">{{ $b->medicine->name }}</td>
          <td class="px-5 py-3 font-mono text-gray-500">{{ $b->batch_number }}</td>
          <td class="px-5 py-3 text-sm {{ $b->isExpiringSoon()?'text-amber-600':'' }}">{{ $b->expiry_date->format('d M Y') }}</td>
          <td class="px-5 py-3 text-right">&#x20A6;{{ number_format($b->purchase_price,2) }}</td>
          <td class="px-5 py-3 text-right font-medium">{{ number_format($b->quantity_remaining) }}</td>
          <td class="px-5 py-3 text-right font-bold text-blue-700">&#x20A6;{{ number_format($b->quantity_remaining*$b->purchase_price,2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
