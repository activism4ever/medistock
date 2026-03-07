@extends('layouts.app')
@section('title','Low Stock') @section('page-title','Low Stock Report (&lt; 20 Units)')
@section('content')
<div class="pt-2">
  @if($batches->isEmpty())
  <div class="bg-green-50 border border-green-200 rounded-2xl px-6 py-10 text-center">
    <p class="text-green-700 font-semibold text-lg">&#10003; All stock levels are healthy!</p>
    <p class="text-green-500 text-sm mt-1">No batches below the 20-unit threshold.</p>
  </div>
  @else
  <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-5 text-sm text-red-800">
    <strong>{{ $batches->count() }} batch(es)</strong> need restocking or reallocation.
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Medicine</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Batch #</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Expiry</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Units Left</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Sell Price</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @foreach($batches as $b)
        <tr class="bg-red-50/30 hover:bg-red-50/60">
          <td class="px-5 py-3 font-medium text-gray-800">{{ $b->medicine->name }}</td>
          <td class="px-5 py-3 font-mono text-gray-500">{{ $b->batch_number }}</td>
          <td class="px-5 py-3 text-gray-500">{{ $b->expiry_date->format('d M Y') }}</td>
          <td class="px-5 py-3 text-right">
            <span class="inline-block bg-red-100 text-red-800 font-bold text-sm px-3 py-1 rounded-full">{{ $b->quantity_remaining }}</span>
          </td>
          <td class="px-5 py-3 text-right font-medium">&#x20A6;{{ number_format($b->selling_price,2) }}</td>
          <td class="px-5 py-3 text-right whitespace-nowrap">
            <a href="{{ route('batches.show',$b) }}" class="text-blue-600 hover:underline text-xs mr-3">View</a>
            <a href="{{ route('allocations.create') }}?batch_id={{ $b->id }}" class="text-gray-500 hover:underline text-xs">Allocate</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
