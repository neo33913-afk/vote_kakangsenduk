<?php
const KATEGORI_VALID = ['Kakang', 'Senduk'];

/**
 * Klasemen satu kategori, urut dari suara terbanyak.
 * Suara sama = ranking sama (contoh: 1, 1, 3).
 * Jika belum ada suara sama sekali, rank bernilai null.
 */
function ambil_klasemen(PDO $pdo, string $kategori): array
{
    if (!in_array($kategori, KATEGORI_VALID, true)) {
        throw new InvalidArgumentException('Kategori tidak valid.');
    }

    $stmt = $pdo->prepare(
        "SELECT k.id, k.nomor_peserta, k.nama, k.foto, k.asal_instansi,
                COUNT(v.id) AS total_suara
         FROM kontestan k
         LEFT JOIN voting v ON v.kontestan_id = k.id
         WHERE k.kategori = ?
         GROUP BY k.id, k.nomor_peserta, k.nama, k.foto, k.asal_instansi
         ORDER BY total_suara DESC, k.nomor_peserta ASC"
    );
    $stmt->execute([$kategori]);
    $rows = $stmt->fetchAll();

    $total = 0;
    foreach ($rows as $r) {
        $total += (int) $r['total_suara'];
    }

    $data = [];
    $rank = 0;
    $suaraSebelumnya = null;

    foreach ($rows as $i => $r) {
        $suara = (int) $r['total_suara'];
        if ($suara !== $suaraSebelumnya) {
            $rank = $i + 1;
            $suaraSebelumnya = $suara;
        }

        $data[] = [
            'rank'          => $total > 0 ? $rank : null,
            'id'            => (int) $r['id'],
            'nomor_peserta' => $r['nomor_peserta'],
            'nama'          => $r['nama'],
            'asal_instansi' => $r['asal_instansi'],
            'foto_url'      => $r['foto'] ? UPLOAD_URL . '/' . rawurlencode($r['foto']) : null,
            'suara'         => $suara,
            'persen'        => $total > 0 ? round($suara / $total * 100, 1) : 0,
        ];
    }

    return [
        'kategori'    => $kategori,
        'total_suara' => $total,
        'diperbarui'  => date('d-m-Y H:i:s'),
        'data'        => $data,
    ];
}