<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kodeBuku = $_POST["kode-buku"];
    $judul = $_POST["judul"];
    $penulis = $_POST["penulis"];
    $tahunTerbit = $_POST["tahun_terbit"];

    // Penanganan pengunggahan file
    // mengambil data melalui metode POST
    $uploadStatus = '';
    $fileName = '';
    $dest_path = '';
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];
        //  mengubagah string jadi array
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // menentukan gambar berupa apaa 
        $allowedfileExtensions = array('jpeg', 'gif', 'png' , 'jpg'); // Specify allowed file types
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Ubah jalur relatif menjadi jalur absolut
            $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '';
            $dest_path = $uploadFileDir . $fileName;

            // tampilan setelah memili/ setelah mengaplod foto foto tadi
            // untuk memindahkan file foto upld file
            if (is_writable($uploadFileDir)) {
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $uploadStatus = '';
                } else {
                    $uploadStatus = 'Ada beberapa kesalahan saat memindahkan file ke direktori unggah. Harap pastikan direktori unggahan dapat ditulis oleh server web.';
                }
            }
        } else {
            $uploadStatus = 'Gagal mengunggah. Jenis file yang diperbolehkan: ' . implode(',', $allowedfileExtensions);
        }
    } else {
        $uploadStatus = 'Tidak ada file yang diunggah atau terjadi kesalahan pengunggahan.';
    }

    echo $uploadStatus;// Menampilkan status unggahan

    // Cek apakah kode_buku sudah ada
    $checkSql = "SELECT kode_buku FROM buku WHERE kode_buku = '$kodeBuku'";
    $result = $koneksi->query($checkSql);

    if ($result->num_rows > 0) {
        echo "Error: Duplicate entry for kode_buku.";
    } else {
        // Masukkan data buku ke dalam database
        $sql = "INSERT INTO buku (kode_buku, judul, penulis, tahun_terbit, file_name, file_path) VALUES ('$kodeBuku', '$judul', '$penulis', '$tahunTerbit', '$fileName', '$dest_path')";

        if ($koneksi->query($sql) === TRUE) {
            echo "Data berhasil dimasukkan.";
        } else {
            echo "Error: " . $sql . "<br>" . $koneksi->error;
        }
    }
}
?>