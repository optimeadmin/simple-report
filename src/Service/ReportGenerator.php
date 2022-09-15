<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 */

namespace Optime\SimpleReport\Bundle\Service;


use Optime\SimpleReport\Bundle\Entity\SimpleReport;
use Optime\SimpleReport\Bundle\Repository\SimpleReportRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

#[AutoconfigureTag("monolog.logger", ['channel' => 'report'])]
class ReportGenerator
{
    /**
     * @var SimpleReportRepository
     */
    private $simpleReportRepository;

    /**
     * @var SimpleReport
     */
    private $simpleReport;

    /**
     * @var XLSXGenerator
     */
    private $xlsxGenerator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ReportGenerator constructor.
     */
    public function __construct(SimpleReportRepository $simpleReportRepository,
                                XLSXGenerator $xlsxGenerator,
                                EntityManagerInterface $entityManager
                                )
    {
        $this->simpleReportRepository = $simpleReportRepository;
        $this->xlsxGenerator = $xlsxGenerator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param $slug
     * @return $this
     */
    protected function getSimpleReportBySlug($slug)
    {
        $this->simpleReport = $this->simpleReportRepository->findOneBy(['slug'=>$slug,'active'=>SimpleReport::ACTIVE]);

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function getQueryResult($query): \Generator
    {
        $parameters = [];

        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare($query);

        return $stmt->executeQuery($parameters)->iterateAssociative();

    }

    public function generateReportFromSlug($slug)
    {
        $this->getSimpleReportBySlug($slug);

        if(is_null($this->simpleReport)) {
            return false;
        }

        $headers = $this->getExcelHeaders($this->simpleReport);

        $rows = $this->getQueryResult($this->simpleReport->getQueryString());

        if($rows->valid()) {
            $headers[] = array_keys($rows->current());
        }else{
            $rows = [['No results for this report']];
        }

        $this->xlsxGenerator->setHeaders($headers);
        $this->xlsxGenerator->setRows($rows);
        unset($rows);

        $this->xlsxGenerator->setFilename($this->simpleReport->getName());

        return $this->xlsxGenerator->generate();

    }

    protected function searchTagIntoQueryString($tag)
    {
        $tag = ":".$tag;

        $queryString = $this->simpleReport->getQueryString();

        return strpos($queryString, $tag);
    }

    public function getActiveReports()
    {
        $parameters = ['active'=>SimpleReport::ACTIVE];

        return $this->simpleReportRepository->findBy($parameters);
    }

    public function generateReport(ReportInterface $report, SimpleReport $simpleReport)
    {
        $data = $report->getDataArray();

        $headers = $this->getExcelHeaders($simpleReport);

        if($data) {
            $headers[] = array_keys($data[0]);
            $rows = $data;
        }else{
            $rows[] = ['No results for this report'];
        }

        $this->xlsxGenerator->setHeaders($headers);
        $this->xlsxGenerator->setRows($rows);

        $this->xlsxGenerator->setFilename($simpleReport->getName());

        return $this->xlsxGenerator->generate();
    }

    public function getExcelHeaders(SimpleReport $simpleReport)
    {

        $headers[] = ['Report Name', $simpleReport->getName()];
        $headers[] = ['Download Date', date('m-d-Y h:i a')];
        $headers[] = [' ', ' '];

        return $headers;
    }

}
