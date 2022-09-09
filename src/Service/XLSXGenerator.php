<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 */

namespace Optime\SimpleReport\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag("monolog.logger", ['channel' => 'report'])]
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
     * @var Stopwatch
     */
    private Stopwatch $stopwatch;
    /**
     * @var LoggerInterface|null
     */
    private LoggerInterface|null $logger;

    /**
     * XLSXGenerator constructor.
     * @param Stopwatch $stopwatch
     * @param LoggerInterface|null $logger
     */
    public function __construct(Stopwatch $stopwatch, LoggerInterface $logger = null)
    {
        $this->writer = new \XLSXWriter();
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;
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
        $this->stopwatch->start('xlsxReport');

        if(!is_null($this->logger)) {
            $this->logger->info('Comenzando el generate',[
                'info'=>(string) $this->stopwatch->lap('xlsxReport'),
                'memory_limit'=>ini_get('memory_limit')
            ]);
        }

        //$data = array_merge($this->headers, $this->rows);
        //$data = &$this->rows;
        //array_unshift($data, $this->headers);
        foreach ($this->headers as $row) {
            $this->writer->writeSheetRow('Sheet1', $row);
        }
        //$this->writer->writeSheetRow('Sheet1', $this->headers);
        //$this->headers = null;
        //$this->rows = null;

        if(!is_null($this->logger)) {
            $this->logger->info('Luego de array_unshift',[
                'info'=>(string) $this->stopwatch->lap('xlsxReport'),
            ]);
        }

        foreach ($this->rows as $row) {
            $this->writer->writeSheetRow('Sheet1', $row);
        }

        //$this->writer->writeSheet($data);

        if(!is_null($this->logger)) {
            $this->logger->info('Luego de writeSheet',[
                'info'=>(string) $this->stopwatch->lap('xlsxReport')
            ]);
        }

        $this->rows = null;
        //unset($data);

        $filename = strtolower(str_replace(' ', '_', $this->filename));

        $excel = $this->writer->writeToString();

        if(!is_null($this->logger)) {
            $this->logger->info('Luego de generar excel',[
                'info'=>(string) $this->stopwatch->stop('xlsxReport')
            ]);
        }

        return new Response($excel,
            200,
            array(
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="'.$filename.'.xlsx"',
            ));
    }
}
