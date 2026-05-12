@extends('errors.layout')

@section('title', 'Session Expired')
@section('code', '419')
@section('message', 'Terminal Timeout')
@section('description', "Your secure session has expired for your protection. Please refresh the page and re-authenticate to continue.")

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
</svg>
@endsection
