<?php


namespace Optime\SimpleReport\Bundle\Service;


use Optime\SimpleReport\Bundle\Entity\SimpleReport;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class GenericReportGenerator implements ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $reports;
    /**
     * @var ReportGenerator
     */
    private $generator;

    public function __construct(ContainerInterface $reports, ReportGenerator $generator)
    {

        $this->reports = $reports;
        $this->generator = $generator;
    }

    public static function getSubscribedServices(): array
    {
        return [];
    }

    public function generate(SimpleReport $simpleReport)
    {
        $report = $this->reports->get($simpleReport->getSlug());

        return $this->generator->generateReport($report, $simpleReport);
    }
}