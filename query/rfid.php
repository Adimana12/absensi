<?php
include "koneksi.php";
$time = date('H:i:s');
// start operations
$sql = mysqli_query($db, "SELECT * FROM temp_rfid WHERE jenis_scan ='absen' ORDER BY id_temp DESC LIMIT 1");
$rfid = mysqli_fetch_array($sql);
$row_rfid = mysqli_num_rows($sql);

if ($row_rfid < 1) {
    // jika tidak ada data
    dataKosong();
}
// jika ada data di temp_rfid
if ($row_rfid >= 1) {
    $id_card = $rfid['rfid'];
    // untuk siswa
    // menjalankan cetakAbsen
    cetakAbsen($db, $id_card);
    $last = strtotime($rfid['last_use']) - strtotime($time);
    echo $last;
    if (($last < -300) || ($last > 300)) {
        mysqli_query($db, "DELETE FROM temp_rfid WHERE `temp_rfid`.`rfid` = '$id_card'");
    }
}

// buat waktu
echo "<h1>{$time}</h1>";




function dataKosong()
{
    echo "<img src='public/images/logo-scan.png' class='mt-lg-5' width='50%' alt='logo scan'>";
}
function cetakAbsen($db, $id_card)
{
    $data_siswa = mysqli_query($db, "SELECT * FROM tb_siswa WHERE uuid = '$id_card' LIMIT 1");
    $row_siswa = mysqli_num_rows($data_siswa);
    if ($row_siswa >= 1) {
        $data_s = mysqli_fetch_array($data_siswa);
        $id_siswa = $data_s['id_siswa'];
        $absen = mysqli_query($db, "SELECT * FROM tb_siswa
LEFT JOIN tb_absen_siswa ON tb_siswa.id_siswa = tb_absen_siswa.id_siswa
LEFT JOIN tb_kelas ON tb_siswa.id_kelas = tb_kelas.id_kelas
WHERE tb_siswa.id_siswa = $id_siswa ORDER BY tb_absen_siswa.id_absen_siswa LIMIT 1;
    ");
        $data = mysqli_fetch_array($absen);
        if ($data['keterangan_absen'] == "Terlambat") {
            $color = "danger";
        } else {
            $color = "success";
        }

        ?>

        <div class="card bg-<?= $color; ?> text-white shadow">
            <div class="card-body">
                <h3 class="mt-2">
                    <?= $data['nama_siswa']; ?>
                </h3>
                <hr>
                <h4>
                    <?= $data['status_absen']; ?>
                </h4>
                <p>
                    <?= $data['keterangan_absen']; ?>
                </p>
                <p>
                    <?= $data['masuk']; ?>,
                    <?= $data['tgl']; ?>
                </p>
                <?php
                if ($data['status_absen'] != "Masuk") {
                    echo "<p>{$data['pulang']}, {$data['tgl']}</p>";
                }
                ?>
            </div>
        </div>

        <?php
    }
    // end if siswa

}
// end function cetakAbsen