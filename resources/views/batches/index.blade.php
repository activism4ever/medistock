@extends('layouts.app')
@section('title','Batches') @section('page-title','Purchase Batches')
@section('content')
<div class="pt-2">
  <div class="flex justify-end mb-5">
    <a href="{{ route('batches.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium">+ Record Purchase</a>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Batch #</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Medicine</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Expiry</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Buy Price</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Sell Price</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Purchased</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Remaining</th>
          <th class="text-center px-5 py-3 font-semibold text-gray-600">Status</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($batches as $b)
        <tr class="hover:bg-gray-50/50 {{ $b->isExpired()?'bg-red-50/30':'' }}">
          <td class="px-5 py-3 font-mono font-medium text-gray-700">{{ $b->batch_number }}</td>
          <td class="px-5 py-3 font-medium text-gray-800">{{ $b->medicine->name }}</td>
          <td class="px-5 py-3 text-sm {{ $b->isExpired()?'text-red-600 font-semibold':($b->isExpiringSoon()?'text-amber-600':'text-gray-500') }}">
            {{ $b->expiry_date->format('d M Y') }}
          </td>
          <td class="px-5 py-3 text-right">&#x20A6;{{ number_format($b->purchase_price,2) }}</td>
          <td class="px-5 py-3 text-right font-semibold text-green-700">&#x20A6;{{ number_format($b->selling_price,2) }}</td>
          <td class="px-5 py-3 text-right text-gray-500">{{ number_format($b->quantity_purchased) }}</td>
          <td class="px-5 py-3 text-right font-semibold {{ $b->quantity_remaining<20?'text-red-600':'' }}">{{ number_format($b->quantity_remaining) }}</td>
          <td class="px-5 py-3 text-center">
            @if($b->isExpired())
              <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full">Expired</span>
            @elseif($b->isExpiringSoon())
              <span class="bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full">Expiring</span>
            @elseif($b->quantity_remaining == 0)
              <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">Depleted</span>
            @else
              <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Active</span>
            @endif
          </td>
          <td class="px-5 py-3 text-right whitespace-nowrap">
            <a href="{{ route('batches.show',$b) }}" class="text-blue-600 hover:underline text-xs mr-3">View</a>
            @if(!$b->isExpired() && $b->quantity_remaining>0)
            <a href="{{ route('allocations.create') }}?batch_id={{ $b->id }}" class="text-gray-500 hover:underline text-xs">Allocate</a>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-5 py-10 text-center text-gray-400">No batches recorded yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">{{ $batches->links() }}</div>
  </div>
</div>
@endsection
