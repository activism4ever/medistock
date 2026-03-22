@extends('layouts.app')
@section('title','Insurance Schemes') @section('page-title','Insurance Schemes')
@section('content')
<div class="pt-2 max-w-2xl">

  @if(session('success'))
  <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
    {{ session('success') }}
  </div>
  @endif

  @if($errors->has('error'))
  <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
    {{ $errors->first('error') }}
  </div>
  @endif

  {{-- Add New Scheme --}}
  <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">Add New Insurance Scheme</h3>
    <form action="{{ route('insurance.store') }}" method="POST" class="flex gap-3">
      @csrf
      <input type="text" name="name" placeholder="e.g. NHIA, JCHMA, PHIS..."
        value="{{ old('name') }}" required
        class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      <button class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold">
        + Add Scheme
      </button>
    </form>
    <p class="text-xs text-gray-400 mt-2">Co-payment is fixed at <strong>10%</strong> for all schemes.</p>
  </div>

  {{-- Schemes List --}}
  <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Scheme Name</th>
          <th class="text-center px-5 py-3 font-semibold text-gray-600">Co-payment</th>
          <th class="text-center px-5 py-3 font-semibold text-gray-600">Status</th>
          <th class="text-center px-5 py-3 font-semibold text-gray-600">Sales</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($schemes as $scheme)
        <tr class="hover:bg-gray-50/50 {{ !$scheme->is_active ? 'opacity-60' : '' }}">
          <td class="px-5 py-3 font-bold text-gray-800">{{ $scheme->name }}</td>
          <td class="px-5 py-3 text-center">
            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-semibold">
              {{ $scheme->copayment_percentage }}% patient
            </span>
          </td>
          <td class="px-5 py-3 text-center">
            <span class="text-xs px-2.5 py-0.5 rounded-full font-medium
              {{ $scheme->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
              {{ $scheme->is_active ? 'Active' : 'Disabled' }}
            </span>
          </td>
          <td class="px-5 py-3 text-center text-gray-500">
            {{ $scheme->sales()->count() }}
          </td>
          <td class="px-5 py-3 text-right whitespace-nowrap">
            {{-- Toggle --}}
            <form action="{{ route('insurance.toggle', $scheme) }}" method="POST" class="inline">
              @csrf @method('PATCH')
              <button class="text-xs px-3 py-1 rounded-lg mr-2
                {{ $scheme->is_active ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                {{ $scheme->is_active ? 'Disable' : 'Enable' }}
              </button>
            </form>
            {{-- Delete --}}
            @if($scheme->sales()->count() === 0)
            <form action="{{ route('insurance.destroy', $scheme) }}" method="POST" class="inline"
              onsubmit="return confirm('Delete {{ $scheme->name }}?')">
              @csrf @method('DELETE')
              <button class="text-xs text-red-500 hover:underline">Delete</button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">No schemes found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection