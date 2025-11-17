@extends('layouts.app')

@section('title','Admin — Users')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Users</h1>
            <div class="text-sm text-slate-300">All registered users</div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded border border-slate-700">Dashboard</a>
            <a href="{{ route('admin.prices') }}" class="px-3 py-2 rounded bg-sky-600 text-slate-900 font-semibold">Edit prices</a>
        </div>
    </div>

    <div class="bg-slate-900 p-4 rounded-xl glass">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-slate-400 text-left">
                        <th class="py-2">ID</th>
                        <th class="py-2">Name</th>
                        <th class="py-2">Email</th>
                        <th class="py-2">Phone</th>
                        <th class="py-2">Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($users as $u)
                        <tr>
                            <td class="py-3">{{ $u->id }}</td>
                            <td class="py-3">{{ $u->name }}</td>
                            <td class="py-3">{{ $u->email }}</td>
                            <td class="py-3">@if($u->phone)<a href="https://wa.me/{{ $u->phone }}" target="_blank" class="underline">{{ $u->phone }}</a>@else—@endif</td>
                            <td class="py-3">@if($u->is_admin)<span class="px-2 py-1 rounded bg-cyan-400 text-slate-900">Yes</span>@else—@endif</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
