<?php

declare(strict_types=1);

namespace Akeneo\Connectivity\Connection\Domain\Apps\Model;

/**
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
final class ConnectedApp
{
    private string $id;
    private string $name;
    private string $connectionCode;
    private string $logo;
    private string $author;
    /** @var string[] $scopes */
    private array $scopes;
    private string $userGroupName;
    /** @var string[] $categories */
    private array $categories;
    private bool $certified;
    private ?string $partner;

    /**
     * @param string[] $scopes
     * @param string[] $categories
     */
    public function __construct(
        string $id,
        string $name,
        array $scopes,
        string $connectionCode,
        string $logo,
        string $author,
        string $userGroupName,
        array $categories = [],
        bool $certified = false,
        ?string $partner = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->scopes = $scopes;
        $this->connectionCode = $connectionCode;
        $this->logo = $logo;
        $this->author = $author;
        $this->userGroupName = $userGroupName;
        $this->categories = $categories;
        $this->certified = $certified;
        $this->partner = $partner;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getConnectionCode(): string
    {
        return $this->connectionCode;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getUserGroupName(): string
    {
        return $this->userGroupName;
    }

    public function getPartner(): ?string
    {
        return $this->partner;
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function isCertified(): bool
    {
        return $this->certified;
    }

    /**
     * @return array{
     *  id: string,
     *  name: string,
     *  scopes: array<string>,
     *  connection_code: string,
     *  logo: string,
     *  author: string,
     *  user_group_name: string,
     *  categories: array<string>,
     *  certified: bool,
     *  partner: string|null
     * }
     */
    public function normalize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'scopes' => $this->scopes,
            'connection_code' => $this->connectionCode,
            'logo' => $this->logo,
            'author' => $this->author,
            'user_group_name' => $this->userGroupName,
            'categories' => $this->categories,
            'certified' => $this->certified,
            'partner' => $this->partner,
        ];
    }
}
