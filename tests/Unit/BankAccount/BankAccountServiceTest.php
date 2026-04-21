<?php

namespace mirrorps\Yii2Taler\Tests\Unit\BankAccount;

use mirrorps\Yii2Taler\BankAccount\BankAccountService;
use mirrorps\Yii2Taler\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\BankAccounts\BankAccountClient;
use Taler\Api\BankAccounts\Dto\AccountAddDetails;
use Taler\Api\BankAccounts\Dto\AccountAddResponse;
use Taler\Api\BankAccounts\Dto\AccountPatchDetails;
use Taler\Api\BankAccounts\Dto\AccountsSummaryResponse;
use Taler\Api\BankAccounts\Dto\BankAccountDetail;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;
use Taler\Taler as TalerClient;

class BankAccountServiceTest extends TestCase
{
    private Taler $taler;
    private BankAccountClient $bankAccountClient;
    private BankAccountService $service;

    protected function setUp(): void
    {
        $this->bankAccountClient = $this->createMock(BankAccountClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('bankAccount')->willReturn($this->bankAccountClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new BankAccountService($this->taler);
    }

    public function testTalerBankAccountsReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->bankAccounts();
        $second = $taler->bankAccounts();

        $this->assertSame($first, $second);
    }

    public function testTalerBankAccountsReturnsBankAccountService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(BankAccountService::class, $taler->bankAccounts());
    }

    public function testCreateAccountDelegatesToClient(): void
    {
        $details = $this->createMock(AccountAddDetails::class);
        $response = $this->createMock(AccountAddResponse::class);

        $this->bankAccountClient
            ->expects($this->once())
            ->method('createAccount')
            ->with($details, [])
            ->willReturn($response);

        $result = $this->service->createAccount($details);

        $this->assertSame($response, $result);
    }

    public function testCreateAccountReturnsChallengeResponse(): void
    {
        $details = $this->createMock(AccountAddDetails::class);
        $response = $this->createMock(ChallengeResponse::class);

        $this->bankAccountClient
            ->expects($this->once())
            ->method('createAccount')
            ->with($details, [])
            ->willReturn($response);

        $result = $this->service->createAccount($details);

        $this->assertSame($response, $result);
    }

    public function testCreateAccountPassesHeaders(): void
    {
        $details = $this->createMock(AccountAddDetails::class);
        $response = $this->createMock(AccountAddResponse::class);
        $headers = ['Authorization' => 'Bearer test'];

        $this->bankAccountClient
            ->expects($this->once())
            ->method('createAccount')
            ->with($details, $headers)
            ->willReturn($response);

        $this->service->createAccount($details, $headers);
    }

    public function testCreateAccountAsyncDelegatesToClient(): void
    {
        $details = $this->createMock(AccountAddDetails::class);

        $this->bankAccountClient
            ->expects($this->once())
            ->method('createAccountAsync')
            ->with($details, [])
            ->willReturn('promise');

        $result = $this->service->createAccountAsync($details);

        $this->assertSame('promise', $result);
    }

    public function testGetAccountsDelegatesToClient(): void
    {
        $response = $this->createMock(AccountsSummaryResponse::class);

        $this->bankAccountClient
            ->expects($this->once())
            ->method('getAccounts')
            ->with([])
            ->willReturn($response);

        $result = $this->service->getAccounts();

        $this->assertSame($response, $result);
    }

    public function testGetAccountsPassesHeaders(): void
    {
        $response = $this->createMock(AccountsSummaryResponse::class);
        $headers = ['X-Custom-Header' => 'value'];

        $this->bankAccountClient
            ->expects($this->once())
            ->method('getAccounts')
            ->with($headers)
            ->willReturn($response);

        $this->service->getAccounts($headers);
    }

    public function testGetAccountsAsyncDelegatesToClient(): void
    {
        $this->bankAccountClient
            ->expects($this->once())
            ->method('getAccountsAsync')
            ->with([])
            ->willReturn('promise');

        $result = $this->service->getAccountsAsync();

        $this->assertSame('promise', $result);
    }

    public function testGetAccountDelegatesToClient(): void
    {
        $response = $this->createMock(BankAccountDetail::class);

        $this->bankAccountClient
            ->expects($this->once())
            ->method('getAccount')
            ->with('abc123', [])
            ->willReturn($response);

        $result = $this->service->getAccount('abc123');

        $this->assertSame($response, $result);
    }

    public function testGetAccountPassesHeaders(): void
    {
        $response = $this->createMock(BankAccountDetail::class);
        $headers = ['X-Custom-Header' => 'value'];

        $this->bankAccountClient
            ->expects($this->once())
            ->method('getAccount')
            ->with('abc123', $headers)
            ->willReturn($response);

        $this->service->getAccount('abc123', $headers);
    }

    public function testGetAccountAsyncDelegatesToClient(): void
    {
        $this->bankAccountClient
            ->expects($this->once())
            ->method('getAccountAsync')
            ->with('abc123', [])
            ->willReturn('promise');

        $result = $this->service->getAccountAsync('abc123');

        $this->assertSame('promise', $result);
    }

    public function testUpdateAccountDelegatesToClient(): void
    {
        $details = new AccountPatchDetails(credit_facade_url: 'https://facade.example');

        $this->bankAccountClient
            ->expects($this->once())
            ->method('updateAccount')
            ->with('abc123', $details, []);

        $this->service->updateAccount('abc123', $details);
    }

    public function testUpdateAccountPassesHeaders(): void
    {
        $details = new AccountPatchDetails(credit_facade_url: 'https://facade.example');
        $headers = ['X-Custom-Header' => 'value'];

        $this->bankAccountClient
            ->expects($this->once())
            ->method('updateAccount')
            ->with('abc123', $details, $headers);

        $this->service->updateAccount('abc123', $details, $headers);
    }

    public function testUpdateAccountAsyncDelegatesToClient(): void
    {
        $details = new AccountPatchDetails(credit_facade_url: 'https://facade.example');

        $this->bankAccountClient
            ->expects($this->once())
            ->method('updateAccountAsync')
            ->with('abc123', $details, [])
            ->willReturn('promise');

        $result = $this->service->updateAccountAsync('abc123', $details);

        $this->assertSame('promise', $result);
    }

    public function testDeleteAccountDelegatesToClient(): void
    {
        $this->bankAccountClient
            ->expects($this->once())
            ->method('deleteAccount')
            ->with('abc123', []);

        $this->service->deleteAccount('abc123');
    }

    public function testDeleteAccountPassesHeaders(): void
    {
        $headers = ['X-Custom-Header' => 'value'];

        $this->bankAccountClient
            ->expects($this->once())
            ->method('deleteAccount')
            ->with('abc123', $headers);

        $this->service->deleteAccount('abc123', $headers);
    }

    public function testDeleteAccountAsyncDelegatesToClient(): void
    {
        $this->bankAccountClient
            ->expects($this->once())
            ->method('deleteAccountAsync')
            ->with('abc123', [])
            ->willReturn('promise');

        $result = $this->service->deleteAccountAsync('abc123');

        $this->assertSame('promise', $result);
    }

    public function testGetBankAccountClientReturnsSameInstance(): void
    {
        $first = $this->service->getBankAccountClient();
        $second = $this->service->getBankAccountClient();

        $this->assertSame($first, $second);
    }

    public function testGetBankAccountClientReturnsBankAccountClient(): void
    {
        $this->assertInstanceOf(BankAccountClient::class, $this->service->getBankAccountClient());
    }
}
