@extends('layouts.layout')

@section('title', 'Tambah Kelas')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">Tambah Kelas</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('kelas.store') }}" method="POST">
                        @csrf

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

                        <!-- Nama Kelas -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Contoh: A (tanpa tingkat)" 
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tingkat -->
                        <div class="mb-3">
                            <label for="tingkat" class="form-label">Tingkat <span class="text-danger">*</span></label>
                            <input type="number" 
                                   id="tingkat" 
                                   name="tingkat" 
                                   class="form-control @error('tingkat') is-invalid @enderror" 
                                   placeholder="Contoh: 1"
                                   value="{{ old('tingkat') }}"
                                   min="1"
                                   required>
                            @error('tingkat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kelas Berikutnya -->
                        <div class="mb-3">
                            <label for="next_class_id" class="form-label">Kelas Berikutnya (Opsional)</label>
                            <select id="next_class_id" name="next_class_id" 
                                    class="form-select @error('next_class_id') is-invalid @enderror">
                                <option value="">Tidak Ada</option>
                                @foreach ($kelas->sortBy('tingkat') as $kelasOption)
                                    <option value="{{ $kelasOption->id }}" {{ old('next_class_id') == $kelasOption->id ? 'selected' : '' }}>
                                        {{ $kelasOption->name }} (Tingkat {{ $kelasOption->tingkat }})
                                    </option>
                                @endforeach
                            </select>
                            @error('next_class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('kelas.index') }}" class="btn btn-secondary">
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