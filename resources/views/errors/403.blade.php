@extends('errors.layout')

@section('title', 'Akses Dilarang')

@section('icon')
    <i class="bi bi-shield-lock-fill text-danger"></i>
@endsection

@section('code', '403')

@section('message', 'Akses Dilarang')

@section('description', 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika
    Anda merasa ini adalah kesalahan.')
