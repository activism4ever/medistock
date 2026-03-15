@extends('layouts.app')
@section('title','Sales Report') @section('page-title','Sales Report')
@section('content')
<div class="pt-2">

  {{-- Filters & Export Buttons --}}
  <div class="flex items-center gap-3 mb-5 flex-wrap">
    <form class="flex gap-2 flex-wrap">
      <select name="period" class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="daily"   {{ $period=='daily'  ?'selected':'' }}>Today Only</option>
        <option value="monthly" {{ $period=='monthly'?'selected':'' }}>This Month</option>
        <option value="all"     {{ $period=='all'    ?'selected':'' }}>All Time</option>
      </select>

      <select name="drawer" class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">All Drawers</option>
        <option value="1" {{ request('drawer')=='1'?'selected':'' }}>🗄 Drawer 1</option>
        <option value="2" {{ request('drawer')=='2'?'selected':'' }}>🗄 Drawer 2</option>
        <option value="3" {{ request('drawer')=='3'?'selected':'' }}>🗄 Drawer 3</option>
      </select>

      <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium">Apply</button>
    </form>

    {{-- PDF Button --}}
    <a href="{{ route('reports.download-pdf', ['period' => $period, 'drawer' => request('drawer')]) }}"
       class="ml-auto flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl text-sm font-semibold shadow transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      Download PDF
    </a>

    {{-- Excel Button --}}
    <a href="{{ route('reports.download-excel', ['period' => $period, 'drawer' => request('drawer')]) }}"
       class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-xl text-sm font-semibold shadow transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      Export Excel
    </a>
  </div>

  {{-- Summary Cards --}}
  <div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Total Revenue</p>
      <p class="text-2xl font-bold text-gray-800">&#x20A6;{{ number_format($summary['total_amount'],2) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Total Profit</p>
      <p class="text-2xl font-bold text-green-700">&#x20A6;{{ number_format($summary['total_profit'],2) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <p class="text-xs text-gray-400 mb-1">Transactions</p>
      <p class="text-2xl font-bold text-blue-700">{{ number_format($summary['total_count']) }}</p>
    </div>
  </div>

  {{-- Active Filter Badge --}}
  @if(request('drawer'))
  <div class="mb-4 flex items-center gap-2">
    <span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1 rounded-full font-semibold">
      🗄 Filtered: Drawer {{ request('drawer') }}
    </span>
    <a href="{{ route('reports.index', ['period' => $period]) }}" class="text-xs text-gray-400 hover:text-red-500">✕ Clear filter</a>
  </div>
  @endif

  {{-- Sales Table --}}
  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Receipt</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Patient</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Department</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Drawer</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Amount</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Profit</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($sales as $s)
        <tr class="hover:bg-gray-50/50">
          <td class="px-5 py-3">
            <a href="{{ route('sales.show',$s) }}" class="font-mono text-blue-600 hover:underline">{{ $s->receipt_number }}</a>
          </td>
          <td class="px-5 py-3 text-gray-700">{{ $s->patient_name }}</td>
          <td class="px-5 py-3">
            <span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full">{{ $s->department->name }}</span>
          </td>
          <td class="px-5 py-3">
            @if($s->drawer_number)
              <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full font-semibold">
                🗄 Drawer {{ $s->drawer_number }}
              </span>
            @else
              <span class="text-gray-300 text-xs">—</span>
            @endif
          </td>
          <td class="px-5 py-3 text-right font-semibold">&#x20A6;{{ number_format($s->total_amount,2) }}</td>
          <td class="px-5 py-3 text-right text-green-600 font-medium">&#x20A6;{{ number_format($s->total_profit,2) }}</td>
          <td class="px-5 py-3 text-gray-400 text-xs">{{ $s->created_at->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No sales found for this period.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">{{ $sales->links() }}</div>
  </div>
</div>
@endsection