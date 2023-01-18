<?php
// Buka potongan image yang akan digunakan
$image = imagecreatefromjpeg('originalImage/oriImage.jpg');

// Konversi string "01809" ke kode ASCII
$data = "01809"; //diambil 5 angka dari NIM : terakhir 2111601809 Nama : ROBBY ILHAMKUSUMA
$data_ascii = array();
for ($i = 0; $i < strlen($data); $i++) {
    $data_ascii[] = ord($data[$i]);
}

// Konversi kode ASCII ke biner
$data_binary = array();
foreach ($data_ascii as $ascii) {
    $data_binary[] = decbin($ascii);
}

// Pilih jumlah bit yang akan digunakan untuk menyisipkan data
$m = 2;

// Pilih pixel yang akan digunakan untuk menyimpan data
$x = 0;
$y = 0;

// Looping untuk setiap bit dari data yang akan disisipkan
foreach ($data_binary as $binary) {
    for ($i = 0; $i < strlen($binary); $i++) {
        // Ambil nilai RGB dari pixel yang dipilih
        $rgb = imagecolorat($image, $x, $y);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        // Ambil m bit LSB dari tiap channel R, G, dan B
        $r_lsb = $r & (1 << $m - 1);
        $g_lsb = $g & (1 << $m - 1);
        $b_lsb = $b & (1 << $m - 1);

        // Ganti m bit LSB dengan bit dari data yang akan disisipkan
        if ($binary[$i] == 1) {
            $r_lsb = 1 << $m - 1;
            $g_lsb = 1 << $m - 1;
            $b_lsb = 1 << $m - 1;
        } else {
            $r_lsb = 0;
            $g_lsb = 0;
            $b_lsb = 0;
        }


        // Ganti m bit LSB pada tiap channel R, G, dan B
        $new_r = ($r - ($r & (1 << $m - 1))) + $r_lsb;
        $new_g = ($g - ($g & (1 << $m - 1))) + $g_lsb;
        $new_b = ($b - ($b & (1 << $m - 1))) + $b_lsb;

        // Buat warna baru dengan nilai RGB yang telah diubah
        $new_color = imagecolorallocate($image, $new_r, $new_g, $new_b);

        // Ganti warna pada pixel yang dipilih dengan warna baru
        imagesetpixel($image, $x, $y, $new_color);

        // Pindah ke pixel berikutnya
        $x++;
        if ($x >= imagesx($image)) {
            $x = 0;
            $y++;
        }
    }
}

// Simpan potongan image yang telah disisipkan data
imagejpeg($image, 'stegoImage/stegoImage.jpg');

// Liberasi memori
imagedestroy($image);
echo "Steganografi Image Berhasil";
?>