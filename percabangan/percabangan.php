
<!DOCTYPE html>
<html>
<head>
    <title>Praktikum PHP Percabangan</title>
</head>
<body>

    <h1>Belajar Percabangan PHP</h1>

    <?php

    $nama = "Suta Wijaya";
    $nilai = 85;

    echo "Nama: " . $nama . "<br>";
    echo "Nilai: " . $nilai . "<br>";

    if ($nilai >= 90) {
        echo "Predikat: A";
    } elseif ($nilai >= 80) {
        echo "Predikat: B";
    } elseif ($nilai >= 70) {
        echo "Predikat: C";
    } else {
        echo "Predikat: D";
    }

    echo "<br>";

    if ($nilai >= 75) {
        echo "Keterangan: LULUS";
    } else {
        echo "Keterangan: TIDAK LULUS";
    }

    ?>

</body>
</html>
