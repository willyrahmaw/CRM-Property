@extends('errors.layout')

@section('title', 'Pemeliharaan Sistem — 503 Maintenance')
@section('code', '503 — Maintenance')

@section('icon')
    <i class="fa-solid fa-screwdriver-wrench text-2xl sm:text-3xl text-[#B89B5E]"></i>
@endsection

@section('headline', 'Sistem Sedang Dalam Pemeliharaan Rutin')

@section('message')
    Kami sedang melakukan peningkatan performa dan pemeliharaan server berkala. Sistem PROPFlow akan segera kembali online dalam beberapa menit. Terima kasih atas pengertian Anda.
@endsection
