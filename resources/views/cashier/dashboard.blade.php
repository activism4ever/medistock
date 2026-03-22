@extends('layouts.app')
@section('title','Cashier Dashboard') @section('page-title','Cashier Dashboard')
@section('content')
<div class="pt-2" x-data="{ tab: 'pending' }">

  @if(session('success'))
  <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
  @endif

  {{-- Summary Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Today's Collections</p>
      <p class="text-2xl font-bold text-gray-800">&#x20A6;{{ number_format($todayPaid,2) }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ $todayCount }} invoices</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">This Month</p>
      <p class="text-2xl font-bold text-blue-700">&#x20A6;{{ number_format($monthPaid,2) }}</p>
      <p class="text-xs text-gray-400 mt-1">{{ $monthCount }} invoices</p>
    </div>
    <div class="col-span-2 bg-blue-50 rounded-2xl border border-blue-200 p-5 flex items-center justify-between">
      <div>
        <p class="text-xs text-blue-500 mb-1">View Full Collections Report</p>
        <p class="text-sm text-blue-700 font-medium">Filter by date & download PDF</p>
      </div>
      <a href="{{ route('cashier.collections') }}"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">
        View Report →
      </a>
    </div>
  </div>

  {{-- Tabs --}}
  <div class="flex gap-1 mb-5 bg-gray-100 p-1 rounded-xl w-fit">
    <button @click="tab='pending'"
      class="px-5 py-2 rounded-lg text-sm font-semibold transition"
      :class="tab==='pending' ? 'bg-white text-gray-800 shadow' : 'text-gray-500 hover:text-gray-700'">
      Pending Invoices
      @if($pending->count() > 0)
      <span class="ml-1.5 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $pending->count() }}</span>
      @endif
    </button>
    <button @click="tab='history'"
      class="px-5 py-2 rounded-lg text-sm font-semibold transition"
      :class="tab==='history' ? 'bg-white text-gray-800 shadow' : 'text-gray-500 hover:text-gray-700'">
      History
      <span class="ml-1.5 bg-gray-300 text-gray-600 text-xs px-1.5 py-0.5 rounded-full">{{ $history->total() }}</span>
    </button>
  </div>

  {{-- Tab: Pending Invoices --}}
  <div x-show="tab==='pending'" x-transition>
    @forelse($pending as $inv)
    <div class="bg-white rounded-2xl border border-gray-200 p-5 mb-4">

      {{-- Invoice Header --}}
      <div class="flex justify-between items-start mb-4">
        <div>
          <p class="font-mono text-blue-600 font-bold">{{ $inv->invoice_number }}</p>
          <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $inv->patient_name }}</p>
          @if($inv->patient_id)<p class="text-xs text-gray-400">ID: {{ $inv->patient_id }}</p>@endif
          <p class="text-xs text-gray-400 mt-1">From: {{ $inv->createdBy->name }}
            @if($inv->drawer_number) · 🗄 Drawer {{ $inv->drawer_number }}@endif
          </p>
        </div>
        <div class="text-right">
          @if($inv->isInsurance())
          <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold block mb-1">🏥 Insurance</span>
          <p class="text-xs text-gray-400">
            {{ $inv->insuranceScheme?->name }}
            @if($inv->sector) · {{ ucfirst($inv->sector) }} Sector @endif
          </p>
          @if($inv->enrolee_id)<p class="text-xs text-gray-400">{{ $inv->enrolee_id }}</p>@endif
          @endif
          <p class="text-xs text-gray-400 mt-1">{{ $inv->created_at->diffForHumans() }}</p>
        </div>
      </div>

      {{-- Prescribed Drugs --}}
      <div class="bg-gray-50 rounded-xl p-3 mb-4">
        <p class="text-xs font-semibold text-gray-500 mb-2 uppercase tracking-wide">Prescribed Drugs</p>
        @foreach($inv->items as $item)
        <div class="flex justify-between text-sm py-1 border-b border-gray-100 last:border-0">
          <span class="text-gray-700">{{ $item->batch->medicine->name }}</span>
          <span class="text-gray-500">{{ $item->quantity }} × &#x20A6;{{ number_format($item->selling_price,2) }}</span>
          <span class="font-semibold">&#x20A6;{{ number_format($item->subtotal,2) }}</span>
        </div>
        @endforeach
      </div>

      {{-- Payment Summary --}}
      <div class="border-t border-gray-200 pt-3 mb-4">
        @if($inv->isInsurance())
        <div class="space-y-1 text-sm">
          <div class="flex justify-between text-gray-500">
            <span>Total Drug Cost</span>
            <span>&#x20A6;{{ number_format($inv->total_amount,2) }}</span>
          </div>
          @if($inv->isInformal())
          <div class="flex justify-between text-purple-600">
            <span>{{ $inv->insuranceScheme?->name }} Covers (100%)</span>
            <span>&#x20A6;{{ number_format($inv->total_amount,2) }}</span>
          </div>
          <div class="flex justify-between font-bold text-blue-600 text-base border-t pt-2 mt-1">
            <span>COLLECT FROM PATIENT (0%) — INFORMAL</span>
            <span>&#x20A6;0.00</span>
          </div>
          @else
          <div class="flex justify-between text-green-600">
            <span>{{ $inv->insuranceScheme?->name }} Covers (90%)</span>
            <span>&#x20A6;{{ number_format($inv->insurance_amount,2) }}</span>
          </div>
          <div class="flex justify-between font-bold text-red-600 text-base border-t pt-2 mt-1">
            <span>COLLECT FROM PATIENT (10%)</span>
            <span>&#x20A6;{{ number_format($inv->copayment_amount,2) }}</span>
          </div>
          @endif
        </div>
        @else
        <div class="flex justify-between font-bold text-gray-800 text-lg">
          <span>COLLECT FROM PATIENT</span>
          <span>&#x20A6;{{ number_format($inv->total_amount,2) }}</span>
        </div>
        @endif
      </div>

      {{-- Confirm Payment --}}
      <form action="{{ route('cashier.pay', $inv) }}" method="POST"
        onsubmit="return confirm('Confirm payment received from {{ $inv->patient_name }}?')">
        @csrf @method('PATCH')
        <button class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl text-sm transition">
          ✓ Confirm Payment & Issue Receipt
        </button>
      </form>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-gray-200 py-16 text-center text-gray-400">
      <p class="text-4xl mb-3">✓</p>
      <p class="font-medium">No pending invoices</p>
      <p class="text-sm mt-1">All payments have been processed</p>
    </div>
    @endforelse
  </div>

  {{-- Tab: History --}}
  <div x-show="tab==='history'" x-transition>
    <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-100">
          <tr>
            <th class="text-left px-5 py-3 font-semibold text-gray-600">Invoice #</th>
            <th class="text-left px-5 py-3 font-semibold text-gray-600">Patient</th>
            <th class="text-left px-5 py-3 font-semibold text-gray-600">Type</th>
            <th class="text-left px-5 py-3 font-semibold text-gray-600">Drawer</th>
            <th class="text-right px-5 py-3 font-semibold text-gray-600">Total</th>
            <th class="text-right px-5 py-3 font-semibold text-gray-600">Collected</th>
            <th class="text-left px-5 py-3 font-semibold text-gray-600">Paid At</th>
            <th class="px-5 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          @forelse($history as $inv)
          <tr class="hover:bg-gray-50/50">
            <td class="px-5 py-3 font-mono text-blue-600">{{ $inv->invoice_number }}</td>
            <td class="px-5 py-3">
              <p class="font-medium text-gray-800">{{ $inv->patient_name }}</p>
              @if($inv->patient_id)<p class="text-xs text-gray-400">ID: {{ $inv->patient_id }}</p>@endif
            </td>
            <td class="px-5 py-3">
              @if($inv->isInsurance())
              <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold">
                🏥 {{ $inv->insuranceScheme?->name }}
              </span>
              @if($inv->sector)
              <p class="text-xs mt-0.5 {{ $inv->isInformal() ? 'text-purple-600' : 'text-green-600' }}">
                {{ $inv->isInformal() ? 'Informal' : 'Formal' }}
              </p>
              @endif
              @else
              <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">Normal</span>
              @endif
            </td>
            <td class="px-5 py-3">
              @if($inv->drawer_number)
              <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">🗄 {{ $inv->drawer_number }}</span>
              @else
              <span class="text-gray-300 text-xs">—</span>
              @endif
            </td>
            <td class="px-5 py-3 text-right font-semibold">&#x20A6;{{ number_format($inv->total_amount,2) }}</td>
            <td class="px-5 py-3 text-right font-bold text-green-600">
              &#x20A6;{{ number_format($inv->isInsurance() && !$inv->isInformal() ? $inv->copayment_amount : ($inv->isInformal() ? 0 : $inv->total_amount), 2) }}
            </td>
            <td class="px-5 py-3 text-gray-400 text-xs">{{ $inv->paid_at?->format('d M Y H:i') }}</td>
            <td class="px-5 py-3 text-right">
              <a href="{{ route('cashier.receipt', $inv) }}"
                target="_blank"
                class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                🖨 Reprint
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400">No payment history yet.</td></tr>
          @endforelse
        </tbody>
      </table>
      <div class="px-5 py-4 border-t border-gray-100">{{ $history->links() }}</div>
    </div>
  </div>

</div>
@endsection