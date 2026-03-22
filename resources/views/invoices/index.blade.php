@extends('layouts.app')
@section('title','Invoices') @section('page-title','My Invoices')
@section('content')
<div class="pt-2">
  <div class="flex justify-end mb-5">
    <a href="{{ route('invoices.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium">+ New Invoice</a>
  </div>

  @if(session('success'))
  <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
  @endif

  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Invoice #</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Patient</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Type</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Cashier</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Amount</th>
          <th class="text-center px-5 py-3 font-semibold text-gray-600">Status</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Date</th>
          <th class="px-5 py-3"></th>
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
            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold">🏥 Insurance</span>
            @else
            <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">Normal</span>
            @endif
          </td>
          <td class="px-5 py-3 text-gray-500">{{ $inv->cashier?->name ?? '—' }}</td>
          <td class="px-5 py-3 text-right font-semibold">
            @if($inv->isInsurance())
              <span class="text-green-600">&#x20A6;{{ number_format($inv->copayment_amount,2) }}</span>
              <p class="text-xs text-gray-400">of &#x20A6;{{ number_format($inv->total_amount,2) }}</p>
            @else
              &#x20A6;{{ number_format($inv->total_amount,2) }}
            @endif
          </td>
          <td class="px-5 py-3 text-center">
            @php
              $colors = ['pending'=>'bg-yellow-100 text-yellow-700','paid'=>'bg-green-100 text-green-700','dispensed'=>'bg-blue-100 text-blue-700','cancelled'=>'bg-red-100 text-red-600'];
              $icons  = ['pending'=>'⏳','paid'=>'✅ Paid — Ready to Dispense','dispensed'=>'✔ Dispensed','cancelled'=>'✕'];
            @endphp
            <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $colors[$inv->status] }}">
              {{ $icons[$inv->status] }}
            </span>
          </td>
          <td class="px-5 py-3 text-gray-400 text-xs">{{ $inv->created_at->format('d M Y H:i') }}</td>
          <td class="px-5 py-3 text-right whitespace-nowrap">
            <a href="{{ route('invoices.show', $inv) }}" class="text-blue-600 hover:underline text-xs mr-3">View</a>
            @if($inv->isPaid())
            <form action="{{ route('invoices.dispense', $inv) }}" method="POST" class="inline"
              onsubmit="return confirm('Confirm dispensing for {{ $inv->patient_name }}?')">
              @csrf @method('PATCH')
              <button class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg">Dispense</button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400">No invoices yet. Create your first invoice.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">{{ $invoices->links() }}</div>
  </div>
</div>
@endsection