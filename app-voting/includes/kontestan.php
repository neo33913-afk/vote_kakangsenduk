<?php
const FOTO_MAKS_BYTES = 2 * 1024 * 1024;   // 2 MB
const FOTO_TIPE = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];

function str_input($v): string
{
    return is_string($v) ? trim($v) : '';
}

function kosong_jadi_null($v)
{
    return $v === '' ? null : $v;
}

/** Ambil dan validasi input form kontestan. Return [data, errors]. */
function ambil_input_kontestan(array $src): array
{
    $data = [
        'nomor_peserta' => str_input($src['nomor_peserta'] ?? ''),
        'nama'          => str_input($src['nama'] ?? ''),
        'kategori'      => str_input($src['kategori'] ?? ''),
        'asal_instansi' => str_input($src['asal_instansi'] ?? ''),
        'tempat_lahir'  => str_input($src['tempat_lahir'] ?? ''),
        'tanggal_lahir' => str_input($src['tanggal_lahir'] ?? ''),
        'visi'          => str_input($src['visi'] ?? ''),
        'misi'          => str_input($src['misi'] ?? ''),
        'prestasi'      => str_input($src['prestasi'] ?? ''),
        'deskripsi'     => str_input($src['deskripsi'] ?? ''),
    ];

    $errors = [];

    if (!preg_match('/^[0-9A-Za-z-]{1,10}$/', $data['nomor_peserta'])) {
        $errors[] = 'Nomor peserta wajib diisi (maksimal 10 karakter, huruf/angka/tanda minus).';
    }
    if ($data['nama'] === '' || mb_strlen($data['nama']) > 100) {
        $errors[] = 'Nama wajib diisi (maksimal 100 karakter).';
    }
    if (!in_array($data['kategori'], ['Kakang', 'Senduk'], true)) {
        $errors[] = 'Kategori harus Kakang atau Senduk.';
    }
    if ($data['asal_instansi'] === '' || mb_strlen($data['asal_instansi']) > 150) {
        $errors[] = 'Asal sekolah/instansi wajib diisi (maksimal 150 karakter).';
    }
    if (mb_strlen($data['tempat_lahir']) > 100) {
        $errors[] = 'Tempat lahir maksimal 100 karakter.';
    }
    if ($data['tanggal_lahir'] !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $data['tanggal_lahir']);
        if (!$d || $d->format('Y-m-d') !== $data['tanggal_lahir'] || $d > new DateTime('today')) {
            $errors[] = 'Tanggal lahir tidak valid.';
        }
    }
    foreach (['visi', 'misi', 'prestasi', 'deskripsi'] as $f) {
        if (mb_strlen($data[$f]) > 5000) {
            $errors[] = ucfirst($f) . ' terlalu panjang (maksimal 5000 karakter).';
        }
    }

    return [$data, $errors];
}

/** Nomor peserta harus unik per kategori (kecuali untuk kontestan $kecuali). */
function nomor_terpakai(PDO $pdo, string $kategori, string $nomor, ?int $kecuali = null): bool
{
    $stmt = $pdo->prepare(
        'SELECT id FROM kontestan WHERE kategori = ? AND nomor_peserta = ? AND id <> ?'
    );
    $stmt->execute([$kategori, $nomor, $kecuali ?? 0]);
    return (bool) $stmt->fetch();
}

function jumlah_suara_kontestan(PDO $pdo, int $id): int
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM voting WHERE kontestan_id = ?');
    $stmt->execute([$id]);
    return (int) $stmt->fetchColumn();
}

/**
 * Proses upload foto. Return nama file baru, atau null jika tidak ada file dikirim.
 * Melempar RuntimeException (pesan aman ditampilkan) jika file bermasalah.
 */
function proses_upload_foto(array $file): ?string
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (is_array($file['error'])) {
        throw new RuntimeException('Upload tidak valid.');
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('Ukuran foto terlalu besar (maksimal 2 MB).');
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload foto gagal. Silakan coba lagi.');
    }
    if ($file['size'] > FOTO_MAKS_BYTES) {
        throw new RuntimeException('Ukuran foto terlalu besar (maksimal 2 MB).');
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Upload tidak valid.');
    }

    // Tipe ditentukan dari ISI file, bukan dari nama atau tipe yang dikirim browser
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset(FOTO_TIPE[$mime]) || @getimagesize($file['tmp_name']) === false) {
        throw new RuntimeException('Foto harus berformat JPG, PNG, atau WebP.');
    }

    // Nama file dibuat sendiri; nama asli dari pengguna tidak pernah dipakai
    $nama = bin2hex(random_bytes(16)) . '.' . FOTO_TIPE[$mime];

    if (!is_dir(UPLOAD_PATH) && !mkdir(UPLOAD_PATH, 0755, true)) {
        throw new RuntimeException('Folder uploads tidak dapat dibuat.');
    }
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH . '/' . $nama)) {
        throw new RuntimeException('Foto gagal disimpan.');
    }

    return $nama;
}

function hapus_foto(?string $nama): void
{
    if (!$nama) return;
    $path = UPLOAD_PATH . '/' . basename($nama);
    if (is_file($path)) {
        @unlink($path);
    }
}