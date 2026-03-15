@extends('layouts.app')
@section('title','Users') @section('page-title','User Management')
@section('content')
<div class="pt-2">
  <div class="flex justify-end mb-5">
    <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium">+ Add User</a>
  </div>
  <div class="bg-white rounded-2xl border border-gray-200 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Name</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Email</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Role</th>
          <th class="text-left px-5 py-3 font-semibold text-gray-600">Department</th>
          <th class="text-center px-5 py-3 font-semibold text-gray-600">Drawer</th>
          <th class="text-center px-5 py-3 font-semibold text-gray-600">Status</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @forelse($users as $u)
        <tr class="hover:bg-gray-50/50 {{ !$u->is_active?'opacity-50':'' }}">
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <span class="text-blue-700 text-xs font-bold">{{ strtoupper(substr($u->name,0,2)) }}</span>
              </div>
              <p class="font-medium text-gray-800">{{ $u->name }}</p>
            </div>
          </td>
          <td class="px-5 py-3 text-gray-500">{{ $u->email }}</td>
          <td class="px-5 py-3">
            <span class="text-xs px-2.5 py-0.5 rounded-full font-medium
              {{ $u->role=='admin'?'bg-purple-100 text-purple-700':'bg-blue-100 text-blue-700' }}">
              {{ ucfirst($u->role) }}
            </span>
          </td>
          <td class="px-5 py-3 text-gray-500">{{ $u->department?->name ?? '—' }}</td>
          <td class="px-5 py-3 text-center">
            @if($u->role === 'pharmacist' && $u->drawer_number)
              <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-0.5 rounded-full font-semibold">
                🗄 Drawer {{ $u->drawer_number }}
              </span>
            @else
              <span class="text-gray-300 text-xs">—</span>
            @endif
          </td>
          <td class="px-5 py-3 text-center">
            <span class="text-xs px-2 py-0.5 rounded-full {{ $u->is_active?'bg-green-100 text-green-700':'bg-gray-100 text-gray-500' }}">
              {{ $u->is_active?'Active':'Inactive' }}
            </span>
          </td>
          <td class="px-5 py-3 text-right whitespace-nowrap">
            <a href="{{ route('users.edit',$u) }}" class="text-blue-600 hover:underline text-xs mr-3">Edit</a>
            @if($u->id !== auth()->id())
            <form action="{{ route('users.destroy',$u) }}" method="POST" class="inline"
              onsubmit="return confirm('Deactivate this user?')">
              @csrf @method('DELETE')
              <button class="text-red-500 hover:underline text-xs">Deactivate</button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400">No users found.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="px-5 py-4 border-t border-gray-100">{{ $users->links() }}</div>
  </div>
</div>
@endsection