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
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Kelas</label>
                            <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan Nama Kelas" required>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
