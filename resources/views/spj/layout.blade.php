<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SPJ Perjalanan Dinas')</title>

    <style>
        /* =========================================================
           PENTING:
           - Kita TIDAK mengunci ukuran kertas (A4/F4/PDF) di CSS.
           - Jadi user bisa memilih "Paper size" di dialog print (seperti gambar).
           - Kita hanya atur margin print agar rapi & mirip PDF.
        ========================================================= */

        @page {
            margin: 12mm 16mm 16mm 16mm;
        }

        /* ================= GLOBAL ================= */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.35;
            /* rapat tapi masih aman */
            color: #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ================= PAGE WRAPPER =================
           Jangan kunci ukuran cm agar paper size ikut dialog print.
           max-width hanya untuk tampilan preview di layar.
        */
        .page {
            max-width: 21.6cm;
            /* biar tampilan layar mirip F4, tapi tidak memaksa print */
            margin: auto;
            padding: 0;
            /* padding dikontrol oleh @page margin saat print */
            box-sizing: border-box;
        }

        .page-break {
            page-break-after: always;
        }

        /* Jangan bikin break setelah elemen terakhir */
        .page-break:last-child {
            page-break-after: auto;
        }

        /* ================= TABLE ================= */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }

        /* Anti kepotong & anti blank page karena table split */
        table,
        tr,
        td,
        th {
            page-break-inside: avoid;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 12px 0;
        }

        /* ================= KOP SURAT ================= */
        .kop {
            position: relative;
            text-align: center;
            margin-bottom: 20px;
        }

        .kop img {
            position: absolute;
            left: 0;
            top: 0;
            width: 65px;
        }

        .kop-text {
            line-height: 1.05;
        }

        .kop-text .baris-1 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .kop-text .baris-2 {
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .kop-text .baris-3,
        .kop-text .baris-4,
        .kop-text .baris-5 {
            font-size: 11pt;
            margin: 0;
        }

        /* ================= JUDUL ================= */
        .judul {
            text-align: center;
            margin: 18px 0 14px;
        }

        .judul h3 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
        }

        .judul p {
            margin: 3px 0 0;
            font-size: 11pt;
        }

        /* ================= ISI ================= */
        .label {
            width: 90px;
        }

        .titik {
            width: 10px;
        }

        .isi {
            text-align: justify;
        }

        .bagian {
            margin: 18px 0 12px;
            font-weight: bold;
            text-align: center;
        }

        /* ================= TANDA TANGAN ================= */
        .ttd {
            width: 45%;
            float: right;
            margin-top: 25px;
        }

        .clearfix {
            clear: both;
        }

        .nama {
            margin-top: 55px;
            font-weight: bold;
            text-decoration: underline;
        }

        .nip {
            font-size: 11pt;
        }

        /* ================= PRINT RULES ================= */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            /* saat print, biarkan full lebar kertas */
            .page {
                max-width: none;
            }

            /* Hindari paragraf nanggung */
            p,
            li,
            td {
                orphans: 3;
                widows: 3;
            }
        }
    </style>

</head>

<body>

    @yield('content')

    {{-- AUTO PRINT + AUTO BACK --}}
    <script>
        window.onload = function() {
            window.print();
        };

        window.onafterprint = function() {
            window.history.back();
        };
    </script>

</body>

</html>
