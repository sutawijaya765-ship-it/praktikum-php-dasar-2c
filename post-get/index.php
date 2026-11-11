
<!DOCTYPE html>
<html>
<head>
    <title>Praktikum PHP POST dan GET</title>
</head>
<body>

    <h1>Formulir POST dan GET</h1>

    <h3>1. Metode GET</h3>

    <form action="proses.php" method="GET">
        Nama:
        <input type="text" name="nama">
        <button type="submit">Kirim GET</button>
    </form>

    <h3>2. Metode POST</h3>

    <form action="proses.php" method="POST">
        Nama:
        <input type="text" name="nama">
        <button type="submit">Kirim POST</button>
    </form>

</body>
</html>
