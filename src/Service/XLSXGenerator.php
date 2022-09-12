<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 */

namespace Optime\SimpleReport\Service;

use Symfony\Component\HttpFoundation\Response;

class XLSXGenerator
{

    /**
     * @var \XLSXWriter
     */
    private $writer;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $rows;

    /**
     * XLSXGenerator constructor.
     */
    public function __construct()
    {
        $this->writer = new \XLSXWriter();
    }

    public function setFilename($filename)
    {
        $this->filename = strtolower(str_replace(' ', '_', $filename));
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function setRows(&$rows)
    {
        $this->rows = $rows;
    }

    public function generate()
    {
        foreach ($this->headers as $row) {
            $this->writer->writeSheetRow('Sheet1', $row);
        }

        foreach ($this->rows as $row) {
            $this->writer->writeSheetRow('Sheet1', $row);
        }

        $this->rows = null;

        $filename = strtolower(str_replace(' ', '_', $this->filename));

        $excel = $this->writer->writeToString();

        return new Response($excel,
            200,
            array(
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="'.$filename.'.xlsx"',
            ));
    }
}
