<?php
include 'db_connect.php'; // Pastikan path ini benar

$message = ""; // Untuk menyimpan pesan sukses/error

// Bagian untuk MENAMBAH kategori baru
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['category_name']) && !empty(trim($_POST['category_name']))) {
        $category_name = trim($_POST['category_name']);

        // Mencegah SQL Injection
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            $message = "<div style='color: green; background-color: #d4edda; border: 1px solid #28a745; padding: 10px; margin-bottom: 15px; border-radius: 5px;'>Kategori '<b>" . htmlspecialchars($category_name) . "</b>' berhasil ditambahkan!</div>";
        } else {
            $message = "<div style='color: red; background-color: #f8d7da; border: 1px solid #dc3545; padding: 10px; margin-bottom: 15px; border-radius: 5px;'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        $message = "<div style='color: orange; background-color: #fff3cd; border: 1px solid #ffc107; padding: 10px; margin-bottom: 15px; border-radius: 5px;'>Nama kategori tidak boleh kosong.</div>";
    }
}

// Bagian untuk MENAMPILKAN daftar kategori
$sql_select = "SELECT category_id, name FROM categories ORDER BY category_id DESC";
$result = $conn->query($sql_select);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori</title>
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
        h2, h3 {
            color: #333;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #555;
        }
        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        form input[type="text"] {
            width: calc(100% - 22px); /* Full width minus padding and border */
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
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
        .no-data-message {
            background-color: #e9f7fe;
            color: #007bff;
            padding: 10px;
            border: 1px solid #b3e0ff;
            border-radius: 5px;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kelola Kategori</h2>

        <?php echo $message; // Menampilkan pesan sukses/error ?>

        <h3>Daftar Kategori</h3>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['category_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data-message">
                Belum ada kategori.
            </div>
        <?php endif; ?>

        <h3>Tambah Kategori Baru</h3>
        <form action="manage_categories.php" method="POST">
            <label for="category_name">Nama Kategori</label>
            <input type="text" id="category_name" name="category_name" placeholder="Masukkan nama kategori baru" required>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <?php $conn->close(); ?>
</body>
</html>