<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit('Not found. Seeder hanya dapat dijalankan melalui CLI.');
}

require_once __DIR__ . '/../config/database.php';

$pdo = db();
$categories = ['Pemrograman', 'Novel', 'Sains', 'Sejarah', 'Bisnis', 'Pendidikan', 'Seni & Desain', 'Anak'];
$authors = ['Nara Wisesa', 'Dimas Arunika', 'Sinta Maheswari', 'Raka Pradipta', 'Mira Kalandra', 'Banyu Satya', 'Tara Anindya', 'Reno Baskara', 'Alya Puspita', 'Galih Pramana'];
$publishers = ['Pustaka Awan', 'Ruang Baca Press', 'Kertas Biru', 'Lentera Nusa', 'Cakrawala Media'];
$titles = ['Merancang Masa Depan', 'Jejak di Ujung Kota', 'Rahasia Hutan Hujan', 'Catatan dari Esok Hari', 'Atlas Pikiran', 'Langkah Kecil', 'Arsitektur Cerita', 'Menjelajah Cakrawala', 'Laboratorium Imajinasi', 'Sebuah Hari yang Baru'];
$topics = ['Teknologi', 'Kehidupan', 'Pengetahuan', 'Perjalanan', 'Kreativitas'];
$memberNames = ['Adira Senja','Bima Naratama','Citra Purnama','Dara Kirana','Elang Prakoso','Fira Daneswari','Gema Wicaksana','Hana Lazuardi','Indra Samudra','Jihan Arunika','Kavi Mahendra','Laras Pertiwi','Miko Pradana','Naya Kinasih','Oka Wiratama','Prita Andalas','Qinan Baskara','Rani Cendana','Saka Wibowo','Talia Mentari','Umar Prasetya','Vina Kalandra','Wira Kusuma','Yara Pramesti','Zaki Nirmala'];
$addresses = ['Jalan Melati 12, Kota Contoh','Jalan Kenanga 8, Kota Fiktif','Jalan Angkasa 4, Kota Harapan','Jalan Pustaka 21, Kota Sejahtera','Jalan Pelangi 7, Kota Mandiri'];

// Migrasi tambahan yang aman untuk database yang sudah digunakan.
$hasSeedKey = $pdo->query("SHOW COLUMNS FROM borrowings LIKE 'seed_key'")->fetch();
if (!$hasSeedKey) $pdo->exec('ALTER TABLE borrowings ADD COLUMN seed_key VARCHAR(40) NULL UNIQUE');

$coverDir = __DIR__ . '/../assets/images/book-covers';
if (!is_dir($coverDir) && !mkdir($coverDir, 0775, true) && !is_dir($coverDir)) throw new RuntimeException('Folder sampul tidak dapat dibuat.');
$pdo->beginTransaction();
try {
    $getCategory = $pdo->prepare('SELECT id FROM categories WHERE name=?');
    $insertCategory = $pdo->prepare('INSERT IGNORE INTO categories(name) VALUES(?)');
    $categoryIds = [];
    foreach ($categories as $name) { $insertCategory->execute([$name]); $getCategory->execute([$name]); $categoryIds[] = (int)$getCategory->fetchColumn(); }

    $insertMember = $pdo->prepare('INSERT IGNORE INTO members(member_no,name,email,phone,address) VALUES(?,?,?,?,?)');
    foreach ($memberNames as $i => $name) $insertMember->execute([sprintf('DEMO-%03d', $i + 1), $name, sprintf('anggota%02d@example.test', $i + 1), sprintf('0800000%05d', $i + 1), $addresses[$i % count($addresses)]]);

    $insertBook = $pdo->prepare('INSERT IGNORE INTO books(title,author,publisher,publish_year,isbn,category_id,stock,description,cover) VALUES(?,?,?,?,?,?,?,?,?)');
    $findBook = $pdo->prepare('SELECT id,cover FROM books WHERE isbn=?');
    for ($i = 0; $i < 50; $i++) {
        $isbn = sprintf('978616900%04d', $i + 1);
        $title = $titles[$i % count($titles)] . ' — ' . $topics[intdiv($i, count($titles)) % count($topics)] . ' ' . sprintf('%02d', $i + 1);
        $author = $authors[$i % count($authors)];
        $publisher = $publishers[$i % count($publishers)];
        $year = 2017 + ($i % 10);
        $stock = 6 + ($i % 9);
        $description = 'Buku simulasi tentang ' . strtolower($topics[$i % count($topics)]) . ', disusun untuk melengkapi koleksi demo perpustakaan.';
        $cover = 'assets/images/book-covers/' . $isbn . '.svg';
        $insertBook->execute([$title, $author, $publisher, $year, $isbn, $categoryIds[$i % count($categoryIds)], $stock, $description, $cover]);
        $findBook->execute([$isbn]); $book = $findBook->fetch();
        $palette = ['#245b8f','#487c70','#8b5d86','#ba7a3c','#52679b']; $color = $palette[$i % count($palette)];
        $safeTitle = htmlspecialchars($title, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $safeAuthor = htmlspecialchars($author, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 420 600"><rect width="420" height="600" fill="'.$color.'"/><path d="M0 430Q180 340 420 455V600H0Z" fill="#102b4e" opacity=".38"/><circle cx="340" cy="95" r="52" fill="#fff" opacity=".15"/><text x="34" y="90" fill="#fff" font-family="Arial" font-size="17" letter-spacing="4">MINI LIBRARY</text><foreignObject x="34" y="190" width="350" height="230"><div xmlns="http://www.w3.org/1999/xhtml" style="font:bold 34px Arial;color:white;line-height:1.2">'.$safeTitle.'</div></foreignObject><text x="34" y="520" fill="#fff" font-family="Arial" font-size="20">'.$safeAuthor.'</text></svg>';
        file_put_contents($coverDir . '/' . $isbn . '.svg', $svg);
        if ($book && !$book['cover']) $pdo->prepare('UPDATE books SET cover=? WHERE id=?')->execute([$cover, $book['id']]);
    }

    $member = $pdo->prepare('SELECT id FROM members WHERE member_no=?');
    $bookByIsbn = $pdo->prepare('SELECT id,stock FROM books WHERE isbn=? FOR UPDATE');
    $insertLoan = $pdo->prepare('INSERT IGNORE INTO borrowings(member_id,borrow_date,due_date,return_date,status,created_by,seed_key) VALUES(?,?,?,?,?,NULL,?)');
    $insertDetail = $pdo->prepare('INSERT INTO borrowing_details(borrowing_id,book_id,quantity) VALUES(?,?,1)');
    $reduceStock = $pdo->prepare('UPDATE books SET stock=stock-1 WHERE id=? AND stock>0');
    for ($i = 0; $i < 12; $i++) {
        $seedKey = sprintf('demo-loan-%02d', $i + 1);
        $exists = $pdo->prepare('SELECT id FROM borrowings WHERE seed_key=?'); $exists->execute([$seedKey]);
        if ($exists->fetchColumn()) continue;
        $member->execute([sprintf('DEMO-%03d', $i + 1)]);
        $bookByIsbn->execute([sprintf('978616900%04d', $i + 1)]); $book = $bookByIsbn->fetch();
        $isActive = $i < 6;
        $borrowDate = date('Y-m-d', strtotime('-' . (15 + $i) . ' days'));
        $dueDate = date('Y-m-d', strtotime($isActive ? ($i < 3 ? '-2 days' : '+5 days') : '-8 days'));
        $returnDate = $isActive ? null : date('Y-m-d', strtotime('-' . (3 + $i) . ' days'));
        $status = $isActive ? 'dipinjam' : 'dikembalikan';
        if ($isActive && (!$book || (int)$book['stock'] < 1)) throw new RuntimeException('Stok tidak cukup untuk transaksi demo.');
        $insertLoan->execute([(int)$member->fetchColumn(), $borrowDate, $dueDate, $returnDate, $status, $seedKey]);
        $loanId = (int)$pdo->lastInsertId();
        if (!$loanId) continue;
        $insertDetail->execute([$loanId, (int)$book['id']]);
        if ($isActive) $reduceStock->execute([(int)$book['id']]);
    }
    $pdo->commit();
    echo "Seeder selesai: 8 kategori demo, 50 buku demo, 25 anggota demo, 12 transaksi demo. Menjalankan ulang tidak menggandakan data atau menghapus admin.\n";
} catch (Throwable $e) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $e; }
