
<!DOCTYPE html>
<html>
<head>
    <title>Praktikum PHP Dasar 1.4</title>
</head>
<body>

    <h1>Operator dalam PHP</h1>

    <?php
    $a = 20;
    $b = 5;

    echo "<h3>1. Operator Aritmatika</h3>";
    echo "Penjumlahan: " . ($a + $b) . "<br>";
    echo "Pengurangan: " . ($a - $b) . "<br>";
    echo "Perkalian: " . ($a * $b) . "<br>";
    echo "Pembagian: " . ($a / $b) . "<br>";
    echo "Modulus: " . ($a % $b) . "<br>";

    echo "<h3>2. Operator Perbandingan</h3>";
    echo "Apakah a lebih besar dari b? ";
    var_dump($a > $b);

    echo "<br>Apakah a sama dengan b? ";
    var_dump($a == $b);

    echo "<h3>3. Operator Logika</h3>";

    $x = true;
    $y = false;

    echo "AND: ";
    var_dump($x && $y);

    echo "<br>OR: ";
    var_dump($x || $y);

    echo "<br>NOT: ";
    var_dump(!$x);
    ?>

</body>
</html>
