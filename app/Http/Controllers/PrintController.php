<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

function _tl($s, $l, $p = ' ')
{
    return str_pad($s, $l, $p, STR_PAD_RIGHT);
}

function _tr($s, $l, $p = ' ')
{
    return str_pad($s, $l, $p, STR_PAD_LEFT);
}

function _tc($s, $l, $p = ' ')
{
    return str_pad($s, $l, $p, STR_PAD_BOTH);
}

class PrintController extends Controller
{
    const WIDTH = 48;

    public function printSuratOrder(Request $request){
        function rupiah ($angka) {
            $hasil = 'Rp ' . number_format($angka, 0, "", ".");
            return $hasil;
        }
        //Variable
        $NoNota = $request->kodetransaksi;

        $Cabang = $request->profile['nama'];
        $RX = $request->karyawan['nama'];

        $toko_nama = "OPTIK KUSTIN";
        $toko_alamat_1 = $request->profile['alamat'];
        $toko_alamat_2 = $request->profile['kota'];
        $toko_telp = $request->profile['notelp'];
        $toko_wa = "0812 3456 7890";       

        //Printer Initialization
        $printerName = "POS-80";
        $connector = new WindowsPrintConnector($printerName);
        $printer = new Printer($connector);

        //Design
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setBarcodeHeight(90);
        $printer->setBarcodeWidth(4);
        $printer->barcode($NoNota, Printer::BARCODE_CODE93);
        $printer->setJustification();
        $printer->feed();
        
        //Design Header
        $this->generateHeader($printer, $toko_nama, $toko_alamat_1, $toko_alamat_2, $toko_telp, $toko_wa);

        //Nota SO
        $printer->feed();
        $printer->text(_tl('No. ', 15) . ": " . $NoNota);
        $printer->feed();
        $printer->text(_tl('Rx. ', 15) . ": " . $RX);
        $printer->feed();
        $printer->text(_tl('Tanggal', 15) . ": " . date("d/m/Y", strtotime($request->tanggaltransaksi)));
        $printer->feed();
        $printer->text(_tl('Tgl. Selesai', 15) . ": " . date("d/m/Y", strtotime( $request->tanggalselesai)));
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();
        $printer->text(_tl('Nama', 15) . ": " . $request->customer['nama']);
        $printer->feed();
        $printer->text(_tl('Alamat', 15) . ": " . $request->customer['alamat']);
        $printer->feed();
        $printer->text(_tl('Telp ', 15) . ": " . $request->customer['notelp']);
        $printer->feed();
        $printer->text(_tl('Umur ', 15) . ": " . $request->customer['umur']);
        $printer->feed();
        $printer->text(_tl('Jenis Kelamin ', 15) . ": " . strtoupper($request->customer['jeniskelamin']));
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '='));
        $printer->feed();

        $printer->setDoubleStrike(true);
        $printer->text(
            _tl('', 3) .
            _tl('SPH', 7) .
            _tc('CYL', 7) .
            _tc('AXIS', 7) .
            _tc('ADD', 7) .
            _tc('PD', 7) .
            _tc('Vis Akhir', 10)
        );
        $printer->setDoubleStrike(false);
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '='));
        $printer->feed();

        $printer->setDoubleStrike(true);
        $printer->text(
            _tl('R', 3) .
            _tl($request->customer['resep'][0]['r_sph'] ?? '', 7) .
            _tc($request->customer['resep'][0]['r_cyl'] ?? '', 7) .
            _tc($request->customer['resep'][0]['r_axs'] ?? '', 7) .
            _tc($request->customer['resep'][0]['r_add'] ?? '', 7) .
            _tc($request->customer['resep'][0]['pd'], 7) .
            _tc($request->customer['resep'][0]['visakhir'], 10)
        );
        $printer->setDoubleStrike(false);
        $printer->setDoubleStrike(true);
        $printer->text(
            _tl('L', 3) .
            _tl($request->customer['resep'][0]['l_sph'] ?? '', 7) .
            _tc($request->customer['resep'][0]['l_cyl'] ?? '', 7) .
            _tc($request->customer['resep'][0]['l_axs'] ?? '', 7) .
            _tc($request->customer['resep'][0]['l_add'] ?? '', 7) .
            _tc('', 7) .
            _tc('', 10)
        );
        $printer->setDoubleStrike(false);

        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();

        $printer->text(_tl('Jenis Frame', 15) . ': ' . _tl($request->customer['resep'][0]['jenisframe'], 29));
        $printer->feed();
        $printer->text(_tl('Koridor', 15) . ': ' . _tl($request->customer['resep'][0]['koridor'], 29));
        $printer->feed();
        $printer->text(_tl('Visus Balance', 15) . ': ' . _tl($request->customer['resep'][0]['visusbalance'], 29));
        $printer->feed();
        $printer->text(_tl('Duke Elder', 15) . ': ' . _tl($request->customer['resep'][0]['dukeelder'], 29));
        $printer->feed();
        $printer->text(_tl('Wrap Angle', 15) . ': ' . _tl($request->customer['resep'][0]['wrapangle'], 29));
        $printer->feed();
        $printer->text(_tl('Pantoskopik', 15) . ': ' . _tl($request->customer['resep'][0]['pantoskopik'], 29));
        $printer->feed();
        $printer->text(_tl('Vertex Distance', 15) . ': ' . _tl($request->customer['resep'][0]['vertexdistance'], 29));
        $printer->feed();
        $printer->text(_tl('Catatan Resep', 15) . ': ' . _tl($request->customer['resep'][0]['catatan'], 29));
        $printer->feed();
        $printer->feed();
        
        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();
        $printer->feed();
        
        $printer->setDoubleStrike(true);
        $printer->text(_tl('PRECAL:', self::WIDTH));
        $printer->setDoubleStrike(false);

        $printer->text(_tl('', self::WIDTH, '='));
        $printer->feed();
        $printer->setDoubleStrike(true);
        $printer->text(
            _tc('A', 9) .
            _tc('B', 9) .
            _tc('DBL', 9) .
            _tc('MPD', 9) .
            _tc('SH/PV', 9)
        );
        $printer->setDoubleStrike(false);
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '='));
        $printer->feed();

        $printer->setDoubleStrike(true);
        $printer->text(
            _tc($request->customer['resep'][0]['A'], 9) .
            _tc($request->customer['resep'][0]['B'], 9) .
            _tc($request->customer['resep'][0]['dbl'], 9) .
            _tc($request->customer['resep'][0]['mpd'], 9) .
            _tc($request->customer['resep'][0]['shpv'], 9)
        );
        $printer->setDoubleStrike(false);
        $printer->feed();
        $printer->feed();

        $printer->text( _tl('Frame', 10) . ': ' . _tl($request->detailtransaksi[0]['produk']['namaproduk'], 36));
        $printer->feed();
        $printer->text( _tl('Lensa', 10) . ': ' . _tl($request->detailtransaksi[1]['produk']['namaproduk'], 36));
        $printer->feed();

        $printer->text(_tl('Total', 10) . ': ' . _tl(rupiah($request->subtotal), 36));
        $printer->feed();
        $printer->text(_tl('Dibayar', 10) . ': ' . _tl(rupiah($request->bayar), 36));
        $printer->feed();
        $printer->text(_tl('Sisa', 10) . ': ' . _tl(rupiah($request->sisa), 36));
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();
        $printer->feed();

        $printer->setDoubleStrike(true);
        $printer->setTextSize(2, 2);
        $printer->text(_tc($Cabang,25));
        $printer->setTextSize(1, 1);
        $printer->setDoubleStrike(false);
        $printer->feed();
    
        $printer->text(_tc('Edger', 24) . _tc('Quality Control', 24));
        $printer->feed();
        $printer->feed();
        $printer->feed();
        $printer->feed();
        $printer->text(_tc('(................)', 24) . _tc('(................)', 24));
        $printer->feed();
        $printer->feed();
        $printer->text(_tc('Yg Menyerahkan', 24) . _tc('Penerima', 24));
        $printer->feed();
        $printer->feed();
        $printer->feed();
        $printer->feed();
        $printer->text(_tc('(................)', 24) . _tc('(................)', 24));
        $printer->feed();

        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();
        $printer->setDoubleStrike(true);
        $printer->text(_tl('Keterangan :', self::WIDTH));
        $printer->feed();
        $printer->text(_tl('', self::WIDTH));
        $printer->feed();
        $printer->setDoubleStrike(false);
        $printer->feed();
        $printer->feed();

        //Cut & Close
        $printer->cut();
        $printer->close();
        return $request;
    }

    public function generateHeader($printer, $toko_nama, $toko_alamat_1, $toko_alamat_2, $toko_telp, $toko_wa){
        $printer->setDoubleStrike(true);
        $printer->text(_tc($toko_nama, self::WIDTH));
        $printer->setDoubleStrike(false);
        $printer->feed();
        $printer->text(_tc($toko_alamat_1, self::WIDTH));
        $printer->feed();
        $printer->text(_tc($toko_alamat_2, self::WIDTH));
        $printer->feed();
        $printer->text(_tc("Telp. $toko_telp - WA. $toko_wa", self::WIDTH));
        $printer->feed();
    }

    public function printSuratOrderAppSheet(Request $request){
        //Variable
        $NoNota = $request->Data['NOTA'];
        $Cabang = $request->DataPesanan['Cabang'];
        $RX = $request->DataPesanan['RX'];

        if($Cabang == 'TEMBALANG'){
            $toko_nama = "OPTIK KUSTIN";
            $toko_alamat_1 = "Jl. Sirojudin Raya No.37";
            $toko_alamat_2 = "Undip Tembalang - Kota Semarang";
            $toko_telp = "024-76402637";
            $toko_wa = "0813 7757 2015";
        }        

        //Printer Initialization
        $printerName = "POS-80";
        $connector = new WindowsPrintConnector($printerName);
        $printer = new Printer($connector);

        //Design
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setBarcodeHeight(90);
        $printer->setBarcodeWidth(4);
        $printer->barcode($NoNota, Printer::BARCODE_CODE93);
        $printer->setJustification();
        $printer->feed();
        
        //Design Header
        $this->generateHeaderAppSheet($printer, $toko_nama, $toko_alamat_1, $toko_alamat_2, $toko_telp, $toko_wa);

        //Nota SO
        $printer->feed();
        $printer->text(_tl('No. ', 15) . ": " . $NoNota);
        $printer->feed();
        $printer->text(_tl('Rx. ', 15) . ": " . $RX);
        $printer->feed();
        $printer->text(_tl('Tanggal', 15) . ": " . $request->DataPesanan['TanggalPesan']);
        $printer->feed();
        $printer->text(_tl('Tgl. Selesai', 15) . ": " .  $request->DataPesanan['TanggalSelesai']);
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();
        $printer->text(_tl('Nama', 15) . ": " . $request->DataPesanan['NamaCustomer']);
        $printer->feed();
        $printer->text(_tl('Alamat', 15) . ": " . $request->DataPesanan['Alamat']);
        $printer->feed();
        $printer->text(_tl('Telp ', 15) . ": " . $request->DataPesanan['NoHandphone']);
        $printer->feed();
        $printer->text(_tl('Umur ', 15) . ": " . $request->DataPesanan['Umur']);
        $printer->feed();
        $printer->text(_tl('Jenis Kelamin ', 15) . ": " . $request->DataPesanan['JenisKelamin']);
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '='));
        $printer->feed();

        $printer->setDoubleStrike(true);
        $printer->text(
            _tl('', 3) .
            _tl('SPH', 7) .
            _tc('CYL', 7) .
            _tc('AXIS', 7) .
            _tc('ADD', 7) .
            _tc('PD', 7) .
            _tc('Vis Akhir', 10)
        );
        $printer->setDoubleStrike(false);
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '='));
        $printer->feed();

        $printer->setDoubleStrike(true);
        $printer->text(
            _tl('R', 3) .
            _tl($request->Data['SPH RIGHT'] ?? '', 7) .
            _tc($request->Data['CYL RIGHT'] ?? '', 7) .
            _tc($request->Data['AXIS RIGHT'] ?? '', 7) .
            _tc($request->Data['ADD'] ?? '', 7) .
            _tc($request->Data['PD'], 7) .
            _tc('', 10)
        );
        $printer->setDoubleStrike(false);
        $printer->setDoubleStrike(true);
        $printer->text(
            _tl('L', 3) .
            _tl($request->Data['SPH RIGHT'] ?? '', 7) .
            _tc($request->Data['CYL RIGHT'] ?? '', 7) .
            _tc($request->Data['AXIS RIGHT'] ?? '', 7) .
            _tc('', 7) .
            _tc('', 7) .
            _tc('', 10)
        );
        $printer->setDoubleStrike(false);

        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();

        $printer->text(_tl('Jenis Frame', 15) . ': ' . _tl($request->Data['JENIS FRAME'], 29));
        $printer->feed();
        $printer->text(_tl('Koridor', 15) . ': ' . _tl($request->Data['CORRIDOR'], 29));
        $printer->feed();
        $printer->text(_tl('Visus Balance', 15) . ': ' . _tl($request->Data['VISUS BALANCING'], 29));
        $printer->feed();
        $printer->text(_tl('Duke Elder', 15) . ': ' . _tl($request->Data['DUKE ELDER'], 29));
        $printer->feed();
        $printer->text(_tl('Wrap Angle', 15) . ': ' . _tl($request->Data['WRAP ANGLE'], 29));
        $printer->feed();
        $printer->text(_tl('Pantoskopik', 15) . ': ' . _tl($request->Data['PANTOSCOPIK'], 29));
        $printer->feed();
        $printer->text(_tl('Vertex Distance', 15) . ': ' . _tl($request->Data['VERTEX DISTANCE'], 29));
        $printer->feed();
        $printer->text(_tl('Catatan Resep', 15) . ': ' . _tl($request->Data['CATATAN RESEP'], 29));
        $printer->feed();
        $printer->feed();
        
        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();
        $printer->feed();
        
        $printer->setDoubleStrike(true);
        $printer->text(_tl('PRECAL:', self::WIDTH));
        $printer->setDoubleStrike(false);

        $printer->text(_tl('', self::WIDTH, '='));
        $printer->feed();
        $printer->setDoubleStrike(true);
        $printer->text(
            _tc('A', 9) .
            _tc('B', 9) .
            _tc('DBL', 9) .
            _tc('MPD', 9) .
            _tc('SH/PV', 9)
        );
        $printer->setDoubleStrike(false);
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '='));
        $printer->feed();

        $printer->setDoubleStrike(true);
        $printer->text(
            _tc($request->Data['A'], 9) .
            _tc($request->Data['B'], 9) .
            _tc($request->Data['DBL'], 9) .
            _tc($request->Data['MPD'], 9) .
            _tc($request->Data['SH/PV'], 9)
        );
        $printer->setDoubleStrike(false);
        $printer->feed();
        $printer->feed();

        $printer->text( _tl('Frame', 10) . ': ' . _tl($request->DataPesanan['Frame'], 36));
        $printer->feed();
        $printer->text( _tl('Lensa', 10) . ': ' . _tl($request->DataPesanan['Lensa'], 36));
        $printer->feed();

        $printer->text(_tl('Total', 10) . ': ' . _tl($request->DataPesanan['Total'], 36));
        $printer->feed();
        $printer->text(_tl('Dibayar', 10) . ': ' . _tl($request->DataPesanan['UangMuka'], 36));
        $printer->feed();
        $printer->text(_tl('Sisa', 10) . ': ' . _tl($request->DataPesanan['Sisa'], 36));
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();
        $printer->feed();

        $printer->setDoubleStrike(true);
        $printer->setTextSize(2, 2);
        $printer->text(_tc($Cabang,25));
        $printer->setTextSize(1, 1);
        $printer->setDoubleStrike(false);
        $printer->feed();
    
        $printer->text(_tc('Edger', 24) . _tc('Quality Control', 24));
        $printer->feed();
        $printer->feed();
        $printer->feed();
        $printer->feed();
        $printer->text(_tc('(................)', 24) . _tc('(................)', 24));
        $printer->feed();
        $printer->feed();
        $printer->text(_tc('Yg Menyerahkan', 24) . _tc('Penerima', 24));
        $printer->feed();
        $printer->feed();
        $printer->feed();
        $printer->feed();
        $printer->text(_tc('(................)', 24) . _tc('(................)', 24));
        $printer->feed();

        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();
        $printer->setDoubleStrike(true);
        $printer->text(_tl('Keterangan :', self::WIDTH));
        $printer->feed();
        $printer->text(_tl('', self::WIDTH));
        $printer->feed();
        $printer->setDoubleStrike(false);
        $printer->feed();
        $printer->feed();

        //Cut & Close
        $printer->cut();
        $printer->close();
        return $request;
    }

    public function generateHeaderAppSheet($printer, $toko_nama, $toko_alamat_1, $toko_alamat_2, $toko_telp, $toko_wa){
        $printer->setDoubleStrike(true);
        $printer->text(_tc($toko_nama, self::WIDTH));
        $printer->setDoubleStrike(false);
        $printer->feed();
        $printer->text(_tc($toko_alamat_1, self::WIDTH));
        $printer->feed();
        $printer->text(_tc($toko_alamat_2, self::WIDTH));
        $printer->feed();
        $printer->text(_tc("Telp. $toko_telp - WA. $toko_wa", self::WIDTH));
        $printer->feed();
    }
}
