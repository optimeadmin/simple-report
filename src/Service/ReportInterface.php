<?php


namespace Optime\SimpleReport\Bundle\Service;


interface ReportInterface
{

    public function getDataArray();

    public static function getSlug(): string;

}