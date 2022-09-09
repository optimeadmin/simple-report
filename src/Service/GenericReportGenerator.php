<?php


namespace Optime\SimpleReport\Service;


use App\Entity\SimpleReport;
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

    public static function getSubscribedServices()
    {
        return [
            'registration-question'=>QuestionUserEventService::class,
            'users-challenges'=>ChallengesReport::class,
            'qa-report'=>QAReport::class,
            'polls-service'=>PollsReport::class,
            'quizes-tries'=>QuizTriesReport::class,
        ];
    }

    public function generate(SimpleReport $simpleReport)
    {
        $report = $this->reports->get($simpleReport->getSlug());

        return $this->generator->generateReport($report, $simpleReport);
    }
}