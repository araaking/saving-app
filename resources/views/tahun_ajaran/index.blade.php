@extends('layouts.layout')

@section('title', 'Kelola Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tahun Ajaran</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Main Card -->
            <div class="card">
                <!-- Card Header -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Tahun Ajaran</h4>
                    <a href="{{ route('tahun-ajaran.create') }}" 
                       class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Tahun Ajaran
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Data Table -->
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Tahun Ajaran</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tahunAjaran as $key => $tahun)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $tahun->nama }}</td>
                                    <td>
                                        <!-- Edit Button -->
                                        <a href="{{ route('tahun-ajaran.edit', $tahun->id) }}" 
                                           class="btn btn-warning btn-sm"
                                           title="Edit Tahun Ajaran">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Delete Form -->
                                        <form action="{{ route('tahun-ajaran.destroy', $tahun->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm"
                                                    title="Hapus Tahun Ajaran">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        Tidak ada data tahun ajaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
