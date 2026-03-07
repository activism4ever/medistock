@extends('layouts.app')
@section('title','Expiry Tracker') @section('page-title','Expiry Tracker — Next 90 Days')
@section('content')
<div class="pt-2">
  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Medicine</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Batch #</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Expiry Date</th>
          <th class="text-center px-5 py-3 font-semibold text-gray-600">Days Left</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Remaining</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Value at Cost</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($batches as $b)
        @php $days = (int) now()->diffInDays($b->expiry_date, false); @endphp
        <tr class="hover:bg-gray-50/50 {{ $days<=7?'bg-red-50/50':($days<=30?'bg-amber-50/30':'') }}">
          <td class="px-5 py-3 font-medium text-gray-800">{{ $b->medicine->name }}</td>
          <td class="px-5 py-3 font-mono text-gray-500">{{ $b->batch_number }}</td>
          <td class="px-5 py-3 font-medium {{ $days<=7?'text-red-600':($days<=30?'text-amber-600':'text-gray-600') }}">
            {{ $b->expiry_date->format('d M Y') }}
          </td>
          <td class="px-5 py-3 text-center">
            <span class="inline-block text-xs px-2.5 py-0.5 rounded-full font-semibold
              {{ $days<=7?'bg-red-100 text-red-700':($days<=30?'bg-amber-100 text-amber-700':'bg-blue-100 text-blue-700') }}">
              {{ $days }} days
            </span>
          </td>
          <td class="px-5 py-3 text-right font-medium">{{ number_format($b->quantity_remaining) }}</td>
          <td class="px-5 py-3 text-right text-gray-600">&#x20A6;{{ number_format($b->quantity_remaining*$b->purchase_price,2) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">&#10003; No batches expiring within 90 days.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
