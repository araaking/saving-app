@extends('layouts.layout')

@section('title', 'Tambah Tahun Ajaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-4">
                
                <div class="card-body">
                    <form action="{{ route('tahun-ajaran.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Tahun Ajaran</label>
                            <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan Nama Tahun Ajaran" required>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Tambah</button>
                            <a href="{{ route('tahun-ajaran.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
