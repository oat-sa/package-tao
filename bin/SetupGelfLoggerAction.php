<?php

use oat\oatbox\extension\AbstractAction;

class SetupGelfLoggerAction extends AbstractAction
{
    public function __invoke($params)
    {
        $fileContent = <<<EOD
<?php

use Gelf\Publisher;
use Gelf\Transport\UdpTransport;

return new oat\oatbox\log\LoggerService([
    'logger' => [
        'class' => 'oat\oatbox\log\logger\TaoMonolog',
        'options' => [
            'name' => 'package-tao',
            'handlers' => [
                [
                    'class' => 'Monolog\Handler\GelfHandler',
                    'options' => [
                        new Publisher(new UdpTransport('oat-docker-logstash', 12201)),
                        100,
                    ],
                ],
                [
                    'class' => 'Monolog\Handler\StreamHandler',
                    'options' => [
                        '/var/www/html/var/log/tao.log',
                        100,
                    ],
                ],
            ],
        ],
    ],
]);

EOD;
        file_put_contents('config/generis/log.conf.php', $fileContent);
    }
}
