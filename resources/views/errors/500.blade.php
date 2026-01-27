@extends('errors.layout')

@section('title', 'Kesalahan Server')

@section('icon')
    <i class="bi bi-exclamation-triangle-fill text-warning"></i>
@endsection

@section('code', '500')

@section('message', 'Kesalahan Sistem')

@section('description', 'Ups! Terjadi kesalahan pada server kami. Kami sedang berusaha memperbaikinya sesegera mungkin.
    Mohon tunggu beberapa saat.')
