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
        //Variable
        $NoNota = $request->Data['NOTA'];
        $Cabang = $request->Data['STORE'];
        $RX = $request->Data['RX'];

        if($Cabang == 'TEMBALANG'){
            $toko_nama = "OPTIK KUSTIN";
            $toko_alamat_1 = "Jl. Sirojudin Raya No.37";
            $toko_alamat_2 = "Undip Tembalang - Kota Semarang";
            $toko_telp = "024-76402637";
            $toko_wa = "0813 7757 2015";
        }        

        //Printer Initialization
        $printerName = env('PRINTER_NAME');
        $connector = new WindowsPrintConnector($printerName);
        $printer = new Printer($connector);

        //Design
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setBarcodeHeight(70);
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
        $printer->text(_tl('Tanggal', 15) . ": " . $request->Data['TGL PESAN']);
        $printer->feed();
        $printer->text(_tl('Tgl. Selesai', 15) . ": " .  $request->Data['TGL SELESAI']);
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();
        $printer->text(_tl('Nama', 15) . ": " . $request->Data['NAMA CUSTOMER']);
        $printer->feed();
        $printer->text(_tl('Alamat', 15) . ": " . $request->Data['ALAMAT']);
        $printer->feed();
        $printer->text(_tl('Telp ', 15) . ": " . $request->Data['NO. TELP']);
        $printer->feed();
        $printer->text(_tl('Umur ', 15) . ": " . $request->Data['Umur']);
        $printer->feed();
        $printer->text(_tl('Jenis Kelamin ', 15) . ": " . $request->Data['JENIS KELAMIN']);
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

        $printer->text(_tl('Jenis Frame', 15) . ': ' . _tl($request->Data['JENIS FRAME'], 5) . _tl('Wrap Angle', 15) . ': ' . _tl($request->Data['WRAP ANGLE'], 5));
        $printer->feed();
        $printer->text(_tl('Koridor', 15) . ': ' . _tl($request->Data['CORRIDOR'], 5) . _tl('Pantoskopik', 15) . ': ' . _tl($request->Data['PANTOSCOPIK'], 5));
        $printer->feed();
        $printer->text(_tl('Visus Balance', 15) . ': ' . _tl($request->Data['VISUS BALANCING'], 5) . _tl('Vertex Distance', 15) . ': ' . _tl($request->Data['VERTEX DISTANCE'], 5));
        $printer->feed();
        $printer->text(_tl('Duke Elder', 15) . ': ' . _tl($request->Data['DUKE ELDER'], 5) . _tl('Catatan Resep', 15) . ': ' . _tl($request->Data['CATATAN RESEP'], 5));
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

        $printer->text( _tl('Frame', 10) . ': ' . _tl($request->Data['FRAME'], 36));
        $printer->feed();
        $printer->text( _tl('Lensa', 10) . ': ' . _tl($request->Data['LENSA'], 36));
        $printer->feed();

        $printer->text(_tl('Total', 10) . ': ' . _tl($request->Data['TOTAL'], 36));
        $printer->feed();
        $printer->text(_tl('Dibayar', 10) . ': ' . _tl($request->Data['UANGMUKA'], 36));
        $printer->feed();
        $printer->text(_tl('Sisa', 10) . ': ' . _tl($request->Data['SISA'], 36));
        $printer->feed();
        $printer->text(_tl('', self::WIDTH, '-'));
        $printer->feed();
        $printer->feed();

        $printer->setDoubleStrike(true);
        $printer->text(_tc($request->Data['STORE'], self::WIDTH));
        $printer->setDoubleStrike(false);
        $printer->feed();
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
        $printer->feed();
        $printer->feed();
        $printer->feed();

        //Cut & Close
        $printer->cut();
        $printer->close();
        return $request;
    }

    public function generateHeader($printer, $toko_nama, $toko_alamat_1, $toko_alamat_2, $toko_telp, $toko_wa)
    {
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
