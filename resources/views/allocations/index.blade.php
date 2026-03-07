@extends('layouts.app')
@section('title','Allocations') @section('page-title','Stock Allocations')
@section('content')
<div class="pt-2">
  <div class="flex justify-end mb-5">
    <a href="{{ route('allocations.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium">+ New Allocation</a>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Medicine</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Batch #</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Department</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Qty Allocated</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Allocated By</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($allocations as $a)
        <tr class="hover:bg-gray-50/50">
          <td class="px-5 py-3 font-medium text-gray-800">{{ $a->batch->medicine->name }}</td>
          <td class="px-5 py-3 font-mono text-gray-500">{{ $a->batch->batch_number }}</td>
          <td class="px-5 py-3">
            <span class="bg-indigo-100 text-indigo-700 text-xs px-2.5 py-0.5 rounded-full font-medium">{{ $a->department->name }}</span>
          </td>
          <td class="px-5 py-3 text-right font-bold text-blue-700">{{ number_format($a->quantity_allocated) }}</td>
          <td class="px-5 py-3 text-gray-500">{{ $a->allocatedBy->name }}</td>
          <td class="px-5 py-3 text-gray-400">{{ $a->created_at->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No allocations yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">{{ $allocations->links() }}</div>
  </div>
</div>
@endsection
