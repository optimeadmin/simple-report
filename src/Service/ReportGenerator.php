<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 */

namespace Optime\SimpleReport\Service;


use App\Entity\SimpleReport;
use App\Repository\SimpleReportRepository;
use App\Services\Event\Provider\CurrentEventProvider;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

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
     * @var CurrentEventProvider
     */
    private $eventProvider;
    /**
     * @var Stopwatch
     */
    private Stopwatch $stopwatch;
    /**
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger;

    /**
     * ReportGenerator constructor.
     */
    public function __construct(SimpleReportRepository $simpleReportRepository,
                                XLSXGenerator $xlsxGenerator,
                                EntityManagerInterface $entityManager,
                                CurrentEventProvider $eventProvider,
                                Stopwatch $stopwatch,
                                LoggerInterface $logger = null)
    {
        $this->simpleReportRepository = $simpleReportRepository;
        $this->xlsxGenerator = $xlsxGenerator;
        $this->entityManager = $entityManager;
        $this->eventProvider = $eventProvider;
        $this->stopwatch = $stopwatch;
        $this->logger = $logger;
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
        if ($this->searchTagIntoQueryString('event_id')) {
            $event = $this->eventProvider->getFromCurrentRequest();
            $parameters['event_id'] = $event->getId();
        }

        $conn = $this->entityManager->getConnection();

        $stmt = $conn->prepare($query);

        return $stmt->executeQuery($parameters)->iterateAssociative();

    }

    public function generateReportFromSlug($slug)
    {
        $this->stopwatch->start('slugReport');

        $this->getSimpleReportBySlug($slug);

        $headers = $this->getExcelHeaders($this->simpleReport);

        if(!is_null($this->logger)) {
            $this->logger->info('Validando memoria antes de la consulta',[
                'info'=>(string) $this->stopwatch->lap('slugReport'),
                'memory_limit'=>ini_get('memory_limit')
            ]);
        }
        $rows = $this->getQueryResult($this->simpleReport->getQueryString());

        if(!is_null($this->logger)) {
            $this->logger->info('Validando memoria despues de la consulta',[
                'info'=>(string) $this->stopwatch->lap('slugReport'),
            ]);
        }

        if($rows->valid()) {
            $headers[] = array_keys($rows->current());
        }else{
            $rows = [['No results for this report']];
        }

        $this->xlsxGenerator->setHeaders($headers);
        $this->xlsxGenerator->setRows($rows);
        if(!is_null($this->logger)) {
            $this->logger->info('Seteo de rows',[
                'info'=>(string) $this->stopwatch->lap('slugReport')
            ]);
        }
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
        $event = $this->eventProvider->getFromCurrentRequest();

        $headers[] = ['Client Name', $event->getClient()->getName()];
        $headers[] = ['Event Name', $event->getName()];
        $headers[] = ['Report Name', $simpleReport->getName()];
        $headers[] = ['Download Date', date('m-d-Y h:i a')];
        $headers[] = [' ', ' '];

        return $headers;
    }

}
