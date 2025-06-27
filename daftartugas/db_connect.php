<?php
// db_connect.php

$servername = "localhost"; // Ganti jika database di server lain
$username = "root";      // Ganti dengan username database Anda
$password = "";          // Ganti dengan password database Anda
$dbname = "tugas11";    // Nama database Anda

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset ke utf8mb4 jika diperlukan (untuk emoji dll.)
$conn->set_charset("utf8mb4");

// echo "Koneksi database berhasil!"; // Bisa dihapus setelah dipastikan koneksi berhasil
?>