@extends('errors.layout')

@section('title', 'Server Error')
@section('code', '500')
@section('message', 'System Hiccup')
@section('description', "Something went wrong on our end. Our technicians have been notified. Try refreshing the page or returning to the dashboard.")

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
</svg>
@endsection
