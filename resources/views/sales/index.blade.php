@extends('layouts.app')
@section('title','Sales') @section('page-title','Sales History')
@section('content')
<div class="pt-2">
  <div class="flex items-center gap-3 mb-5">
    <form class="flex gap-2">
      <input type="date" name="date" value="{{ request('date') }}" class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl text-sm">Filter</button>
      @if(request('date'))<a href="{{ route('sales.index') }}" class="text-sm text-gray-400 hover:text-gray-600 px-2 py-2">Clear</a>@endif
    </form>
    @if(auth()->user()->isDepartmentUser())
    <a href="{{ route('sales.create') }}" class="ml-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium">+ New Sale</a>
    @endif
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Receipt #</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Patient</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Department</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Dispensed By</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Amount</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Profit</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Date</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($sales as $s)
        <tr class="hover:bg-gray-50/50">
          <td class="px-5 py-3 font-mono font-medium text-blue-600">{{ $s->receipt_number }}</td>
          <td class="px-5 py-3 font-medium text-gray-800">{{ $s->patient_name }}</td>
          <td class="px-5 py-3"><span class="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full">{{ $s->department->name }}</span></td>
          <td class="px-5 py-3 text-gray-500">{{ $s->soldBy->name }}</td>
          <td class="px-5 py-3 text-right font-semibold">&#x20A6;{{ number_format($s->total_amount,2) }}</td>
          <td class="px-5 py-3 text-right text-green-600 font-medium">&#x20A6;{{ number_format($s->total_profit,2) }}</td>
          <td class="px-5 py-3 text-gray-400 text-xs">{{ $s->created_at->format('d M Y H:i') }}</td>
          <td class="px-5 py-3 text-right whitespace-nowrap">
            <a href="{{ route('sales.show',$s) }}" class="text-blue-600 hover:underline text-xs mr-2">View</a>
            <a href="{{ route('sales.receipt',$s) }}" class="text-gray-400 hover:underline text-xs">Receipt</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400">No sales found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">{{ $sales->links() }}</div>
  </div>
</div>
@endsection
