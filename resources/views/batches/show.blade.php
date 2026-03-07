@extends('layouts.app')
@section('title','Batch '.$batch->batch_number) @section('page-title','Batch: '.$batch->batch_number)
@section('content')
<div class="pt-2">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
      <h3 class="font-semibold text-gray-800 mb-5">Batch Details</h3>
      <dl class="space-y-3 text-sm">
        <div><dt class="text-xs text-gray-400 uppercase">Medicine</dt>
          <dd class="font-semibold text-blue-600 mt-0.5">{{ $batch->medicine->name }}</dd></div>
        <div><dt class="text-xs text-gray-400 uppercase">Batch Number</dt>
          <dd class="font-mono font-medium mt-0.5">{{ $batch->batch_number }}</dd></div>
        <div><dt class="text-xs text-gray-400 uppercase">Expiry Date</dt>
          <dd class="font-medium mt-0.5 {{ $batch->isExpired()?'text-red-600':($batch->isExpiringSoon()?'text-amber-600':'') }}">
            {{ $batch->expiry_date->format('d M Y') }}
            @if($batch->isExpired()) <span class="text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded ml-1">EXPIRED</span>@endif
            @if($batch->isExpiringSoon()) <span class="text-xs bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded ml-1">EXPIRING SOON</span>@endif
          </dd></div>
        <div class="bg-gray-50 rounded-xl p-3 grid grid-cols-2 gap-2">
          <div><p class="text-xs text-gray-400">Buy Price</p><p class="font-semibold">&#x20A6;{{ number_format($batch->purchase_price,2) }}</p></div>
          <div><p class="text-xs text-gray-400">Margin</p><p class="font-semibold">{{ $batch->margin_percentage }}%</p></div>
          <div class="col-span-2 border-t border-gray-200 pt-2">
            <p class="text-xs text-gray-400">Selling Price</p>
            <p class="text-lg font-bold text-green-700">&#x20A6;{{ number_format($batch->selling_price,2) }}</p>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div><dt class="text-xs text-gray-400">Purchased</dt><dd class="font-semibold">{{ number_format($batch->quantity_purchased) }}</dd></div>
          <div><dt class="text-xs text-gray-400">Remaining</dt>
            <dd class="font-bold text-lg {{ $batch->quantity_remaining<20?'text-red-600':'text-blue-700' }}">
              {{ number_format($batch->quantity_remaining) }}
            </dd></div>
        </div>
        <div><dt class="text-xs text-gray-400">Receipt / Invoice</dt>
          <dd class="font-medium mt-0.5 text-gray-600">{{ $batch->receipt_no ?? '—' }} / {{ $batch->invoice_no ?? '—' }}</dd></div>
        <div><dt class="text-xs text-gray-400">Created By</dt>
          <dd class="font-medium mt-0.5">{{ $batch->creator->name }}</dd></div>
      </dl>
      <div class="flex gap-2 mt-5">
        <a href="{{ route('batches.edit',$batch) }}" class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg">Edit</a>
        @if(!$batch->isExpired() && $batch->quantity_remaining>0)
        <a href="{{ route('allocations.create') }}?batch_id={{ $batch->id }}"
          class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg">Allocate Stock</a>
        @endif
      </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100 flex justify-between">
        <h3 class="font-semibold text-gray-800">Allocation History</h3>
        <span class="text-xs text-gray-400">{{ $batch->allocations->count() }} records</span>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th class="text-left px-5 py-3 font-semibold text-gray-600">Department</th>
            <th class="text-right px-5 py-3 font-semibold text-gray-600">Qty Allocated</th>
            <th class="text-left px-5 py-3 font-semibold text-gray-600">Allocated By</th>
            <th class="text-left px-5 py-3 font-semibold text-gray-600">Date</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($batch->allocations as $a)
          <tr class="hover:bg-gray-50/50">
            <td class="px-5 py-3">
              <span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full font-medium">{{ $a->department->name }}</span>
            </td>
            <td class="px-5 py-3 text-right font-semibold text-blue-700">{{ number_format($a->quantity_allocated) }}</td>
            <td class="px-5 py-3 text-gray-600">{{ $a->allocatedBy->name }}</td>
            <td class="px-5 py-3 text-gray-400">{{ $a->created_at->format('d M Y H:i') }}</td>
          </tr>
          @empty
          <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No allocations yet</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
