<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kelas</title>
</head>
<body>
    <h1>Tambah Kelas</h1>
    <form action="{{ route('kelas.store') }}" method="POST">
        @csrf
        <label for="nama">Nama Kelas:</label>
        <input type="text" id="nama" name="nama" required>
        <button type="submit">Simpan</button>
    </form>
</body>
</html>