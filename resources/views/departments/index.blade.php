@extends('layouts.app')
@section('title','Departments') @section('page-title','Departments')
@section('content')
<div class="pt-2">
  <div class="flex justify-end mb-5">
    <a href="{{ route('departments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium">+ Add Department</a>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @forelse($departments as $d)
    <div class="bg-white rounded-2xl border border-gray-200 p-5 hover:shadow-md transition">
      <div class="flex items-start justify-between mb-3">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
        </div>
        <span class="text-xs px-2 py-0.5 rounded-full {{ $d->is_active?'bg-green-100 text-green-700':'bg-gray-100 text-gray-500' }}">
          {{ $d->is_active?'Active':'Inactive' }}
        </span>
      </div>
      <h3 class="font-semibold text-gray-800 mb-1">{{ $d->name }}</h3>
      @if($d->description)<p class="text-xs text-gray-400 mb-3">{{ $d->description }}</p>@endif
      <div class="flex gap-4 text-xs text-gray-400 mb-4">
        <span>{{ $d->users_count }} users</span>
        <span>{{ $d->sales_count }} sales</span>
      </div>
      <a href="{{ route('departments.edit',$d) }}" class="text-xs text-blue-600 hover:underline">Edit</a>
    </div>
    @empty
    <div class="col-span-4 py-10 text-center text-gray-400">No departments found.</div>
    @endforelse
  </div>
</div>
@endsection
