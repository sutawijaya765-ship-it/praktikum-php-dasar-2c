
<!DOCTYPE html>
<html>
<head>
    <title>Hasil POST dan GET</title>
</head>
<body>

    <h1>Hasil Pengiriman Data</h1>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $nama = htmlspecialchars($_GET["nama"] ?? "");

        echo "Metode: GET<br>";
        echo "Nama: " . $nama;

    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {

        $nama = htmlspecialchars($_POST["nama"] ?? "");

        echo "Metode: POST<br>";
        echo "Nama: " . $nama;

    }

    ?>

    <br><br>
    <a href="index.php">Kembali ke Formulir</a>

</body>
</html>
