@extends('layouts.app')
@section('title','Add Department') @section('page-title','Add Department')
@section('content')
<div class="pt-2 max-w-md">
  <div class="bg-white rounded-2xl border border-gray-200 p-7">
    <form action="{{ route('departments.store') }}" method="POST" class="space-y-5">
      @csrf
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Department Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" required
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="e.g. ICU, Pediatrics">
        @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
        <textarea name="description" rows="3"
          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
      </div>
      <div class="flex gap-3 pt-2">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold">Create Department</button>
        <a href="{{ route('departments.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-medium">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
