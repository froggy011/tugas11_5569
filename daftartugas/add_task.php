<?php
include 'db_connect.php'; // Pastikan path ini benar

$message = ""; // Untuk menyimpan pesan sukses/error

// Ambil daftar kategori dari database untuk dropdown
$categories_sql = "SELECT category_id, name FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_sql);
$categories = [];
if ($categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Bagian untuk MENAMBAH tugas baru
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = $_POST['category'] ?? null; // Bisa null jika tidak ada kategori dipilih
    $status = $_POST['status'] ?? 'pending'; // Default: pending

    if (empty($title)) {
        $message = "<div style='color: orange; background-color: #fff3cd; border: 1px solid #ffc107; padding: 10px; margin-bottom: 15px; border-radius: 5px;'>Judul tugas tidak boleh kosong.</div>";
    } else {
        // Mencegah SQL Injection
        $stmt = $conn->prepare("INSERT INTO tasks (title, description, category_id, status) VALUES (?, ?, ?, ?)");
        // 'ssis' => string, string, integer, string
        $stmt->bind_param("ssis", $title, $description, $category_id, $status);

        if ($stmt->execute()) {
            // Setelah berhasil, arahkan kembali ke halaman utama
            header("Location: index.php?status=success_add");
            exit();
        } else {
            $message = "<div style='color: red; background-color: #f8d7da; border: 1px solid #dc3545; padding: 10px; margin-bottom: 15px; border-radius: 5px;'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tugas Baru</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input[type="text"],
        form textarea,
        form select {
            width: calc(100% - 22px); /* Full width minus padding and border */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
        }
        form textarea {
            resize: vertical; /* Allow vertical resizing */
            min-height: 80px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
            margin-right: 10px;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tambah Tugas Baru</h2>

        <?php echo $message; // Menampilkan pesan sukses/error ?>

        <form action="add_task.php" method="POST">
            <label for="title">Judul</label>
            <input type="text" id="title" name="title" placeholder="Contoh: Tugas Pemrograman Web" required>

            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" placeholder="Contoh: Bikin CRUD BANG???" rows="4"></textarea>

            <label for="category">Kategori</label>
            <select id="category" name="category">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['category_id']); ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="pending">Pending</option>
                <option value="onprogress">Sedang Dikerjakan</option>
                <option value="completed">Selesai</option>
            </select>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <?php $conn->close(); ?>
</body>
</html>