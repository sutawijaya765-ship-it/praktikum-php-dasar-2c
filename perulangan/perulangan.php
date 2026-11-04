
<!DOCTYPE html>
<html>
<head>
    <title>Praktikum PHP Perulangan</title>
</head>
<body>

    <h1>Belajar Perulangan PHP</h1>

    <?php

    echo "<h3>1. Perulangan FOR</h3>";

    for ($i = 1; $i <= 5; $i++) {
        echo "Perulangan ke-" . $i . "<br>";
    }

    echo "<h3>2. Perulangan WHILE</h3>";

    $angka = 1;

    while ($angka <= 5) {
        echo "Angka: " . $angka . "<br>";
        $angka++;
    }

    echo "<h3>3. Perulangan FOREACH</h3>";

    $buah = ["Apel", "Jeruk", "Mangga", "Pisang"];

    foreach ($buah as $nama_buah) {
        echo "Buah: " . $nama_buah . "<br>";
    }

    ?>

</body>
</html>
