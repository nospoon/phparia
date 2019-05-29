<?php
namespace phparia\Tests\Functional;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use phparia\Client\Phparia;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;


abstract class PhpariaTestCase extends TestCase
{
    /**
     * @var Phparia
     */
    protected $client;

    /**
     * @var string
     */
    protected $ariAddress;

    /**
     * @var string
     */
    protected $amiAddress;

    /**
     * @var string
     */
    protected $dialString;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function setUp(): void
    {
        parent::setUp();

        $configFile = __DIR__ . '/../../config.yml';
        $value = Yaml::parse(file_get_contents($configFile));

        $this->ariAddress = $value['tests']['ari_address'];
        $this->amiAddress = $value['tests']['ami_address'];
        $this->dialString = $value['tests']['dial_string'];

        $this->logger = new Logger('test');
        $logHandler = new StreamHandler("php://output");
        $this->logger->pushHandler($logHandler);

        $this->client = new Phparia($this->logger);
        $this->client->connect($this->ariAddress, $this->amiAddress);
    }

    public function tearDown(): void
    {
        $channels = $this->client->channels()->getChannels();
        foreach ($channels as $channel) {
            $channel->hangup();
        }

        parent::tearDown();
    }
}
