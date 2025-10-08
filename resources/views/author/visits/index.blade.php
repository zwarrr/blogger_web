@extends('layouts.admin')

@section('title', 'Daftar Kunjungan - Author')

@push('head')
<meta name="user-role" content="{{ auth()->user()->role }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
@include('author.visits.dynamic-table')
@include('visits.workflow-modals')
@endsection