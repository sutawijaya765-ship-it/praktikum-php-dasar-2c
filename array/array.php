
<!DOCTYPE html>
<html>
<head>
    <title>Praktikum PHP Array</title>
</head>
<body>

    <h1>Belajar Penggunaan Array PHP</h1>

    <?php

    // 1. Membuat Array
    $buah = ["Apel", "Jeruk", "Mangga", "Pisang"];

    echo "<h3>1. Menampilkan Semua Data Array</h3>";

    foreach ($buah as $nama_buah) {
        echo $nama_buah . "<br>";
    }

    // 2. Mengakses Array berdasarkan index
    echo "<h3>2. Mengakses Data Array</h3>";

    echo "Buah pertama: " . $buah[0] . "<br>";
    echo "Buah kedua: " . $buah[1] . "<br>";

    // 3. Menambahkan Data Array
    echo "<h3>3. Menambahkan Data Array</h3>";

    $buah[] = "Semangka";

    foreach ($buah as $nama_buah) {
        echo $nama_buah . "<br>";
    }

    // 4. Menghitung Jumlah Array
    echo "<h3>4. Jumlah Data Array</h3>";

    echo "Jumlah buah: " . count($buah);

    ?>

</body>
</html>
