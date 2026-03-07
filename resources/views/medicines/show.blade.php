@extends('layouts.app')
@section('title',$medicine->name) @section('page-title',$medicine->name)
@section('content')
<div class="pt-2">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <div class="bg-white rounded-2xl border border-gray-200 p-6">
      <h3 class="font-semibold text-gray-800 mb-4">Medicine Info</h3>
      <dl class="space-y-3 text-sm">
        <div><dt class="text-gray-400 text-xs uppercase">Generic</dt><dd class="font-medium mt-0.5">{{ $medicine->generic_name ?? '—' }}</dd></div>
        <div><dt class="text-gray-400 text-xs uppercase">Dosage</dt><dd class="font-medium mt-0.5">{{ $medicine->dosage ?? '—' }}</dd></div>
        <div><dt class="text-gray-400 text-xs uppercase">Unit</dt><dd class="font-medium mt-0.5">{{ ucfirst($medicine->unit) }}</dd></div>
        <div><dt class="text-gray-400 text-xs uppercase">Category</dt><dd class="mt-0.5">
          @if($medicine->category)<span class="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $medicine->category }}</span>
          @else —@endif
        </dd></div>
        <div><dt class="text-gray-400 text-xs uppercase">Status</dt><dd class="mt-0.5">
          <span class="text-xs px-2.5 py-0.5 rounded-full {{ $medicine->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
            {{ $medicine->is_active ? 'Active' : 'Inactive' }}
          </span>
        </dd></div>
        @if($medicine->description)
        <div><dt class="text-gray-400 text-xs uppercase">Description</dt><dd class="font-medium mt-0.5 text-gray-600">{{ $medicine->description }}</dd></div>
        @endif
      </dl>
      <div class="flex gap-2 mt-5">
        <a href="{{ route('medicines.edit',$medicine) }}" class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg">Edit</a>
        <a href="{{ route('batches.create') }}?medicine_id={{ $medicine->id }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg">+ Add Batch</a>
      </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">Purchase Batches</h3>
        <span class="text-xs text-gray-400">{{ $batches->total() }} batches</span>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
              <th class="text-left px-5 py-3 font-semibold text-gray-600">Batch #</th>
              <th class="text-left px-5 py-3 font-semibold text-gray-600">Expiry</th>
              <th class="text-right px-5 py-3 font-semibold text-gray-600">Buy Price</th>
              <th class="text-right px-5 py-3 font-semibold text-gray-600">Sell Price</th>
              <th class="text-right px-5 py-3 font-semibold text-gray-600">Remaining</th>
              <th class="px-5 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            @forelse($batches as $b)
            <tr class="hover:bg-gray-50/50 {{ $b->isExpired() ? 'opacity-50' : '' }}">
              <td class="px-5 py-3 font-medium">
                {{ $b->batch_number }}
                @if($b->isExpired())<span class="ml-1.5 bg-red-100 text-red-700 text-xs px-1.5 rounded">Expired</span>@endif
                @if($b->isExpiringSoon())<span class="ml-1.5 bg-amber-100 text-amber-700 text-xs px-1.5 rounded">Expiring</span>@endif
              </td>
              <td class="px-5 py-3 text-gray-500 {{ $b->isExpired()?'text-red-600':($b->isExpiringSoon()?'text-amber-600':'') }}">
                {{ $b->expiry_date->format('d M Y') }}
              </td>
              <td class="px-5 py-3 text-right">&#x20A6;{{ number_format($b->purchase_price,2) }}</td>
              <td class="px-5 py-3 text-right font-medium text-green-700">&#x20A6;{{ number_format($b->selling_price,2) }}</td>
              <td class="px-5 py-3 text-right font-semibold {{ $b->quantity_remaining<20?'text-red-600':'' }}">{{ $b->quantity_remaining }}</td>
              <td class="px-5 py-3 text-right">
                <a href="{{ route('batches.show',$b) }}" class="text-blue-600 hover:underline text-xs">View</a>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No batches yet</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="px-5 py-4 border-t border-gray-100">{{ $batches->links() }}</div>
    </div>
  </div>
</div>
@endsection
