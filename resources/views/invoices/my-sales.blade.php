@extends('layouts.app')
@section('title','My Sales') @section('page-title','My Sales')
@section('content')
<div class="pt-2">

  @if(auth()->user()->drawer_number)
  <div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-3">
    <span class="text-yellow-600 font-bold text-sm">🗄 Drawer {{ auth()->user()->drawer_number }} Sales</span>
  </div>
  @endif

  {{-- Date Filter --}}
  <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5">
    <form class="flex items-end gap-3 flex-wrap">
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">From Date</label>
        <input type="date" name="from" value="{{ $from }}"
          class="border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">To Date</label>
        <input type="date" name="to" value="{{ $to }}"
          class="border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">Apply</button>
      <a href="{{ route('invoices.my-sales.pdf', ['from' => $from, 'to' => $to]) }}"
        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold ml-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Download PDF
      </a>
    </form>
  </div>

  {{-- Summary Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Total Sales</p>
      <p class="text-2xl font-bold text-gray-800">&#x20A6;{{ number_format($summary['total_amount'],2) }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ $summary['total_count'] }} invoices</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Normal Sales</p>
      <p class="text-2xl font-bold text-blue-700">&#x20A6;{{ number_format($summary['normal_amount'],2) }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ $summary['normal_count'] }} invoices</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Period</p>
      <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</p>
      <p class="text-xs text-gray-400">to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    </div>
  </div>

  {{-- Insurance Breakdown --}}
  <div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
      <p class="text-xs text-green-500 mb-1">JCHMA Formal (10%)</p>
      <p class="text-xl font-bold text-green-700">&#x20A6;{{ number_format($summary['jchma_formal_amount'],2) }}</p>
      <p class="text-xs text-green-500 mt-1">{{ $summary['jchma_formal_count'] }} invoices</p>
    </div>
    <div class="bg-purple-50 rounded-2xl border border-purple-200 p-5">
      <p class="text-xs text-purple-500 mb-1">JCHMA Informal (0%)</p>
      <p class="text-xl font-bold text-purple-700">&#x20A6;{{ number_format($summary['jchma_informal_amount'],2) }}</p>
      <p class="text-xs text-purple-500 mt-1">{{ $summary['jchma_informal_count'] }} invoices</p>
    </div>
    <div class="bg-blue-50 rounded-2xl border border-blue-200 p-5">
      <p class="text-xs text-blue-500 mb-1">NHIA (10%)</p>
      <p class="text-xl font-bold text-blue-700">&#x20A6;{{ number_format($summary['nhia_amount'],2) }}</p>
      <p class="text-xs text-blue-500 mt-1">{{ $summary['nhia_count'] }} invoices</p>
    </div>
  </div>

  {{-- Sales Table --}}
  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Invoice #</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Patient</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Type</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Cashier</th>
          <th class="text-center px-5 py-3 font-semibold text-gray-600">Status</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Total</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Paid At</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($invoices as $inv)
        <tr class="hover:bg-gray-50/50">
          <td class="px-5 py-3">
            <a href="{{ route('invoices.show', $inv) }}" class="font-mono text-blue-600 hover:underline">{{ $inv->invoice_number }}</a>
          </td>
          <td class="px-5 py-3">
            <p class="font-medium text-gray-800">{{ $inv->patient_name }}</p>
            @if($inv->patient_id)<p class="text-xs text-gray-400">ID: {{ $inv->patient_id }}</p>@endif
          </td>
         <td class="px-5 py-3">
            @if($inv->isInsurance())
            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold">🏥 {{ $inv->insuranceScheme?->name }}</span>
            @if($inv->sector)
            <p class="text-xs mt-0.5 {{ $inv->isInformal() ? 'text-purple-600' : 'text-green-600' }}">
              {{ $inv->isInformal() ? 'Informal' : 'Formal' }}
            </p>
            @endif
            @else
            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">Normal</span>
            @endif
          </td>
          <td class="px-5 py-3 text-gray-500">{{ $inv->cashier?->name ?? '—' }}</td>
          <td class="px-5 py-3 text-center">
            @if($inv->isDispensed())
            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-semibold">✔ Dispensed</span>
            @else
            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold">✅ Paid</span>
            @endif
          </td>
          <td class="px-5 py-3 text-right font-semibold">&#x20A6;{{ number_format($inv->total_amount,2) }}</td>
          <td class="px-5 py-3 text-gray-400 text-xs">{{ $inv->paid_at?->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No sales found for this period.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">{{ $invoices->links() }}</div>
  </div>
</div>
@endsection