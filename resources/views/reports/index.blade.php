@extends('layouts.app')
@section('title','Sales Report') @section('page-title','Sales Report')
@section('content')
<div class="pt-2">
  <div class="flex items-center gap-3 mb-5">
    <form class="flex gap-2">
      <select name="period" class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="daily"   {{ $period=='daily'  ?'selected':'' }}>Today Only</option>
        <option value="monthly" {{ $period=='monthly'?'selected':'' }}>This Month</option>
        <option value="all"     {{ $period=='all'    ?'selected':'' }}>All Time</option>
      </select>
      <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium">Apply</button>
    </form>
  </div>

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

  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Receipt</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Patient</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Department</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Amount</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Profit</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($sales as $s)
        <tr class="hover:bg-gray-50/50">
          <td class="px-5 py-3"><a href="{{ route('sales.show',$s) }}" class="font-mono text-blue-600 hover:underline">{{ $s->receipt_number }}</a></td>
          <td class="px-5 py-3 text-gray-700">{{ $s->patient_name }}</td>
          <td class="px-5 py-3"><span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full">{{ $s->department->name }}</span></td>
          <td class="px-5 py-3 text-right font-semibold">&#x20A6;{{ number_format($s->total_amount,2) }}</td>
          <td class="px-5 py-3 text-right text-green-600 font-medium">&#x20A6;{{ number_format($s->total_profit,2) }}</td>
          <td class="px-5 py-3 text-gray-400 text-xs">{{ $s->created_at->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No sales found for this period.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">{{ $sales->links() }}</div>
  </div>
</div>
@endsection
