<?php

namespace mirrorps\Yii2Taler\BankAccount;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\BankAccounts\BankAccountClient;
use Taler\Api\BankAccounts\Dto\AccountAddDetails;
use Taler\Api\BankAccounts\Dto\AccountAddResponse;
use Taler\Api\BankAccounts\Dto\AccountPatchDetails;
use Taler\Api\BankAccounts\Dto\AccountsSummaryResponse;
use Taler\Api\BankAccounts\Dto\BankAccountDetail;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;
use yii\base\Component;

/**
 * BankAccountService - Yii2 service component for GNU Taler Bank Accounts API.
 *
 * Provides a thin, testable wrapper over the underlying BankAccountClient.
 */
class BankAccountService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var BankAccountClient|null Lazily resolved BankAccountClient */
    private ?BankAccountClient $_bankAccountClient = null;

    /**
     * @param Taler $taler The parent Taler component
     * @param array $config Yii2 component config
     */
    public function __construct(Taler $taler, array $config = [])
    {
        $this->_taler = $taler;
        parent::__construct($config);
    }

    /**
     * Returns the underlying BankAccountClient, creating it on first access.
     */
    public function getBankAccountClient(): BankAccountClient
    {
        if ($this->_bankAccountClient === null) {
            $this->_bankAccountClient = $this->_taler->getClient()->bankAccount();
        }

        return $this->_bankAccountClient;
    }

    /**
     * Create a bank account for the merchant instance.
     *
     * @param AccountAddDetails $details
     * @param array<string, string> $headers
     * @return AccountAddResponse|ChallengeResponse|array
     */
    public function createAccount(
        AccountAddDetails $details,
        array $headers = []
    ): AccountAddResponse|ChallengeResponse|array {
        return $this->getBankAccountClient()->createAccount($details, $headers);
    }

    /**
     * Create a bank account for the merchant instance (async).
     *
     * @param AccountAddDetails $details
     * @param array<string, string> $headers
     * @return mixed
     */
    public function createAccountAsync(AccountAddDetails $details, array $headers = []): mixed
    {
        return $this->getBankAccountClient()->createAccountAsync($details, $headers);
    }

    /**
     * Get all bank accounts for the merchant instance.
     *
     * @param array<string, string> $headers
     * @return AccountsSummaryResponse|array
     */
    public function getAccounts(array $headers = []): AccountsSummaryResponse|array
    {
        return $this->getBankAccountClient()->getAccounts($headers);
    }

    /**
     * Get all bank accounts for the merchant instance (async).
     *
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getAccountsAsync(array $headers = []): mixed
    {
        return $this->getBankAccountClient()->getAccountsAsync($headers);
    }

    /**
     * Get a specific bank account by h_wire.
     *
     * @param string $hWire
     * @param array<string, string> $headers
     * @return BankAccountDetail|array
     */
    public function getAccount(string $hWire, array $headers = []): BankAccountDetail|array
    {
        return $this->getBankAccountClient()->getAccount($hWire, $headers);
    }

    /**
     * Get a specific bank account by h_wire (async).
     *
     * @param string $hWire
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getAccountAsync(string $hWire, array $headers = []): mixed
    {
        return $this->getBankAccountClient()->getAccountAsync($hWire, $headers);
    }

    /**
     * Update a bank account by h_wire.
     *
     * @param string $hWire
     * @param AccountPatchDetails $details
     * @param array<string, string> $headers
     * @return void
     */
    public function updateAccount(string $hWire, AccountPatchDetails $details, array $headers = []): void
    {
        $this->getBankAccountClient()->updateAccount($hWire, $details, $headers);
    }

    /**
     * Update a bank account by h_wire (async).
     *
     * @param string $hWire
     * @param AccountPatchDetails $details
     * @param array<string, string> $headers
     * @return mixed
     */
    public function updateAccountAsync(string $hWire, AccountPatchDetails $details, array $headers = []): mixed
    {
        return $this->getBankAccountClient()->updateAccountAsync($hWire, $details, $headers);
    }

    /**
     * Delete a bank account by h_wire.
     *
     * @param string $hWire
     * @param array<string, string> $headers
     * @return void
     */
    public function deleteAccount(string $hWire, array $headers = []): void
    {
        $this->getBankAccountClient()->deleteAccount($hWire, $headers);
    }

    /**
     * Delete a bank account by h_wire (async).
     *
     * @param string $hWire
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteAccountAsync(string $hWire, array $headers = []): mixed
    {
        return $this->getBankAccountClient()->deleteAccountAsync($hWire, $headers);
    }
}
