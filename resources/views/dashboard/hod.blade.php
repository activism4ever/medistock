@extends('layouts.app')
@section('title','HOD Dashboard') @section('page-title','HOD Pharmacy Dashboard')
@section('content')
<div class="pt-2">

  {{-- Today Summary --}}
  <div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Today's Total Sales (All Drawers)</p>
      <p class="text-2xl font-bold text-gray-800">&#x20A6;{{ number_format($todaySales,2) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Today's Transactions</p>
      <p class="text-2xl font-bold text-blue-700">{{ number_format($todayCount) }}</p>
    </div>
  </div>

  {{-- Drawer Performance --}}
  <h3 class="text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wide">Drawer Performance</h3>
  <div class="grid grid-cols-3 gap-4 mb-6">
    @foreach($drawers as $num => $d)
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <div class="flex items-center justify-between mb-3">
        <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full font-bold">🗄 Drawer {{ $num }}</span>
        @if($d['pending_invoices'] > 0)
        <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">{{ $d['pending_invoices'] }} pending</span>
        @endif
      </div>
      <p class="text-xs text-gray-400 mb-0.5">Today's Sales</p>
      <p class="text-xl font-bold text-gray-800 mb-2">&#x20A6;{{ number_format($d['today_sales'],2) }}</p>
      <p class="text-xs text-gray-400 mb-0.5">This Month</p>
      <p class="text-sm font-semibold text-green-700">&#x20A6;{{ number_format($d['month_sales'],2) }}</p>
      <p class="text-xs text-gray-400 mt-2">{{ $d['total_count'] }} transactions today</p>
    </div>
    @endforeach
  </div>

  {{-- Drawer Stock Summary --}}
  <h3 class="text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wide">Remaining Stock Per Drawer</h3>
  <div class="grid grid-cols-3 gap-4 mb-6">
    @foreach([1,2,3] as $num)
    <div class="bg-white rounded-2xl border border-gray-200">
      <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
        <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full font-bold">🗄 Drawer {{ $num }}</span>
        <span class="text-xs text-gray-400">{{ count($drawerStock[$num]) }} medicines</span>
      </div>
      <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto">
        @forelse($drawerStock[$num] as $s)
        <div class="px-5 py-3 flex justify-between items-center">
          <div>
            <p class="text-sm font-medium text-gray-700">{{ $s['medicine'] }}</p>
            <p class="text-xs text-gray-400">Batch: {{ $s['batch'] }} · Exp: {{ $s['expiry'] }}</p>
          </div>
          <span class="text-sm font-bold {{ $s['qty'] < 20 ? 'text-red-600' : 'text-blue-700' }}">
            {{ $s['qty'] }} units
          </span>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-xs text-gray-400">No stock allocated</div>
        @endforelse
      </div>
      @if(count($drawerStock[$num]) > 0)
      <div class="px-5 py-2 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
        <p class="text-xs text-gray-500 font-semibold">
          Total: {{ collect($drawerStock[$num])->sum('qty') }} units
        </p>
      </div>
      @endif
    </div>
    @endforeach
  </div>

  <div class="grid grid-cols-2 gap-5">
    {{-- Low Stock Alert --}}
    <div class="bg-white rounded-2xl border border-gray-200">
      <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-sm">⚠ Low Stock Alert</h3>
        <span class="text-xs text-gray-400">Pharmacy only</span>
      </div>
      <div class="divide-y divide-gray-50">
        @forelse($lowStock as $s)
        <div class="px-5 py-3 flex justify-between items-center">
          <p class="text-sm text-gray-700">{{ $s->batch->medicine->name }}</p>
          <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full font-semibold">
            {{ $s->quantity_remaining }} left
          </span>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-xs text-gray-400">✓ All stock levels healthy</div>
        @endforelse
      </div>
    </div>

    {{-- Recent Allocations --}}
    <div class="bg-white rounded-2xl border border-gray-200">
      <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">Recent Allocations</h3>
      </div>
      <div class="divide-y divide-gray-50">
        @forelse($allocations as $a)
        <div class="px-5 py-3">
          <div class="flex justify-between items-start">
            <div>
              <p class="text-sm font-medium text-gray-700">{{ $a->batch->medicine->name }}</p>
              <p class="text-xs text-gray-400">{{ $a->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="text-right">
              <p class="text-sm font-bold text-blue-700">{{ number_format($a->quantity_allocated) }} units</p>
              @if($a->drawer_number)
              <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">🗄 Drawer {{ $a->drawer_number }}</span>
              @endif
            </div>
          </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-xs text-gray-400">No allocations yet</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection