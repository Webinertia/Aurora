<?php

declare(strict_types=1);

namespace App\Db\TableGateway;

use App\Log\LoggerAwareInterface;
use App\Log\LoggerAwareInterfaceTrait;
use App\Upload\UploadAwareInterface;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Db\TableGateway\TableGatewayInterface;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\InputFilter\InputFilterAwareTrait;
use Laminas\Stdlib\ArrayObject;

use DateTimeImmutable;
use DateTimeZone;

abstract class AbstractGatewayModel extends ArrayObject implements InputFilterAwareInterface, LoggerAwareInterface
{
    use InputFilterAwareTrait;
    use LoggerAwareInterfaceTrait;

    /** @var array<mixed> $config */
    protected $config;
    /** @var InputFilter $inputFilter */
    protected $inputFilter;
    protected $inputFilterClass = InputFilter::class;
    /** @var TableGatewayInterface $gateway */
    protected $gateway;
    /** @var int|string $timeStamp */
    protected $timeStamp;
    /**
     * type can one of ['insert', 'update', 'delete']
     * result will be one of the following,
     * insert => lastInsertId
     * update => affected rows
     * delete => rows deleted
     * @var array<string, mixed> $result
     * */
    protected $result = ['type' => '', 'result' => 0];

    public function __construct(array $data = [], array $config = [])
    {
        parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
        $this->config = $config;
    }

    public function getTimeStamp(bool $useConfigFormat = true): int|string
    {
        $format   = $this->config['server']['db_time_format'] ?? DateTimeImmutable::RFC3339;
        $dateTime = new DateTimeImmutable('now', new DateTimeZone($this->config['server']['time_zone']));
        return $this->timeStamp = $dateTime->format($format);
    }

    public function roundToGivenDigit($number, $digit) {

        $multiplier = 1;
        while ($number < 0.1) {
            $number *= 10;
            $multiplier /= 10;
        }
        while ($number >= 1) {
            $number /= 10;
            $multiplier *= 10;
        }
        return round($number, $digit) * $multiplier;
    }
}
