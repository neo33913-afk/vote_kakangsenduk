<?php
require_once __DIR__ . '/../config/app.php';
require_once ROOT_PATH . '/includes/klasemen.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Belum login.']);
    exit;
}
if (!is_admin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Akses ditolak.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method tidak diizinkan.']);
    exit;
}
session_write_close();

try {
    $s = $pdo->query(
        "SELECT
            (SELECT COUNT(*) FROM kontestan WHERE kategori = 'Kakang')   AS kontestan_kakang,
            (SELECT COUNT(*) FROM kontestan WHERE kategori = 'Senduk')   AS kontestan_senduk,
            (SELECT COUNT(*) FROM users WHERE role = 'pemilih')          AS total_pemilih,
            (SELECT COUNT(DISTINCT user_id) FROM voting)                 AS pemilih_sudah_memilih,
            (SELECT COUNT(*) FROM voting WHERE kategori = 'Kakang')      AS suara_kakang,
            (SELECT COUNT(*) FROM voting WHERE kategori = 'Senduk')      AS suara_senduk"
    )->fetch();

    $periode = $pdo->query(
        "SELECT id, nama_periode, tanggal_mulai, tanggal_selesai, status
         FROM periode_voting WHERE status = 'Aktif' ORDER BY id DESC LIMIT 1"
    )->fetch() ?: null;

    $totalPemilih = (int) $s['total_pemilih'];
    $sudah        = (int) $s['pemilih_sudah_memilih'];

    // Data grafik: suara per kontestan, dipisah per kategori
    $grafik = [];
    foreach (KATEGORI_VALID as $kat) {
        $k = ambil_klasemen($pdo, $kat);
        $grafik[$kat] = array_map(fn($r) => [
            'nomor_peserta' => $r['nomor_peserta'],
            'nama'          => $r['nama'],
            'suara'         => $r['suara'],
        ], $k['data']);
    }

    echo json_encode([
        'kontestan' => [
            'total'  => (int) $s['kontestan_kakang'] + (int) $s['kontestan_senduk'],
            'kakang' => (int) $s['kontestan_kakang'],
            'senduk' => (int) $s['kontestan_senduk'],
        ],
        'pemilih' => [
            'total'        => $totalPemilih,
            'sudah_memilih' => $sudah,
            'partisipasi'  => $totalPemilih > 0 ? round($sudah / $totalPemilih * 100, 1) : 0,
        ],
        'suara' => [
            'total'  => (int) $s['suara_kakang'] + (int) $s['suara_senduk'],
            'kakang' => (int) $s['suara_kakang'],
            'senduk' => (int) $s['suara_senduk'],
        ],
        'periode' => [
            'aktif'          => $periode,                        // null jika tidak ada yang berstatus Aktif
            'voting_terbuka' => periode_aktif($pdo) !== null,    // Aktif DAN waktu sekarang di dalam rentang
        ],
        'grafik'     => $grafik,
        'diperbarui' => date('d-m-Y H:i:s'),
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mengambil statistik.']);
}