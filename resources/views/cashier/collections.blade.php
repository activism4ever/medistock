@extends('layouts.app')
@section('title','My Collections') @section('page-title','My Collections')
@section('content')
<div class="pt-2">

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
      <a href="{{ route('cashier.collections.pdf', ['from' => $from, 'to' => $to]) }}"
        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold ml-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Download PDF
      </a>
    </form>
  </div>

  {{-- Summary Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Total Collected</p>
      <p class="text-2xl font-bold text-gray-800">&#x20A6;{{ number_format($summary['total_amount'],2) }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ $summary['total_count'] }} invoices</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Normal Sales</p>
      <p class="text-2xl font-bold text-blue-700">&#x20A6;{{ number_format($summary['normal_amount'],2) }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ $summary['normal_count'] }} invoices</p>
    </div>
    <div class="bg-green-50 rounded-2xl border border-green-200 p-5">
      <p class="text-xs text-green-500 mb-1">Insurance Co-payments</p>
      <p class="text-2xl font-bold text-green-700">&#x20A6;{{ number_format($summary['insurance_amount'],2) }}</p>
      <p class="text-xs text-green-500 mt-1">{{ $summary['insurance_count'] }} invoices</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Period</p>
      <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($from)->format('d M Y') }}</p>
      <p class="text-xs text-gray-400">to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    </div>
  </div>

  {{-- Collections Table --}}
  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Invoice #</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Patient</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Type</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">From Drawer</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Total</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Collected</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Paid At</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($invoices as $inv)
        <tr class="hover:bg-gray-50/50">
          <td class="px-5 py-3 font-mono text-blue-600">{{ $inv->invoice_number }}</td>
          <td class="px-5 py-3">
            <p class="font-medium text-gray-800">{{ $inv->patient_name }}</p>
            @if($inv->patient_id)<p class="text-xs text-gray-400">ID: {{ $inv->patient_id }}</p>@endif
          </td>
          <td class="px-5 py-3">
            @if($inv->isInsurance())
            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold">🏥 {{ $inv->insuranceScheme?->name }}</span>
            @else
            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">Normal</span>
            @endif
          </td>
          <td class="px-5 py-3">
            @if($inv->drawer_number)
            <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full font-semibold">🗄 Drawer {{ $inv->drawer_number }}</span>
            @else <span class="text-gray-300 text-xs">—</span> @endif
          </td>
          <td class="px-5 py-3 text-right font-semibold">&#x20A6;{{ number_format($inv->total_amount,2) }}</td>
          <td class="px-5 py-3 text-right font-bold text-green-600">
            &#x20A6;{{ number_format($inv->isInsurance() ? $inv->copayment_amount : $inv->total_amount, 2) }}
          </td>
          <td class="px-5 py-3 text-gray-400 text-xs">{{ $inv->paid_at?->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No collections found for this period.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">{{ $invoices->links() }}</div>
  </div>
</div>
@endsection