<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara Pemasangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 10px;
            line-height: 1.1;
        }

        .header-container {
            text-align: center;
            margin-bottom: 5px;
        }

        .header-image {
            width: 100%;
            height: auto;
            max-height: 60px;
            display: block;
        }

        .smk3-logo {
            height: 50px;
            display: block;
            margin: 0 auto;
        }

        .title-section {
            text-align: center;
            margin-top: 5px;
        }

        .title-section h3 {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .title-section p {
            margin: 5px 0 0 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .section {
            border: 1px solid black;
            padding: 5px;
            margin-top: 5px;
        }

        .row-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .row-table td {
            padding: 1px;
            vertical-align: top;
            font-size: 10px;
        }

        .col-2 {
            width: 35%;
        }

        .col-3 {
            width: 60%;
        }

        .box {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            margin-right: 3px;
            vertical-align: middle;
        }

        input[type="text"] {
            border: none;
            border-bottom: 1px solid #000;
            padding: 1px 2px;
            width: 40px;
            /* Ukuran default */
            background: transparent;
            font-size: 9px;
        }

        .input-50px {
            width: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        td,
        th {
            padding: 2px;
            text-align: left;
            font-size: 9px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .signature-table td {
            width: 33%;
            text-align: center;
            font-size: 9px;
            padding: 0;
            border: none;
        }

        .lined-input {
            border-bottom: 1px solid #000;
            width: 80%;
            display: inline-block;
            height: 12px;
            margin-top: 5px;
        }

        .note {
            font-size: 8px;
            margin-top: 8px;
            font-style: italic;
            text-align: center;
        }

        ol,
        ul {
            margin: 1px 0;
            padding-left: 15px;
        }

        li {
            margin-bottom: 1px;
        }
    </style>
</head>

<body>
    <div class="header-container">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
            <tr>
                <td style="width: 80%; text-align: right; border: none; padding-right: 20px;">
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('img/prisan_banner.jpg'))) }}"
                        alt="Header PT PAL" class="header-image">
                </td>
                <td style="width: 20%; text-align: left; border: none;">
                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('img/smk3_logo.jpg'))) }}"
                        alt="Header SMK3" class="smk3-logo">
                </td>
            </tr>
        </table>

        <div class="title-section">
            <h3>BERITA ACARA PEMASANGAN</h3>
            <p>NO : {{ $record->nomor_bap }}</p>
        </div>
    </div>

    <p>Jenis Pekerjaan: {{ ucfirst(trim($record->jenis_pekerjaan)) }}</p>
    <div class="section">
        <table class="row-table">
            <tr>
                <td class="col-2">1. NO. SPK / SPBJ / PK / WO</td>
                <td>:</td>
                <td class="col-3">{{ $record->no_spk }}</td>
            </tr>
            <tr>
                <td class="col-2">2. CUSTOMER</td>
                <td>:</td>
                <td class="col-3">{{ $record->customer->customer_name }}</td>
            </tr>
            <tr>
                <td class="col-2">3. PELAKSANA GALI / KONTRAKTOR</td>
                <td>:</td>
                <td class="col-3">{{ $record->pelaksana_gali }}</td>
            </tr>
            <tr>
                <td class="col-2">4. PENYULANG & ARAH GARDU</td>
                <td>:</td>
                <td class="col-3">{{ $record->penyulang->penyulang_gardu }} Arah Gardu {{ $record->arah_gardu }}</td>
            </tr>
            <tr>
                <td class="col-2">5. LOKASI PEKERJAAN</td>
                <td>:</td>
                <td class="col-3">{{ $record->lokasi_pekerjaan }}</td>
            </tr>
            <tr>
                <td class="col-2">6. GALIAN & PERBAIKAN</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 33%;">
                                ASPAL <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->galian_perbaikan['aspal']))
                                        &#10004;
                                    @endif
                                </span><br>
                                BETON <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->galian_perbaikan['beton']))
                                        &#10004;
                                    @endif
                                </span><br>
                                LEBAR <span style="display: inline-block; width: 50px; text-align: center;">
                                    {{ $record->galian_perbaikan['lebar'] ?? '' }} m
                                </span>
                            </td>
                            <td style="width: 33%;">
                                BERM <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->galian_perbaikan['berm']))
                                        &#10004;
                                    @endif
                                </span><br>
                                TROTOAR <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->galian_perbaikan['trotoar']))
                                        &#10004;
                                    @endif
                                </span><br>
                                TINGGI <span style="display: inline-block; width: 50px; text-align: center;">
                                    {{ $record->galian_perbaikan['tinggi'] ?? '' }} m
                                </span>
                            </td>
                            <td style="width: 33%;">
                                JUMLAH GALIAN: {{ $record->galian_perbaikan['jumlah_galian'] ?? '.....' }} <br>
                                <br>
                                PANJANG <span style="display: inline-block; width: 50px; text-align: center;">
                                    {{ $record->galian_perbaikan['panjang'] ?? '' }} m
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">7. GANGGUAN PADA</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 33%;">
                                KABEL
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->gangguan['kabel']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">
                                SAMBUNGAN
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->gangguan['sambungan']))
                                        &#10004;
                                    @endif
                                </span><br>
                            </td>
                            <td style="width: 33%;">MERK {{ $record->gangguan['merk'] ?? '.....' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">8. CEK FISIK KABEL GANGGUAN</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td>
                                1. 1 kV
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['tegangan_1kv']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                7,2 kV
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['tegangan_7c2kv']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                17,5 kV
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['tegangan_17c5kv']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                24 kV
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['tegangan_24kv']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                36 kV
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['tegangan_36kv']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                2. XLPE
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['jenis_isolasi_xlpe']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                PILC
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['jenis_isolasi_pilc']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                3. 1 C
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['inti_kabel_1c']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                3 C
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['inti_kabel_3c']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                {{ $record->cek_fisik_kabel_gangguan['inti_kabel'] ?? '..............' }}
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['inti_kabel']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                4. 150 mm <sup>2</sup>
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['ukuran_kabel_150']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                240 mm <sup>2</sup>
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['ukuran_kabel_240']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                300 mm <sup>2</sup>
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['ukuran_kabel_300']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                {{ $record->cek_fisik_kabel_gangguan['ukuran_kabel'] ?? '..............' }}
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_gangguan['ukuran_kabel']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">9. CEK TAHANAN ISOLASI AWAL</td>
                <td>:</td>
                <td class="col-3">
                    R <input type="text" value="{{ $record->cek_tahanan_isolasi_awal['r'] ?? '' }}"
                        style="text-align: center;"> Ohm
                    &nbsp;&nbsp;
                    S <input type="text" value="{{ $record->cek_tahanan_isolasi_awal['s'] ?? '' }}"
                        style="text-align: center;"> Ohm
                    &nbsp;&nbsp;
                    T <input type="text" value="{{ $record->cek_tahanan_isolasi_awal['t'] ?? '' }}"
                        style="text-align: center;"> Ohm
                </td>
            </tr>
            <tr>
                <td class="col-2">10. CEK FISIK KABEL TAMBAHAN</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td>
                                1. 1 kV
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['tegangan_1kv']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                7,2 kV
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['tegangan_7c2kv']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                17,5 kV
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['tegangan_17c5kv']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                24 kV
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['tegangan_24kv']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                36 kV
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['tegangan_36kv']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                2. XLPE
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['jenis_isolasi_xlpe']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                PILC
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['jenis_isolasi_pilc']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                3. 1 C
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['inti_kabel_1c']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                3 C
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['inti_kabel_3c']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                {{ $record->cek_fisik_kabel_tambahan['inti_kabel'] ?? '..............' }}
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['inti_kabel']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                4. 150 mm <sup>2</sup>
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['ukuran_kabel_150']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                240 mm <sup>2</sup>
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['ukuran_kabel_240']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                300 mm <sup>2</sup>
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['ukuran_kabel_300']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td>
                                {{ $record->cek_fisik_kabel_tambahan['ukuran_kabel'] ?? '..............' }}
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->cek_fisik_kabel_tambahan['ukuran_kabel']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">11. NAMA MATERIAL</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 40%;">NAMA MATERIAL</td>
                            <td style="width: 30%;">SERIAL NUMBER</td>
                            <td style="width: 30%;">KONDUKTOR</td>
                        </tr>

                        @php
                            $materials = $record->material ?? [];
                            $totalRowsToDisplay = 5;
                        @endphp

                        @for ($i = 0; $i < $totalRowsToDisplay; $i++)
                            <tr>
                                @if (isset($materials[$i]))
                                    @php
                                        $item = $materials[$i];
                                    @endphp
                                    <td>{{ $i + 1 }}. {{ $item['nama_material'] ?? '.....' }}</td>
                                    <td>{{ $item['serial_number'] ?? '.....' }}</td>
                                    <td>{{ $item['konduktor'] ?? '.....' }}</td>
                                @else
                                    <td>{{ $i + 1 }}. ..............</td>
                                    <td>..............</td>
                                    <td>..............</td>
                                @endif
                            </tr>
                        @endfor

                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">12. CEK TAHANAN ISOLASI AKHIR</td>
                <td>:</td>
                <td class="col-3">
                    R <input type="text" value="{{ $record->cek_tahanan_isolasi_akhir['r'] ?? '' }}"
                        style="text-align: center;"> Ohm
                    &nbsp;&nbsp;
                    S <input type="text" value="{{ $record->cek_tahanan_isolasi_akhir['s'] ?? '' }}"
                        style="text-align: center;"> Ohm
                    &nbsp;&nbsp;
                    T <input type="text" value="{{ $record->cek_tahanan_isolasi_akhir['t'] ?? '' }}"
                        style="text-align: center;"> Ohm
                </td>
            </tr>
            <tr>
                <td class="col-2">13. PEKERJAAN LAIN-LAIN</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 33%;">
                                PENGASPALAN
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->pekerjaan_lain['pengaspalan']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">
                                COR BETON
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->pekerjaan_lain['cor_beton']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">
                                URUG
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->pekerjaan_lain['urug']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                GELAR KABEL
                                {{ $record->pekerjaan_lain['gelar_kabel'] ?? '.....' }} m
                            </td>
                            <td></td>
                            <td>
                                SEWA STAMPER
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->pekerjaan_lain['sewa_stamper']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">14. JOINTER PELAKSANA</td>
                <td>:</td>
                <td class="col-3">
                    <div>Leader: {{ $record->leader->nama_jointer ?? '............................................' }}
                    </div>
                    <div>Jointer:
                        {{ $record->jointer->nama_jointer ?? '............................................' }}</div>
                    <div>Helper: {{ $record->helper->nama_jointer ?? '............................................' }}
                    </div>
                </td>
            </tr>
            <tr>
                <td class="col-2">15. TITIK KOORDINAT (GPS)</td>
                <td>:</td>
                <td class="col-3">
                    @php
                        $koordinat = $record->titik_koordinat;
                    @endphp

                    ( {{ $koordinat['lat'] ?? '.....' }} , {{ $koordinat['lng'] ?? '.....' }} )
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table class="row-table">
            <tr>
                <td class="col-2">(KOLOM PENGAWAS)</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="col-2">16. WAKTU PEMASANGAN</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 33%;">TIBA DI LOKASI
                                {{ $record->waktu_pemasangan['tiba_di_lokasi'] ?? '..............' }}</td>
                            <td style="width: 33%;">MULAI KERJA
                                {{ $record->waktu_pemasangan['mulai_kerja'] ?? '..............' }}</td>
                            <td style="width: 33%;">SELESAI
                                {{ $record->waktu_pemasangan['selesai'] ?? '..............' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">17. PERALATAN KERJA</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 33%;">
                                LENGKAP
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->peralatan_kerja['lengkap']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">
                                TIDAK LENGKAP
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->peralatan_kerja['tidak_lengkap']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">{{ $record->peralatan_kerja['catatan'] ?? '................' }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">18. SERAGAM KERJA</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 33%;">
                                ADA
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->seragam_kerja['lengkap']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">
                                TIDAK ADA
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->seragam_kerja['tidak_lengkap']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">{{ $record->seragam_kerja['catatan'] ?? '................' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">19. PERALATAN K2</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 33%;">
                                ADA
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->peralatan_k2['lengkap']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">
                                TIDAK ADA
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->peralatan_k2['tidak_lengkap']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">{{ $record->peralatan_k2['catatan'] ?? '................' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">20. LABEL TIMAH / PENANG</td>
                <td>:</td>
                <td class="col-3">
                    <table class="row-table" style="margin-bottom: 0;">
                        <tr>
                            <td style="width: 33%;">
                                ADA
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->label_timah['lengkap']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">
                                TIDAK ADA
                                <span
                                    style="display:inline-block;width:10px;height:10px;border:1px solid #000;text-align:center;font-family:DejaVu Sans;font-size:10px;line-height:10px;">
                                    @if (!empty($record->label_timah['tidak_lengkap']))
                                        &#10004;
                                    @endif
                                </span>
                            </td>
                            <td style="width: 33%;">{{ $record->label_timah['catatan'] ?? '................' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="col-2">CATATAN PEKERJAAN</td>
                <td>:</td>
                <td class="col-3">
                    <div style="border: 1px solid #000; height: 60px; margin-top: 3px; padding: 2px;">
                        {{ $record->catatan_pekerjaan }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="signature-table" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <tr>
            <td style="width: 33%; text-align: center; font-size: 9px; padding: 0; border: none; vertical-align: top;">
                Mengetahui<br><br>
                @if ($record->signature_pengawas)
                    <img src="{{ $record->signature_pengawas }}" alt="Tanda Tangan Pengawas"
                        style="max-height: 60px; max-width: 100%; display: block; margin: 5px auto;">
                @else
                    <br><br><br><br>
                @endif
                <span class="lined-input"
                    style="border-bottom: 1px solid #000; width: 80%; display: inline-block; height: 12px; margin-top: 5px;">
                    {{ $record->nama_pengawas ?? '............................' }}
                </span><br>
                Pengawas
            </td>

            <td style="width: 33%; text-align: center; font-size: 9px; padding: 0; border: none; vertical-align: top;">
                Pelaksana<br>
                Jointer<br>
                @if ($record->signature_pelaksana)
                    <img src="{{ $record->signature_pelaksana }}" alt="Tanda Tangan Pelaksana"
                        style="max-height: 60px; max-width: 100%; display: block; margin: 5px auto;">
                @else
                    <br><br><br><br>
                @endif
                <span class="lined-input"
                    style="border-bottom: 1px solid #000; width: 80%; display: inline-block; height: 12px; margin-top: 5px;">
                    {{ $record->nama_pelaksana ?? '............................' }}
                </span><br>
                Pelaksana
            </td>

            <td style="width: 33%; text-align: center; font-size: 9px; padding: 0; border: none; vertical-align: top;">
                Tanggal,
                {{ $record->created_at ? $record->created_at->format('d F Y') : '..................' }}<br><br>
                @if ($record->signature_kontraktor)
                    <img src="{{ $record->signature_kontraktor }}" alt="Tanda Tangan Kontraktor"
                        style="max-height: 60px; max-width: 100%; display: block; margin: 5px auto;">
                @else
                    <br><br><br><br>
                @endif
                <span class="lined-input"
                    style="border-bottom: 1px solid #000; width: 80%; display: inline-block; height: 12px; margin-top: 5px;">
                    {{ $record->nama_kontraktor ?? '............................' }}
                </span><br>
                Kontraktor
            </td>
        </tr>
    </table>

    <div class="note">
        Berita Acara Pemasangan ini merupakan serah terima pekerjaan yang telah dilaksanakan dengan baik dan benar. <br>
        Putih untuk PAL, Merah untuk PLN/Pengawas, Kuning untuk Jointer, Biru untuk Koordinator.
    </div>

    {{-- FOTO PENGUKURAN (TARGET: 6 FOTO, 3 BARIS x 2 KOLOM) --}}
    <div style="page-break-before: always; margin-top: 20px;">
        <p
            style="text-align: left; font-size: 14px; font-weight: bold; margin: 0px; margin-top: -5px; margin-bottom: 10px;">
            Dokumentasi Foto Pengukuran
        </p>
        @if (
            $record->foto_pengukuran &&
                count(is_array($record->foto_pengukuran)
                        ? $record->foto_pengukuran
                        : json_decode($record->foto_pengukuran, true) ?? []) > 0)
            @php
                $gambarArrayPengukuran = is_array($record->foto_pengukuran)
                    ? $record->foto_pengukuran
                    : json_decode($record->foto_pengukuran, true) ?? [];
                $chunkedImagesPengukuran = array_chunk($gambarArrayPengukuran, 2); // 2 gambar per baris

                // Estimasi ukuran untuk 6 foto (3 baris)
                // Tinggi total tersedia untuk gambar: sekitar 728pt
                // Tinggi per baris: 728pt / 3 baris = ~242pt
                // Kurangi sedikit untuk padding/border sel:
                $maxImageHeightPengukuran = '220pt';
                $maxImageWidth = '250pt'; // Lebar tetap (setengah halaman dikurangi padding)
            @endphp

            <table style="width: 100%; border-collapse: collapse;">
                @foreach ($chunkedImagesPengukuran as $rowItems)
                    <tr>
                        @foreach ($rowItems as $imagePathDalamArray)
                            @php
                                $fullPath = storage_path('app/public/' . $imagePathDalamArray);
                                $imageSrc = '';
                                if (file_exists($fullPath)) {
                                    $fileContents = file_get_contents($fullPath);
                                    if ($fileContents !== false) {
                                        $imageData = base64_encode($fileContents);
                                        $imageMime = mime_content_type($fullPath) ?: 'image/jpeg';
                                        $imageSrc = 'data:' . $imageMime . ';base64,' . $imageData;
                                    }
                                }
                            @endphp
                            <td
                                style="width: 50%; text-align: center; padding: 5pt; border: 1px solid #ccc; vertical-align: middle;">
                                @if ($imageSrc)
                                    <img src="{{ $imageSrc }}" alt="Gambar Pengukuran"
                                        style="max-width: {{ $maxImageWidth }}; max-height: {{ $maxImageHeightPengukuran }}; width: auto; height: auto; display: block; margin: auto;">
                                @else
                                    <span style="font-size: 9px; color: #777;">Gbr tdk ditemukan:
                                        {{ basename($imagePathDalamArray) }}</span>
                                @endif
                            </td>
                        @endforeach
                        @if (count($rowItems) < 2)
                            <td style="width: 50%; border: 1px solid #ccc;"></td> {{-- Sel kosong --}}
                        @endif
                    </tr>
                @endforeach
            </table>
        @else
            <p style="font-size: 10px; color: #777;">Tidak ada dokumentasi foto pengukuran.</p>
        @endif
    </div>

    {{-- FOTO REALISASI (TARGET: 8 FOTO, 4 BARIS x 2 KOLOM) --}}
    <div style="page-break-before: always; margin-top: 20px;">
        <p
            style="text-align: left; font-size: 14px; font-weight: bold; margin: 0px; margin-top: -5px; margin-bottom: 10px;">
            Dokumentasi Foto Realisasi
        </p>
        @if (
            $record->foto_realisasi &&
                count(is_array($record->foto_realisasi)
                        ? $record->foto_realisasi
                        : json_decode($record->foto_realisasi, true) ?? []) > 0)
            @php
                $gambarArrayRealisasi = is_array($record->foto_realisasi)
                    ? $record->foto_realisasi
                    : json_decode($record->foto_realisasi, true) ?? [];
                $chunkedImagesRealisasi = array_chunk($gambarArrayRealisasi, 2); // 2 gambar per baris

                // Estimasi ukuran untuk 8 foto (4 baris)
                // Tinggi total tersedia untuk gambar: sekitar 728pt
                // Tinggi per baris: 728pt / 4 baris = ~182pt
                // Kurangi sedikit untuk padding/border sel:
                $maxImageHeightRealisasi = '160pt';
                $maxImageWidth = '250pt'; // Lebar bisa tetap sama
            @endphp

            <table style="width: 100%; border-collapse: collapse;">
                @foreach ($chunkedImagesRealisasi as $rowItems)
                    <tr>
                        @foreach ($rowItems as $imagePathDalamArray)
                            @php
                                $fullPath = storage_path('app/public/' . $imagePathDalamArray);
                                $imageSrc = '';
                                if (file_exists($fullPath)) {
                                    $fileContents = file_get_contents($fullPath);
                                    if ($fileContents !== false) {
                                        $imageData = base64_encode($fileContents);
                                        $imageMime = mime_content_type($fullPath) ?: 'image/jpeg';
                                        $imageSrc = 'data:' . $imageMime . ';base64,' . $imageData;
                                    }
                                }
                            @endphp
                            <td
                                style="width: 50%; text-align: center; padding: 5pt; border: 1px solid #ccc; vertical-align: middle;">
                                @if ($imageSrc)
                                    <img src="{{ $imageSrc }}" alt="Gambar Realisasi"
                                        style="max-width: {{ $maxImageWidth }}; max-height: {{ $maxImageHeightRealisasi }}; width: auto; height: auto; display: block; margin: auto;">
                                @else
                                    <span style="font-size: 9px; color: #777;">Gbr tdk ditemukan:
                                        {{ basename($imagePathDalamArray) }}</span>
                                @endif
                            </td>
                        @endforeach
                        @if (count($rowItems) < 2)
                            <td style="width: 50%; border: 1px solid #ccc;"></td> {{-- Sel kosong --}}
                        @endif
                    </tr>
                @endforeach
            </table>
        @else
            <p style="font-size: 10px; color: #777;">Tidak ada dokumentasi foto realisasi.</p>
        @endif
    </div>

</body>

</html>
