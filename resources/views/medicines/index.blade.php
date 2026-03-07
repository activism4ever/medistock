@extends('layouts.app')
@section('title','Medicines') @section('page-title','Medicine Catalogue')
@section('content')
<div class="pt-2">
  <div class="flex items-center justify-between mb-5">
    <form class="flex gap-2">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search medicines..."
        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
      <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl text-sm font-medium">Search</button>
      @if(request('search'))<a href="{{ route('medicines.index') }}" class="px-3 py-2 text-sm text-gray-400 hover:text-gray-600">Clear</a>@endif
    </form>
    <a href="{{ route('medicines.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium">+ Add Medicine</a>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Name</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Generic</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Dosage / Unit</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Category</th>
          <th class="text-right px-5 py-3 font-semibold text-gray-600">Total Stock</th>
          <th class="text-center px-5 py-3 font-semibold text-gray-600">Status</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($medicines as $m)
        <tr class="hover:bg-gray-50/50">
          <td class="px-5 py-3 font-medium text-gray-800">{{ $m->name }}</td>
          <td class="px-5 py-3 text-gray-500">{{ $m->generic_name ?? '—' }}</td>
          <td class="px-5 py-3 text-gray-500">{{ $m->dosage ?? '—' }} / {{ ucfirst($m->unit) }}</td>
          <td class="px-5 py-3">
            @if($m->category)
            <span class="bg-blue-50 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $m->category }}</span>
            @else —@endif
          </td>
          <td class="px-5 py-3 text-right font-semibold {{ ($m->batches_sum_quantity_remaining ?? 0) < 20 ? 'text-red-600' : 'text-gray-800' }}">
            {{ number_format($m->batches_sum_quantity_remaining ?? 0) }}
          </td>
          <td class="px-5 py-3 text-center">
            <span class="inline-block text-xs px-2.5 py-0.5 rounded-full {{ $m->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
              {{ $m->is_active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td class="px-5 py-3 text-right whitespace-nowrap">
            <a href="{{ route('medicines.show',$m) }}" class="text-blue-600 hover:underline mr-3 text-xs">View</a>
            <a href="{{ route('medicines.edit',$m) }}" class="text-gray-500 hover:underline text-xs">Edit</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No medicines found. <a href="{{ route('medicines.create') }}" class="text-blue-600 underline">Add one</a></td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">{{ $medicines->links() }}</div>
  </div>
</div>
@endsection
