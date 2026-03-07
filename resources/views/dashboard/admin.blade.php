@extends('layouts.app')
@section('title','Admin Dashboard')
@section('page-title','Admin Dashboard')
@section('subtitle','System-wide overview')

@section('content')
<div class="pt-2">
  {{-- Stats row --}}
  <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 p-5 flex items-center gap-4">
      <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
      </div>
      <div>
        <p class="text-xs text-gray-500">Total Stock Value</p>
        <p class="text-xl font-bold text-gray-800">&#x20A6;{{ number_format($stats['totalStockValue'],2) }}</p>
      </div>
    </div>
    <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 p-5 flex items-center gap-4">
      <div class="w-11 h-11 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div>
        <p class="text-xs text-gray-500">All-Time Sales</p>
        <p class="text-xl font-bold text-gray-800">&#x20A6;{{ number_format($stats['totalSales'],2) }}</p>
      </div>
    </div>
    <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 p-5 flex items-center gap-4">
      <div class="w-11 h-11 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
      </div>
      <div>
        <p class="text-xs text-gray-500">Total Profit</p>
        <p class="text-xl font-bold text-gray-800">&#x20A6;{{ number_format($stats['totalProfit'],2) }}</p>
      </div>
    </div>
    <div class="xl:col-span-2 bg-teal-50 rounded-2xl border border-teal-200 p-5 flex items-center gap-4">
      <div class="w-11 h-11 bg-teal-100 rounded-xl flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
      <div>
        <p class="text-xs text-teal-600">Today's Sales</p>
        <p class="text-xl font-bold text-teal-800">&#x20A6;{{ number_format($stats['todaySales'],2) }}</p>
      </div>
    </div>
    <a href="{{ route('reports.expiry') }}"
      class="xl:col-span-1 bg-amber-50 rounded-2xl border border-amber-200 p-5 hover:shadow-md transition block">
      <p class="text-xs text-amber-700 mb-1">&#x23F0; Expiring Soon</p>
      <p class="text-3xl font-bold text-amber-800">{{ $stats['expiringCount'] }}</p>
      <p class="text-xs text-amber-600 mt-1">batches in 30 days</p>
    </a>
    <a href="{{ route('reports.low-stock') }}"
      class="xl:col-span-1 bg-red-50 rounded-2xl border border-red-200 p-5 hover:shadow-md transition block">
      <p class="text-xs text-red-700 mb-1">&#x26A0; Low Stock</p>
      <p class="text-3xl font-bold text-red-800">{{ $stats['lowStockCount'] }}</p>
      <p class="text-xs text-red-600 mt-1">batches &lt; 20 units</p>
    </a>
  </div>

  {{-- Alert cards --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
    {{-- Expiring --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-amber-500"></span> Expiring Within 30 Days
        </h3>
        <a href="{{ route('reports.expiry') }}" class="text-xs text-blue-600 hover:underline">View all &rarr;</a>
      </div>
      <div class="divide-y divide-gray-50">
        @forelse($expiringBatches as $batch)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
          <div>
            <p class="text-sm font-medium text-gray-800">{{ $batch->medicine->name }}</p>
            <p class="text-xs text-gray-400">Batch: {{ $batch->batch_number }}</p>
          </div>
          <div class="text-right">
            <span class="inline-block bg-amber-100 text-amber-800 text-xs px-2.5 py-0.5 rounded-full font-medium">
              {{ $batch->expiry_date->format('d M Y') }}
            </span>
            <p class="text-xs text-gray-400 mt-0.5">{{ $batch->quantity_remaining }} units</p>
          </div>
        </div>
        @empty
        <div class="px-5 py-10 text-center text-sm text-gray-400">&#10003; No batches expiring soon</div>
        @endforelse
      </div>
    </div>

    {{-- Low stock --}}
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-red-500"></span> Low Stock Alert (&lt; 20 units)
        </h3>
        <a href="{{ route('reports.low-stock') }}" class="text-xs text-blue-600 hover:underline">View all &rarr;</a>
      </div>
      <div class="divide-y divide-gray-50">
        @forelse($lowStockBatches as $batch)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50">
          <div>
            <p class="text-sm font-medium text-gray-800">{{ $batch->medicine->name }}</p>
            <p class="text-xs text-gray-400">Batch: {{ $batch->batch_number }}</p>
          </div>
          <span class="inline-block bg-red-100 text-red-800 text-xs px-2.5 py-0.5 rounded-full font-bold">
            {{ $batch->quantity_remaining }} left
          </span>
        </div>
        @empty
        <div class="px-5 py-10 text-center text-sm text-gray-400">&#10003; All stock levels healthy</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
