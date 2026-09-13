@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan — 404 Not Found')
@section('code', '404 — Not Found')

@section('icon')
    <i class="fa-solid fa-compass text-2xl sm:text-3xl text-[#B89B5E]"></i>
@endsection

@section('headline', 'Data atau Halaman Tidak Ditemukan')

@section('message')
    Halaman, data prospek, atau unit properti yang Anda cari tidak tersedia, telah dihapus, atau tautan yang Anda gunakan salah.
@endsection
