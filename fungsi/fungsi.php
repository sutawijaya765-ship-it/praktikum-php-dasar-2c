
<!DOCTYPE html>
<html>
<head>
    <title>Praktikum PHP Fungsi</title>
</head>
<body>

    <h1>Belajar Fungsi dalam PHP</h1>

    <?php

    // 1. Fungsi sederhana
    function salam() {
        echo "Halo, selamat datang di praktikum PHP!";
    }

    echo "<h3>1. Fungsi Sederhana</h3>";
    salam();

    // 2. Fungsi dengan parameter
    function perkenalan($nama) {
        echo "<br>Nama saya adalah " . $nama;
    }

    echo "<h3>2. Fungsi dengan Parameter</h3>";
    perkenalan("Suta Wijaya");

    // 3. Fungsi dengan nilai kembali (return)
    function penjumlahan($a, $b) {
        return $a + $b;
    }

    echo "<h3>3. Fungsi dengan Return</h3>";
    echo "Hasil penjumlahan: " . penjumlahan(10, 5);

    // 4. Fungsi menghitung luas persegi panjang
    function luasPersegiPanjang($panjang, $lebar) {
        return $panjang * $lebar;
    }

    echo "<h3>4. Menghitung Luas Persegi Panjang</h3>";

    $panjang = 10;
    $lebar = 5;

    echo "Panjang: " . $panjang . " cm<br>";
    echo "Lebar: " . $lebar . " cm<br>";
    echo "Luas: " . luasPersegiPanjang($panjang, $lebar) . " cm persegi";

    ?>

</body>
</html>
