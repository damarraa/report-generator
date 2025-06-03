<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara Pemasangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
            background: #fff;
        }

        /* Grup selector untuk tabel utama */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            /* Penting untuk menghilangkan celah antar sel */
            margin-bottom: 8px;
        }

        /* INI BAGIAN UTAMA PERBAIKANNYA */
        /* Menghilangkan border dari sel tabel di dalam .main-table */
        .main-table td,
        .main-table th {
            border: none;
            /* Menghapus border dari semua sel */
            padding: 4px;
            vertical-align: top;
            text-align: left;
            /* Menambahkan perataan teks agar rapi */
        }

        .section-title {
            font-weight: bold;
            margin-top: 10px;
            text-transform: uppercase;
        }

        .small {
            font-size: 11px;
        }

        /* Perbaikan kecil pada header agar lebih rapi */
        .header-container {
            display: flex;
            justify-content: space-between;
            /* Menjaga jarak antar logo */
            align-items: center;
            /* Menyelaraskan logo secara vertikal */
            margin-bottom: 5px;
        }

        .header-container img {
            max-height: 90px;
            /* Sedikit penyesuaian tinggi agar proporsional */
            width: auto;
        }

        /* Memberi border pada container luar, bukan pada tabel */
        .form-container {
            border: 1px solid #000;
            padding: 10px;
        }

        /* Penyesuaian untuk input fields agar lebih konsisten */
        input[type="text"] {
            border: 1px solid #ccc;
            border-radius: 3px;
            padding: 2px 4px;
        }
    </style>
</head>

<body>

    <div class="header-container">
        <div class="logo-left">
            <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('img/prisan_banner.jpg'))) }}"
                alt="Header PT PAL">
        </div>
        <div class="logo-right">
            <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('img/smk3_logo.jpg'))) }}"
                alt="Header SMK3">
        </div>
    </div>

    <h3 style="text-align: center; margin-bottom: 0;">BERITA ACARA PEMASANGAN</h3>
    <p style="text-align: center; margin-top: 2px;">NO : {{ $record->nomor_bap }}</p>

    <p>Jenis Pekerjaan:
        {{ match ($record->jenis_pekerjaan) {
            'jointing' => 'Jointing',
            'terminating' => 'Terminating',
            default => ucfirst($record->jenis_pekerjaan),
        } }}
    </p>

    <div class="form-container">

        <table class="main-table">
            <tr>
                <td style="width: 5%;">1</td>
                <td style="width: 35%;">NO. SPK / SPBJ / PK / WO</td>
                <td style="width: 5%;">:</td>
                <td style="width: 55%;">{{ $record->no_spk }}</td>
            </tr>
            <tr>
                <td style="width: 5%;">2.</td>
                <td>CUSTOMER</td>
                <td>:</td>
                <td>{{ $record->customer->customer_name ?? '-' }}</td>
            </tr>
            <tr>
                <td style="width: 5%;">3.</td>
                <td>PELAKSANA GALI / KONTRAKTOR</td>
                <td>:</td>
                <td>{{ $record->pelaksana_gali }}</td>
            </tr>
            <tr>
                <td style="width: 5%;">4.</td>
                <td>PENYULANG & ARAH GARDU</td>
                <td>:</td>
                <td>{{ $record->penyulang->penyulang_gardu ?? '-' }}</td>
            </tr>
            <tr>
                <td style="width: 5%;">5.</td>
                <td>LOKASI PEKERJAAN</td>
                <td>:</td>
                <td>{{ $record->lokasi_pekerjaan }}</td>
            </tr>
            <tr>
                <td style="width: 5%;">6.</td>
                <td>GALIAN & PERBAIKAN</td>
                <td>:</td>
                <td>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px;">
                        <!-- Kolom 1 -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> ASPAL</label>
                            <label style="display: block;"><input type="checkbox" /> BERM</label>
                            <label style="display: block;">LEBAR: <input type="text" style="width: 30%;" /> m</label>
                        </div>

                        <!-- Kolom 2 -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> BETON</label>
                            <label style="display: block;"><input type="checkbox" /> TROTOAR</label>
                            <label style="display: block;">TINGGI: <input type="text" style="width: 30%;" />
                                m</label>
                        </div>

                        <!-- Kolom 3 -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> JUMLAH GALIAN</label>
                            <label style="display: block;"><input type="checkbox" /> ____</label>
                            <label style="display: block;">PANJANG: <input type="text" style="width: 30%;" />
                                m</label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">7.</td>
                <td>GANGGUAN PADA</td>
                <td>:</td>
                <td>
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <!-- Item 1 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="checkbox" /> KABEL
                        </label>

                        <!-- Item 2 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            <input type="checkbox" /> SAMBUNGAN
                        </label>

                        <!-- Item 3 -->
                        <label style="display: flex; align-items: center;">
                            MERK: <input type="text" style="width: 30%;" />
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">8.</td>
                <td>CEK FISIK KABEL GANGGUAN</td>
                <td>:</td>
                <td>
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 5px; align-items: start;">
                        <!-- Kolom 1 - 1 kV -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> 1 kV</label>
                            <label style="display: block;"><input type="checkbox" /> XLPE</label>
                            <label style="display: block;"><input type="checkbox" /> 1 C</label>
                            <label style="display: block;"><input type="checkbox" /> 150 mm<sup>2</sup></label>
                        </div>

                        <!-- Kolom 2 - 7,2 kV -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> 7,2 kV</label>
                            <label style="display: block;"><input type="checkbox" /> PILC</label>
                            <label style="display: block;"><input type="checkbox" /> 3 C</label>
                            <label style="display: block;"><input type="checkbox" /> 240 mm<sup>2</sup></label>
                        </div>

                        <!-- Kolom 3 - 17,5 kV -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> 17,5 kV</label>
                            <label style="display: block;"><br></label>
                            <label style="display: block;"><input type="checkbox"> .... </label>
                            <label style="display: block;"><input type="checkbox" /> 300 mm<sup>2</sup></label>
                        </div>

                        <!-- Kolom 4 - 24 kV -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> 24 kV</label>
                            <label style="display: block;"><br></label>
                            <label style="display: block;"><br></label>
                            <label style="display: block;"><input type="checkbox"> .... </label>
                        </div>

                        <!-- Kolom 5 - 36 kV -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> 36 kV</label>
                            <label style="display: block;"><br></label>
                            <label style="display: block;"><br></label>
                            <label style="display: block;"><br></label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">9.</td>
                <td>CEK TAHANAN ISOLASI AWAL</td>
                <td>:</td>
                <td>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <!-- Item 1 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            R <input type="text" style="width: 30%;" /> Ohm
                        </label>

                        <!-- Item 2 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            S <input type="text" style="width: 30%;" /> Ohm
                        </label>

                        <!-- Item 3 -->
                        <label style="display: flex; align-items: center;">
                            T <input type="text" style="width: 30%;" /> Ohm
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">10.</td>
                <td>CEK FISIK KABEL TAMBAHAN</td>
                <td>:</td>
                <td>
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 5px; align-items: start;">
                        <!-- Kolom 1 - 1 kV -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> 1 kV</label>
                            <label style="display: block;"><input type="checkbox" /> XLPE</label>
                            <label style="display: block;"><input type="checkbox" /> 1 C</label>
                            <label style="display: block;"><input type="checkbox" /> 150 mm<sup>2</sup></label>
                        </div>

                        <!-- Kolom 2 - 7,2 kV -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> 7,2 kV</label>
                            <label style="display: block;"><input type="checkbox" /> PILC</label>
                            <label style="display: block;"><input type="checkbox" /> 3 C</label>
                            <label style="display: block;"><input type="checkbox" /> 240 mm<sup>2</sup></label>
                        </div>

                        <!-- Kolom 3 - 17,5 kV -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> 17,5 kV</label>
                            <label style="display: block;"><br></label>
                            <label style="display: block;"><input type="checkbox"> .... </label>
                            <label style="display: block;"><input type="checkbox" /> 300 mm<sup>2</sup></label>
                        </div>

                        <!-- Kolom 4 - 24 kV -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> 24 kV</label>
                            <label style="display: block;"><br></label>
                            <label style="display: block;"><br></label>
                            <label style="display: block;"><input type="checkbox"> .... </label>
                        </div>

                        <!-- Kolom 5 - 36 kV -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> 36 kV</label>
                            <label style="display: block;"><br></label>
                            <label style="display: block;"><br></label>
                            <label style="display: block;"><br></label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <!-- Nomor 11. memakai rowspan agar sejajar dengan baris header dan data -->
                <td style="width: 5%;" rowspan="2">11.</td>

                <!-- Header kolom utama -->
                <td colspan="6">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 0;">
                        <tr>
                            <td style="width: 33%; font-weight: bold; text-align: left;">NAMA MATERIAL</td>
                            <td style="width: 33%; font-weight: bold; text-align: left;">SERIAL NUMBER</td>
                            <td style="width: 33%; font-weight: bold; text-align: left;">KONDUKTOR</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <!-- Isi data -->
                <td colspan="6">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                        <tr>
                            <!-- NAMA MATERIAL -->
                            <td style="width: 33%; vertical-align: top;">
                                <ol style="margin: 0; padding-left: 20px;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <li>
                                            <span
                                                style="display: inline-block; width: 65%;">...............................................</span>
                                            <span style="margin-left: 5px;">Set</span>
                                        </li>
                                    @endfor
                                </ol>
                            </td>

                            <!-- SERIAL NUMBER -->
                            <td style="width: 33%; vertical-align: top;">
                                <ul style="list-style: none; margin: 0; padding-left: 0;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <li>.......................................................</li>
                                    @endfor
                                </ul>
                            </td>

                            <!-- KONDUKTOR -->
                            <td style="width: 33%; vertical-align: top;">
                                <ul style="list-style: none; margin: 0; padding-left: 0;">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <li>.......................................................</li>
                                    @endfor
                                </ul>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">12.</td>
                <td>CEK TAHANAN ISOLASI AKHIR</td>
                <td>:</td>
                <td>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <!-- Item 1 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            R <input type="text" style="width: 30%;" /> Ohm
                        </label>

                        <!-- Item 2 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            S <input type="text" style="width: 30%;" /> Ohm
                        </label>

                        <!-- Item 3 -->
                        <label style="display: flex; align-items: center;">
                            T <input type="text" style="width: 30%;" /> Ohm
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">13.</td>
                <td>PEKERJAAN LAIN-LAIN</td>
                <td>:</td>
                <td>
                    <div style="font-weight: bold; margin-bottom: 8px;">13 PEKERJAAN LAIN-LAIN</div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                        <!-- Kolom 1 -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> PENGASPALAN</label>
                            <label style="display: block;"><input type="checkbox" /> GELAR KABEL</label>
                        </div>
                        <!-- Kolom 2 -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> COR BETON</label>
                        </div>
                        <!-- Kolom 3 -->
                        <div>
                            <label style="display: block;"><input type="checkbox" /> URUG</label>
                            <label style="display: block;"><input type="checkbox" /> SEWA STAMPER</label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">14.</td>
                <td>JOINTER PELAKSANA</td>
                <td>:</td>
                <td>
                    <div
                        style="display: grid; grid-template-columns: repeat(5, 1fr); grid-template-rows: repeat(5, 1fr); grid-column-gap: 0px; grid-row-gap: 0px;">
                        <div style="grid-area: 1 / 1 / 2 / 2;">
                            Leader: <input type="text" style="width: 100px;" />
                        </div>
                        <div style="grid-area: 2 / 1 / 3 / 2;">
                            Jointer: <input type="text" style="width: 100px;" />
                        </div>
                        <div style="grid-area: 3 / 1 / 4 / 2;">
                            Helper: <input type="text" style="width: 100px;" />
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">15.</td>
                <td>TITIK KOORDINAT (GPS)</td>
                <td>:</td>
                <td><input type="text" style="width: 100px;" /></td>
            </tr>
        </table>
    </div>
    <br>
    <div class="form-container">
        <table class="main-table">
            <tr>
                <td colspan="3">(KOLOM PENGAWAS)</td>
            </tr>
            <tr>
                <td style="width: 5%;">16.</td>
                <td>WAKTU PEMASANGAN</td>
                <td>:</td>
                <td>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <!-- Item 1 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            TIBA DI LOKASI <input type="text" style="width: 30%;" />
                        </label>

                        <!-- Item 2 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            MULAI KERJA <input type="text" style="width: 30%;" />
                        </label>

                        <!-- Item 3 -->
                        <label style="display: flex; align-items: center;">
                            SELESAI <input type="text" style="width: 30%;" />
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">17.</td>
                <td>PERALATAN KERJA</td>
                <td>:</td>
                <td>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <!-- Item 1 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            LENGKAP <input type="text" style="width: 30%;" />
                        </label>

                        <!-- Item 2 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            TIDAK LENGKAP <input type="text" style="width: 30%;" />
                        </label>

                        <!-- Item 3 -->
                        <label style="display: flex; align-items: center;">
                            .....
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">18.</td>
                <td>SERAGAM KERJA</td>
                <td>:</td>
                <td>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <!-- Item 1 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            ADA <input type="text" style="width: 30%;" />
                        </label>

                        <!-- Item 2 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            TIDAK ADA <input type="text" style="width: 30%;" />
                        </label>

                        <!-- Item 3 -->
                        <label style="display: flex; align-items: center;">
                            .....
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">19.</td>
                <td>PERALATAN K2</td>
                <td>:</td>
                <td>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <!-- Item 1 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            LENGKAP <input type="text" style="width: 30%;" />
                        </label>

                        <!-- Item 2 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            TIDAK LENGKAP <input type="text" style="width: 30%;" />
                        </label>

                        <!-- Item 3 -->
                        <label style="display: flex; align-items: center;">
                            .....
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 5%;">20.</td>
                <td>LABEL TIMAH / PENANG</td>
                <td>:</td>
                <td>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <!-- Item 1 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            ADA <input type="text" style="width: 30%;" />
                        </label>

                        <!-- Item 2 -->
                        <label style="display: flex; align-items: center; gap: 5px;">
                            TIDAK ADA <input type="text" style="width: 30%;" />
                        </label>

                        <!-- Item 3 -->
                        <label style="display: flex; align-items: center;">
                            .....
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <!-- Spacer Column -->
                <td style="width: 5%;"></td>
                <!-- CATATAN PEKERJAAN -->
                <td style="width: 55%; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="font-weight: bold;">CATATAN PEKERJAAN :</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #000; height: 100px;"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
