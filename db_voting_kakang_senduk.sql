-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Nov 2026 pada 07.22
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_voting_kakang_senduk`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kontestan`
--

CREATE TABLE `kontestan` (
  `id` int(11) NOT NULL,
  `nomor_peserta` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kategori` enum('Kakang','Senduk') NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `asal_instansi` varchar(150) NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `visi` text DEFAULT NULL,
  `misi` text DEFAULT NULL,
  `prestasi` text DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kontestan`
--

INSERT INTO `kontestan` (`id`, `nomor_peserta`, `nama`, `kategori`, `foto`, `asal_instansi`, `tempat_lahir`, `tanggal_lahir`, `visi`, `misi`, `prestasi`, `deskripsi`, `created_at`) VALUES
(1, '01', 'Kontestan Kakang 1', 'Kakang', 'a0d74ec6021879305cef5b96d91cbabe.jpg', 'SMKN 1 Ponorogo', NULL, NULL, NULL, NULL, NULL, 'Data simulasi', '2026-11-12 10:55:18'),
(2, '02', 'Kontestan Kakang 2', 'Kakang', NULL, 'SMKN 2 Ponorogo', NULL, NULL, NULL, NULL, NULL, 'Data simulasi', '2026-11-12 10:55:18'),
(3, '01', 'Kontestan Senduk 1', 'Senduk', NULL, 'SMAN 1 Ponorogo', NULL, NULL, NULL, NULL, NULL, 'Data simulasi', '2026-11-12 10:55:18'),
(4, '02', 'Kontestan Senduk 2', 'Senduk', NULL, 'SMAN 2 Ponorogo', NULL, NULL, NULL, NULL, NULL, 'Data simulasi', '2026-11-12 10:55:18'),
(10, '03', 'Amfidza neo danara', 'Kakang', 'edcc6c84952e90c7c97917fa337a4d9a.jpg', 'SMK PGRI 2 PONOROGO', 'Ponorogo', '2009-01-01', 'Hidup indonesia', 'Menjadi tutor de em 3 di modern warship ke pada manusia poke poke di luar sana', 'S17 DM 3\r\nS18 DM 3\r\nS19 DM 3\r\nS20 DM 3', '(yntkts)Yondak tahu kok tanya saya', '2026-11-12 13:07:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `periode_voting`
--

CREATE TABLE `periode_voting` (
  `id` int(11) NOT NULL,
  `nama_periode` varchar(100) NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `status` enum('Aktif','Nonaktif') NOT NULL DEFAULT 'Nonaktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `periode_voting`
--

INSERT INTO `periode_voting` (`id`, `nama_periode`, `tanggal_mulai`, `tanggal_selesai`, `status`) VALUES
(2, 'Contoh Periode', '2026-11-12 12:36:09', '2026-11-19 12:36:09', 'Aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pemilih') NOT NULL DEFAULT 'pemilih',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `no_hp`, `password`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@contoh.com', '-', '$2y$10$ckHjJh0PXMvqfmCr0gdB1Od12vuJVrCgqEg8cNBPj3R2odVMi1vQm', 'admin', '2026-11-12 10:51:35'),
(5, '03.Amfidza neo danara', 'neo33913@gmail.com', '+6285784691260', '$2y$10$kaxJAvjhVieFl4HWrXAjIOGIKJoAoXTDQb5bx7Wpxz2HtMJzjcA5m', 'pemilih', '2026-11-12 12:41:41'),
(6, 'Administrator', 'admin123@gmail.com', '-', '$2y$10$Bo38YgtjnlJHvARW8IbzHe.jdxZzGxouHipNNWOx8sV3SY5Tn8VKi', 'admin', '2026-11-12 12:45:32'),
(7, 'neo lagi', 'neo123@gmail.com', '0812345678', '$2y$10$AkRH8H4O.fvJJp.h2LWDZ.5ZJtaZtzVAqcRUCJ7lZDXkKaxIlICSW', 'pemilih', '2026-11-12 13:17:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `voting`
--

CREATE TABLE `voting` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `kontestan_id` int(11) NOT NULL,
  `kategori` enum('Kakang','Senduk') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `voting`
--

INSERT INTO `voting` (`id`, `user_id`, `kontestan_id`, `kategori`, `created_at`) VALUES
(12, 5, 2, 'Kakang', '2026-11-12 12:42:16'),
(13, 7, 10, 'Kakang', '2026-11-12 13:17:58');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `kontestan`
--
ALTER TABLE `kontestan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_kontestan_nomor` (`kategori`,`nomor_peserta`);

--
-- Indeks untuk tabel `periode_voting`
--
ALTER TABLE `periode_voting`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- Indeks untuk tabel `voting`
--
ALTER TABLE `voting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_kategori` (`user_id`,`kategori`),
  ADD KEY `idx_voting_kontestan` (`kontestan_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kontestan`
--
ALTER TABLE `kontestan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `periode_voting`
--
ALTER TABLE `periode_voting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `voting`
--
ALTER TABLE `voting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `voting`
--
ALTER TABLE `voting`
  ADD CONSTRAINT `fk_voting_kontestan` FOREIGN KEY (`kontestan_id`) REFERENCES `kontestan` (`id`),
  ADD CONSTRAINT `fk_voting_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
