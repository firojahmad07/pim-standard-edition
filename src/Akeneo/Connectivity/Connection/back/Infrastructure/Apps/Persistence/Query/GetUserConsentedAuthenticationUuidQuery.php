<?php

declare(strict_types=1);

namespace Akeneo\Connectivity\Connection\Infrastructure\Apps\Persistence\Query;

use Akeneo\Connectivity\Connection\Domain\Apps\Persistence\Query\GetUserConsentedAuthenticationUuidQueryInterface;
use Doctrine\DBAL\Connection;

/**
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class GetUserConsentedAuthenticationUuidQuery implements GetUserConsentedAuthenticationUuidQueryInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function execute(int $userId, string $appId): string
    {
        $query = <<<SQL
            SELECT uuid FROM akeneo_connectivity_user_consent
            WHERE user_id = :userId AND app_id = :appId
            SQL;

        $uuid = $this->connection->fetchOne($query, [
            'userId' => $userId,
            'appId' => $appId,
        ]);

        if (!$uuid) {
            throw new \LogicException(sprintf('Consent doesn\'t exist for user %s on app %s', $userId, $appId));
        }

        return $uuid;
    }
}
