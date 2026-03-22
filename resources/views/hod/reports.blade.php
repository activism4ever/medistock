@extends('layouts.app')
@section('title','HOD Reports') @section('page-title','Pharmacy Reports')
@section('content')
<div class="pt-2">

  {{-- Date Filter --}}
  <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
    <form class="flex items-end gap-3 flex-wrap">
      {{-- Quick Filters --}}
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Quick Filter</label>
        <div class="flex gap-2">
          <a href="{{ route('hod.reports', ['period'=>'today']) }}"
            class="px-4 py-2.5 rounded-xl text-sm font-medium border transition
              {{ $period==='today' ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
            Today
          </a>
        </div>
      </div>

      <div class="border-l border-gray-200 pl-4">
        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Custom Range</label>
        <div class="flex gap-2 items-center">
          <input type="date" name="from" value="{{ $from }}"
            class="border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <span class="text-gray-400 text-sm">to</span>
          <input type="date" name="to" value="{{ $to }}"
            class="border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <input type="hidden" name="period" value="custom">
          <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">Apply</button>
        </div>
      </div>

      {{-- Export Buttons --}}
      <div class="ml-auto flex gap-2">
        <a href="{{ route('hod.reports.sales-pdf', ['from'=>$from,'to'=>$to]) }}"
          class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Sales PDF
        </a>
        <a href="{{ route('hod.reports.stock-pdf') }}"
          class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Stock PDF
        </a>
      </div>
    </form>
  </div>

  {{-- Overall Summary --}}
  <div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Total Revenue</p>
      <p class="text-2xl font-bold text-gray-800">&#x20A6;{{ number_format($summary['total_amount'],2) }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ $summary['total_count'] }} invoices</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Normal Sales</p>
      <p class="text-2xl font-bold text-blue-700">&#x20A6;{{ number_format($summary['normal_amount'],2) }}</p>
    </div>
    <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
      <p class="text-xs text-green-500 mb-1">Insurance Co-payments</p>
      <p class="text-2xl font-bold text-green-700">&#x20A6;{{ number_format($summary['insurance_amount'],2) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Period</p>
      <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</p>
      <p class="text-xs text-gray-400">to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    </div>
  </div>

  {{-- Sales Per Drawer --}}
  <h3 class="text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wide">Sales Per Drawer</h3>
  <div class="grid grid-cols-3 gap-4 mb-6">
    @foreach([1,2,3] as $num)
    @php $d = $drawerSales[$num]; @endphp
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <div class="flex items-center justify-between mb-4">
        <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full font-bold">🗄 Drawer {{ $num }}</span>
        @if($d['pending'] > 0)
        <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">{{ $d['pending'] }} pending</span>
        @endif
      </div>
      <p class="text-xs text-gray-400 mb-0.5">Total Sales</p>
      <p class="text-xl font-bold text-gray-800 mb-3">&#x20A6;{{ number_format($d['total_amount'],2) }}</p>
      <div class="space-y-2 text-xs">
        <div class="flex justify-between bg-blue-50 rounded-lg px-3 py-2">
          <span class="text-blue-600 font-medium">Normal ({{ $d['normal_count'] }})</span>
          <span class="font-bold text-blue-700">&#x20A6;{{ number_format($d['normal_amount'],2) }}</span>
        </div>
        <div class="flex justify-between bg-green-50 rounded-lg px-3 py-2">
          <span class="text-green-600 font-medium">Insurance ({{ $d['insurance_count'] }})</span>
          <span class="font-bold text-green-700">&#x20A6;{{ number_format($d['insurance_amount'],2) }}</span>
        </div>
        <div class="flex justify-between bg-gray-50 rounded-lg px-3 py-2">
          <span class="text-gray-500">Total Invoices</span>
          <span class="font-bold text-gray-700">{{ $d['total_count'] }}</span>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Stock Per Drawer --}}
  <h3 class="text-sm font-semibold text-gray-600 mb-3 uppercase tracking-wide">Current Stock Per Drawer</h3>
  <div class="grid grid-cols-3 gap-4 mb-6">
    @foreach([1,2,3] as $num)
    <div class="bg-white rounded-2xl border border-gray-200">
      <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
        <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full font-bold">🗄 Drawer {{ $num }}</span>
        <span class="text-xs text-gray-400">{{ $drawerStock[$num]->count() }} medicines</span>
      </div>
      <div class="divide-y divide-gray-50 max-h-48 overflow-y-auto">
        @forelse($drawerStock[$num] as $s)
        <div class="px-5 py-3 flex justify-between items-center">
          <div>
            <p class="text-sm font-medium text-gray-700">{{ $s->batch->medicine->name }}</p>
            <p class="text-xs text-gray-400">Exp: {{ $s->batch->expiry_date->format('d M Y') }}</p>
          </div>
          <span class="text-sm font-bold {{ $s->quantity_remaining < 20 ? 'text-red-600' : 'text-blue-700' }}">
            {{ $s->quantity_remaining }} units
          </span>
        </div>
        @empty
        <div class="px-5 py-6 text-center text-xs text-gray-400">No stock</div>
        @endforelse
      </div>
      @if($drawerStock[$num]->count() > 0)
      <div class="px-5 py-2 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
        <p class="text-xs text-gray-500 font-semibold">Total: {{ $drawerStock[$num]->sum('quantity_remaining') }} units</p>
      </div>
      @endif
    </div>
    @endforeach
  </div>

  <div class="grid grid-cols-2 gap-5">
    {{-- Low Stock --}}
    <div class="bg-white rounded-2xl border border-gray-200">
      <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">⚠ Low Stock (below 20 units)</h3>
      </div>
      <div class="divide-y divide-gray-50">
        @forelse($lowStock as $s)
        <div class="px-5 py-3 flex justify-between items-center">
          <p class="text-sm text-gray-700">{{ $s->batch->medicine->name }}</p>
          <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full font-semibold">{{ $s->quantity_remaining }} left</span>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-xs text-gray-400">✓ All stock levels healthy</div>
        @endforelse
      </div>
    </div>

    {{-- Expiring Soon --}}
    <div class="bg-white rounded-2xl border border-gray-200">
      <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800 text-sm">🕐 Expiring Within 30 Days</h3>
      </div>
      <div class="divide-y divide-gray-50">
        @forelse($expiring as $s)
        <div class="px-5 py-3 flex justify-between items-center">
          <div>
            <p class="text-sm text-gray-700">{{ $s->batch->medicine->name }}</p>
            <p class="text-xs text-gray-400">Batch: {{ $s->batch->batch_number }}</p>
          </div>
          <div class="text-right">
            <span class="bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full font-semibold">
              {{ $s->batch->expiry_date->format('d M Y') }}
            </span>
            <p class="text-xs text-gray-400 mt-0.5">{{ $s->quantity_remaining }} units</p>
          </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-xs text-gray-400">✓ No medicines expiring soon</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection