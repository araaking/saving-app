<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>
</head>
<body>
    <h1>Tambah Siswa</h1>
    <form action="{{ route('siswa.store') }}" method="POST">
        @csrf
        <label for="nama">Nama Siswa:</label>
        <input type="text" id="nama" name="nama" required>
        <br>
        <label for="kelas_id">Kelas:</label>
        <select id="kelas_id" name="kelas_id" required>
            @foreach ($kelas as $item)
                <option value="{{ $item->id }}">{{ $item->nama }}</option>
            @endforeach
        </select>
        <br>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>