<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku Tabungan</title>
</head>
<body>
    <h1>Tambah Buku Tabungan</h1>
    <form action="{{ route('buku_tabungan.store') }}" method="POST">
        @csrf
        <label for="siswa_id">Siswa:</label>
        <select id="siswa_id" name="siswa_id" required>
            @foreach ($siswas as $siswa)
                <option value="{{ $siswa->id }}">{{ $siswa->nama }}</option>
            @endforeach
        </select>
        <br>
        <label for="tahun_ajaran_id">Tahun Ajaran:</label>
        <select id="tahun_ajaran_id" name="tahun_ajaran_id" required>
            @foreach ($tahunAjarans as $tahunAjaran)
                <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->nama }}</option>
            @endforeach
        </select>
        <br>
        <label for="nomor_urut">Nomor Urut:</label>
        <input type="number" id="nomor_urut" name="nomor_urut" required>
        <br>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>