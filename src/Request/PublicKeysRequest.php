<?php

declare(strict_types=1);

namespace App\Request;

class PublicKeysRequest
{
    private array $emails = [];

    public function getEmails(): array
    {
        return $this->emails;
    }

    /**
     * @param string[] $emails
     */
    public function setEmails(array $emails): void
    {
        $this->emails = $emails;
    }

    public function addEmail(string $email): void
    {
        $this->emails[] = $email;
    }
}
