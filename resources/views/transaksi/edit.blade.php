<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Edit Transaksi</h1>
        <form action="/transaksi/{{ $transaksi->id }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="buku_tabungan_id">Buku Tabungan</label>
                <select name="buku_tabungan_id" id="buku_tabungan_id" class="form-control" required>
                    @foreach ($bukuTabungans as $bukuTabungan)
                    <option value="{{ $bukuTabungan->id }}" {{ $transaksi->buku_tabungan_id == $bukuTabungan->id ? 'selected' : '' }}>
                        {{ $bukuTabungan->nomor_urut }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
    <label for="jenis">Jenis</label>
    <select name="jenis" id="jenis" class="form-control" required>
        <option value="simpanan" {{ $transaksi->jenis == 'simpanan' ? 'selected' : '' }}>Simpanan</option>
        <option value="penarikan" {{ $transaksi->jenis == 'penarikan' ? 'selected' : '' }}>Penarikan</option>
        <option value="cicilan" {{ $transaksi->jenis == 'cicilan' ? 'selected' : '' }}>Cicilan</option>
    </select>
</div>
            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" step="0.01" value="{{ $transaksi->jumlah }}" required>
            </div>
            
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control">{{ $transaksi->keterangan }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>