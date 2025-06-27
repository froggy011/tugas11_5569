<?php
include 'db_connect.php'; // Pastikan path ini benar

$message = ""; // Untuk menyimpan pesan sukses/error
$task_id = null;
$task = null;
$categories = []; // Untuk dropdown kategori

// Ambil daftar kategori dari database untuk dropdown
$categories_sql = "SELECT category_id, name FROM categories ORDER BY name ASC";
$categories_result = $conn->query($categories_sql);
if ($categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Bagian untuk mengambil data tugas yang akan diedit
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $task_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT task_id, title, description, category_id, status FROM tasks WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        // Jika tugas tidak ditemukan, arahkan kembali ke index.php
        header("Location: index.php?status=error_task_not_found");
        exit();
    }
    $stmt->close();
} else {
    // Jika tidak ada ID tugas, arahkan kembali ke index.php
    header("Location: index.php?status=error_no_id");
    exit();
}

// Bagian untuk MEMPERBARUI (UPDATE) tugas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = $_POST['task_id']; // Ambil ID tugas dari form hidden input
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = $_POST['category'] ?? null;
    $status = $_POST['status'] ?? 'pending';

    if (empty($title)) {
        $message = "<div style='color: orange; background-color: #fff3cd; border: 1px solid #ffc107; padding: 10px; margin-bottom: 15px; border-radius: 5px;'>Judul tugas tidak boleh kosong.</div>";
    } else {
        // Menggunakan prepared statement untuk UPDATE
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, category_id = ?, status = ? WHERE task_id = ?");
        // 'ssisi' => string, string, integer, string, integer
        $stmt->bind_param("ssisi", $title, $description, $category_id, $status, $task_id);

        if ($stmt->execute()) {
            // Setelah berhasil, arahkan kembali ke halaman utama
            header("Location: index.php?status=success_edit");
            exit();
        } else {
            $message = "<div style='color: red; background-color: #f8d7da; border: 1px solid #dc3545; padding: 10px; margin-bottom: 15px; border-radius: 5px;'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Jika $task masih null setelah mencoba mengambil dari GET (misalnya, ID tidak valid),
// maka kita tidak bisa menampilkan form, jadi redirect saja.
if ($task === null) {
    header("Location: index.php?status=error_task_not_found_final");
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas</title>
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
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        form textarea {
            resize: vertical;
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
        <h2>Edit Tugas</h2>

        <?php echo $message; // Menampilkan pesan sukses/error ?>

        <form action="edit_task.php" method="POST">
            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['task_id']); ?>">

            <label for="title">Judul</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>

            <label for="description">Deskripsi</label>
            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>

            <label for="category">Kategori</label>
            <select id="category" name="category">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['category_id']); ?>"
                        <?php echo ($task['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="pending" <?php echo ($task['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="onprogress" <?php echo ($task['status'] == 'onprogress') ? 'selected' : ''; ?>>Sedang Dikerjakan</option>
                <option value="completed" <?php echo ($task['status'] == 'completed') ? 'selected' : ''; ?>>Selesai</option>
            </select>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <?php $conn->close(); ?>
</body>
</html>