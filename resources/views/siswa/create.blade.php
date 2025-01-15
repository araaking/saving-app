@extends('layouts.layout')

@section('title', 'Tambah Siswa')

@section('content')

<div class="container-fluid">
    <!-- Header & Breadcrumb -->
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Pages</a>
                </li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <!-- Form Tambah Siswa -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tambah Siswa</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('siswa.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Siswa:</label>
                            <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan nama siswa" required>
                        </div>
                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS:</label>
                            <input type="text" id="nis" name="nis" class="form-control" placeholder="Masukkan NIS (opsional)" maxlength="20">
                        </div>
                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas:</label>
                            <select id="kelas_id" name="kelas_id" class="form-select" required>
                                <option value="">Pilih Kelas</option>
                                @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                           <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
