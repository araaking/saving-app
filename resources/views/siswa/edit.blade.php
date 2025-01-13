@extends('layouts.layout')

@section('title', 'Edit Siswa')

@section('content')
<div class="container-fluid">
    <!-- Header dan Breadcrumb -->
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Dashboard</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item">
                    <a href="javascript: void(0);">Pages</a>
                </li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <!-- Konten Utama: Edit Siswa -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Siswa</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Siswa:</label>
                                    <input type="text" id="nama" name="nama" class="form-control" value="{{ $siswa->nama }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="kelas_id" class="form-label">Kelas:</label>
                                    <select id="kelas_id" name="kelas_id" class="form-select" required>
                                        <option value="">Pilih Kelas</option>
                                        @foreach ($kelas as $item)
                                            <option value="{{ $item->id }}" {{ $siswa->kelas_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Perbarui</button>
                                </div>
                            </form>
                        </div> <!-- End col-lg-6 -->
                    </div> <!-- End row -->
                </div> <!-- End card-body -->
            </div> <!-- End card -->
        </div> <!-- End col-12 -->
    </div> <!-- End row -->
</div> <!-- End container-fluid -->
@endsection
