<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa</title>
</head>
<body>
    <h1>Edit Siswa</h1>
    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="nama">Nama Siswa:</label>
        <input type="text" id="nama" name="nama" value="{{ $siswa->nama }}" required>
        <br>
        <label for="kelas_id">Kelas:</label>
        <select id="kelas_id" name="kelas_id" required>
            @foreach ($kelas as $item)
                <option value="{{ $item->id }}" {{ $siswa->kelas_id == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
            @endforeach
        </select>
        <br>
        <button type="submit">Perbarui</button>
    </form>
</body>
</html>