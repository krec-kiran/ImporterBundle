<?php

namespace Glocal\Bundle\ImporterBundle\Controller;
use Symfony\Component\Process\Process;
use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local as LocalAdapter;
use Gaufrette\File;

class ImporterController {

    public function __construct($format, $size, $quality, $location) {
        $this->size = $size;
        $this->quality = $quality;
        $this->format = $format;
        $this->location = $location;
    }

    public function convertPdfToImage($srcFile) {

        $destFile = basename($srcFile, ".pdf");
        $destFile = $this->location . '/' . $destFile . '.' . $this->format;

        $process = new Process("/usr/local/bin/convert  $srcFile -quality $this->quality -resize $this->size $destFile");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        print $process->getOutput();
    }

    public function convertDocToPdf($srcFile) {

        $process = new Process("/Users/kirankotresh/unoconv/unoconv --timeout=300 $srcFile");
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        print $process->getOutput();
    }

    public function pdfStringExtract($srcFile) {
        $adapter = new LocalAdapter('/Users/kirankotresh');
        $filesystem = new Filesystem($adapter);

        $file = new File($srcFile, $filesystem);

        if ($filesystem->has(basename($srcFile))) {
            $demo = new \Smalot\PdfParser\Parser();
            $pdf = $demo->parseFile($file->getName());
            $text = $pdf->getText();
            $words = preg_split('/ /', $text);
          //  print_r($words);
        }
    }

}
