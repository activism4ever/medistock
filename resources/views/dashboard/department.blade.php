@extends('layouts.app')
@section('title', auth()->user()->department->name . ' Dashboard')
@section('page-title', auth()->user()->department->name . ' Dashboard')
@section('subtitle', auth()->user()->role_label)

@section('content')
<div class="pt-2">
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-500 mb-1">Assigned Batches</p>
      <p class="text-3xl font-bold text-gray-800">{{ $stats['assignedStock'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-500 mb-1">Today's Sales</p>
      <p class="text-3xl font-bold text-green-600">&#x20A6;{{ number_format($stats['todaySales'],2) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-500 mb-1">Total Units Remaining</p>
      <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['totalUnits']) }}</p>
    </div>
    <div class="bg-{{ $stats['lowStockItems']>0?'red':'green' }}-50 rounded-2xl border border-{{ $stats['lowStockItems']>0?'red':'green' }}-200 p-5">
      <p class="text-xs text-{{ $stats['lowStockItems']>0?'red':'green' }}-600 mb-1">Low Stock Items</p>
      <p class="text-3xl font-bold text-{{ $stats['lowStockItems']>0?'red':'green' }}-700">{{ $stats['lowStockItems'] }}</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">Recent Transactions</h3>
        <a href="{{ route('sales.index') }}" class="text-xs text-blue-600 hover:underline">View all &rarr;</a>
      </div>
      <div class="divide-y divide-gray-50">
        @forelse($recentSales as $sale)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
          <div>
            <p class="text-sm font-medium text-gray-800">{{ $sale->patient_name }}</p>
            <p class="text-xs text-gray-400">{{ $sale->receipt_number }} &middot; {{ $sale->created_at->diffForHumans() }}</p>
          </div>
          <div class="text-right">
            <p class="text-sm font-semibold">&#x20A6;{{ number_format($sale->total_amount,2) }}</p>
            <a href="{{ route('sales.show',$sale) }}" class="text-xs text-blue-600">View</a>
          </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-sm text-gray-400">No sales yet</div>
        @endforelse
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">Low Stock Items</h3>
      </div>
      <div class="divide-y divide-gray-50">
        @forelse($lowStockItems as $item)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
          <div>
            <p class="text-sm font-medium text-gray-800">{{ $item->batch->medicine->name }}</p>
            <p class="text-xs text-gray-400">Batch: {{ $item->batch->batch_number }}</p>
          </div>
          <span class="bg-red-100 text-red-800 text-xs px-2.5 py-0.5 rounded-full font-semibold">{{ $item->quantity_remaining }} left</span>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-sm text-gray-400">&#10003; All stock healthy</div>
        @endforelse
      </div>
    </div>
  </div>

  <a href="{{ route('sales.create') }}"
    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition text-sm shadow-lg shadow-blue-500/20">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Start New Sale / Dispense
  </a>
</div>
@endsection
