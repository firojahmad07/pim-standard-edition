<?php
declare(strict_types=1);

namespace Akeneo\Connectivity\Connection\Infrastructure\Cli;

use Akeneo\Connectivity\Connection\Application\Apps\Command\GenerateAsymmetricKeysCommand;
use Akeneo\Connectivity\Connection\Application\Apps\Command\GenerateAsymmetricKeysHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CreateOpenIdKeysCommand extends Command
{
    protected static $defaultName = 'akeneo:connectivity-connection:openid-keys:create';
    private GenerateAsymmetricKeysHandler $generateAsymmetricKeysHandler;

    public function __construct(GenerateAsymmetricKeysHandler $generateAsymmetricKeysHandler)
    {
        parent::__construct();
        $this->generateAsymmetricKeysHandler = $generateAsymmetricKeysHandler;
    }

    protected function configure(): void
    {
        $this->setDescription('Create or update a public/private key couple to be used to sign openid token and store it into database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->generateAsymmetricKeysHandler->handle(new GenerateAsymmetricKeysCommand());

        return self::SUCCESS;
    }
}
