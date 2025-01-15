@extends('layouts.layout')

@section('title', 'Edit Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tahun-ajaran.index') }}">Tahun Ajaran</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Tahun Ajaran</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Main Card -->
            <div class="card">
                <!-- Card Header -->
                <div class="card-header">
                    <h4 class="card-title">Edit Tahun Ajaran</h4>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Form -->
                    <form action="{{ route('tahun-ajaran.update', $tahunAjaran->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Error Messages -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Nama Tahun Ajaran Field -->
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Tahun Ajaran</label>
                            <input type="text" 
                                   name="nama" 
                                   id="nama" 
                                   class="form-control @error('nama') is-invalid @enderror" 
                                   placeholder="Masukkan Nama Tahun Ajaran"
                                   value="{{ old('nama', $tahunAjaran->nama) }}"
                                   required>
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="{{ route('tahun-ajaran.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
