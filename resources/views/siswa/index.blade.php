@extends('layouts.layout')

@section('title', 'Daftar Siswa')

@section('content')
<div class="container-fluid">
    <!-- Header / Breadcrumb -->
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        
    </div>

    <!-- Card Header with Filter & Search -->

    <!-- Card with Table for Daftar Siswa -->
    <div class="card overflow-hidden mt-4">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h5 class="card-title mb-0">Daftar Siswa</h5>
                <!-- Form Filter dan Search -->
                <form action="{{ route('siswa.index') }}" method="GET" class="ms-auto d-flex align-items-center gap-2">
                    <!-- Filter Kelas -->
                    <select name="kelas" class="form-select form-select-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($allKelas as $kelas)
                            <option value="{{ $kelas->nama }}" {{ request('kelas') == $kelas->nama ? 'selected' : '' }}>
                                {{ $kelas->nama }}
                            </option>
                        @endforeach
                    </select>
                    <!-- Search berdasarkan Nama Siswa -->
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Siswa..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                </form>
                <!-- Button Add Siswa -->
                <a href="{{ route('siswa.create') }}" class="btn btn-sm btn-success ms-2">Tambah Siswa</a>
            </div>
        </div>
        <div class="card-body mt-0">
            <div class="table-responsive table-card mt-0">
                <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                    <thead class="text-muted table-light">
                        <tr>
                            <th scope="col" class="cursor-pointer">ID</th>
                            <th scope="col" class="cursor-pointer">Nama Siswa</th>
                            <th scope="col" class="cursor-pointer">Kelas</th>
                            <th scope="col" class="cursor-pointer">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswas as $siswa)
                            <tr>
                                <td>{{ $siswa->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{ $siswa->nama }}
                                    </div>
                                </td>
                                <td>{{ $siswa->kelas->nama }}</td>
                                <td>
                                    <a href="{{ route('siswa.edit', $siswa->id) }}" class="btn btn-icon btn-sm bg-primary-subtle me-1" data-bs-toggle="tooltip" title="Edit">
                                        <i class="mdi mdi-pencil-outline fs-14 text-primary"></i>
                                    </a>
                                    <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-sm bg-danger-subtle" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')">
                                            <i class="mdi mdi-delete fs-14 text-danger"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3">Tidak ada data siswa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div><!-- end table-responsive -->
        </div><!-- end card-body -->
    </div><!-- end card -->
</div><!-- end container-fluid -->
@endsection
