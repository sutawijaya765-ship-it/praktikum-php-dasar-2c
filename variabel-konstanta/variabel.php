
<!DOCTYPE html>
<html>
<head>
    <title>Praktikum PHP Dasar 1.3</title>
</head>
<body>

    <h1>Variabel, Konstanta, dan Tipe Data PHP</h1>

    <?php

    // Variabel
    $nama = "Suta Wijaya";
    $umur = 21;
    $tinggi = 171;
    $mahasiswa = true;

    // Konstanta
    define("KAMPUS", "Universitas");

    // Menampilkan data
    echo "Nama: " . $nama . "<br>";
    echo "Umur: " . $umur . " tahun<br>";
    echo "Tinggi: " . $tinggi . " cm<br>";
    echo "Status Mahasiswa: " . ($mahasiswa ? "Aktif" : "Tidak Aktif") . "<br>";
    echo "Kampus: " . KAMPUS . "<br>";

    // Menampilkan tipe data
    echo "<br><b>Tipe Data:</b><br>";
    echo "Nama: " . gettype($nama) . "<br>";
    echo "Umur: " . gettype($umur) . "<br>";
    echo "Tinggi: " . gettype($tinggi) . "<br>";
    echo "Mahasiswa: " . gettype($mahasiswa) . "<br>";

    ?>

</body>
</html>
