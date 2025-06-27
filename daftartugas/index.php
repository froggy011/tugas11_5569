<?php
// error_reporting(E_ALL); // Aktifkan ini untuk melihat semua error selama pengembangan
// ini_set('display_errors', 1); // Aktifkan ini untuk melihat semua error di browser

include 'db_connect.php'; // PASTIKAN NAMA FILE INI BENAR: db_config.php

// Ambil data tugas dari database
$sql = "SELECT t.task_id, t.title, t.description, c.name AS category_name, t.status
        FROM tasks t
        LEFT JOIN categories c ON t.category_id = c.category_id
        ORDER BY t.task_id DESC"; // Urutkan berdasarkan tugas terbaru
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 900px;
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
        .actions-top {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
            gap: 10px; /* Jarak antar tombol */
        }
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }
        .btn-primary {
            background-color: #007bff; /* Biru */
        }
        .btn-info {
            background-color: #17a2b8; /* Biru muda/Cyan */
        }
        .btn-warning {
            background-color: #ffc107; /* Oranye/Kuning */
            color: #333; /* Warna teks lebih gelap agar terlihat */
        }
        .btn-success {
            background-color: #28a745; /* Hijau */
        }
        .btn-danger {
            background-color: #dc3545; /* Merah */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        .no-data-message {
            background-color: #e9f7fe;
            color: #007bff;
            padding: 10px;
            border: 1px solid #b3e0ff;
            border-radius: 5px;
            text-align: center;
            margin-top: 15px;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Tugas</h2>

        <?php
        // Menampilkan pesan sukses/error dari operasi CRUD
        if (isset($_GET['status'])) {
            echo "<div style='padding: 10px; margin-bottom: 15px; border-radius: 5px;";
            switch ($_GET['status']) {
                case 'success_add':
                    echo "background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;'>Tugas berhasil ditambahkan.</div>";
                    break;
                case 'success_edit':
                    echo "background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb;'>Tugas berhasil diperbarui.</div>";
                    break;
                case 'success_delete':
                    echo "background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'>Tugas berhasil dihapus.</div>";
                    break;
                case 'success_complete':
                    echo "background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;'>Status tugas berhasil diubah menjadi selesai.</div>";
                    break;
                case 'error_task_not_found':
                case 'error_no_id':
                case 'error_delete':
                case 'error_no_id_delete':
                    $errorMessage = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Terjadi kesalahan dalam operasi.';
                    echo "background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'>Error: " . $errorMessage . "</div>";
                    break;
            }
        }
        ?>

        <div class="actions-top">
            <a href="add_task.php" class="btn btn-primary">Tambah Tugas</a>
            <a href="manage_categories.php" class="btn btn-info">Kelola Kategori</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Penting: Reset pointer result set jika ingin mengulang atau ada logika lain di atas
                    // $result->data_seek(0); // Ini opsional, hanya jika Anda perlu mengulang $result sebelum ini
                    while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($row['status'] == 'completed'): ?>
                                    <button class="btn btn-warning" disabled style="opacity: 0.6; cursor: not-allowed;">Edit</button>
                                <?php else: ?>
                                    <a href="edit_task.php?id=<?php echo $row['task_id']; ?>" class="btn btn-warning">Edit</a>
                                <?php endif; ?>

                                <a href="delete_task.php?id=<?php echo $row['task_id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus tugas \'<?php echo htmlspecialchars($row['title']); ?>\'?');">Hapus</a>
                                   
                                <?php if ($row['status'] != 'completed'): ?>
                                    <a href="complete_task.php?id=<?php echo $row['task_id']; ?>" class="btn btn-success">Selesai</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data-message">
                Tidak ada tugas
            </div>
        <?php endif; ?>
    </div> <?php $conn->close(); ?>
</body>
</html>