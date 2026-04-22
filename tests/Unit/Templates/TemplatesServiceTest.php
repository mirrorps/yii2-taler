<?php

namespace mirrorps\Yii2Taler\Tests\Unit\Templates;

use mirrorps\Yii2Taler\Taler;
use mirrorps\Yii2Taler\Templates\TemplatesService;
use PHPUnit\Framework\TestCase;
use Taler\Api\Templates\Dto\TemplateAddDetails;
use Taler\Api\Templates\Dto\TemplateDetails;
use Taler\Api\Templates\Dto\TemplatePatchDetails;
use Taler\Api\Templates\Dto\TemplatesSummaryResponse;
use Taler\Api\Templates\TemplatesClient;
use Taler\Taler as TalerClient;

class TemplatesServiceTest extends TestCase
{
    private Taler $taler;
    private TemplatesClient $templatesClient;
    private TemplatesService $service;

    protected function setUp(): void
    {
        $this->templatesClient = $this->createMock(TemplatesClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('templates')->willReturn($this->templatesClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new TemplatesService($this->taler);
    }

    public function testTalerTemplatesReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->templates();
        $second = $taler->templates();

        $this->assertSame($first, $second);
    }

    public function testTalerTemplatesReturnsTemplatesService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(TemplatesService::class, $taler->templates());
    }

    public function testCreateTemplateDelegatesToClient(): void
    {
        $details = $this->createMock(TemplateAddDetails::class);

        $this->templatesClient
            ->expects($this->once())
            ->method('createTemplate')
            ->with($details, []);

        $this->service->createTemplate($details);
    }

    public function testCreateTemplatePassesHeaders(): void
    {
        $details = $this->createMock(TemplateAddDetails::class);
        $headers = ['X-Custom' => 'value'];

        $this->templatesClient
            ->expects($this->once())
            ->method('createTemplate')
            ->with($details, $headers);

        $this->service->createTemplate($details, $headers);
    }

    public function testCreateTemplateAsyncDelegatesToClient(): void
    {
        $details = $this->createMock(TemplateAddDetails::class);

        $this->templatesClient
            ->expects($this->once())
            ->method('createTemplateAsync')
            ->with($details, [])
            ->willReturn('promise');

        $result = $this->service->createTemplateAsync($details);

        $this->assertSame('promise', $result);
    }

    public function testUpdateTemplateDelegatesToClient(): void
    {
        $details = $this->createMock(TemplatePatchDetails::class);

        $this->templatesClient
            ->expects($this->once())
            ->method('updateTemplate')
            ->with('tpl-1', $details, []);

        $this->service->updateTemplate('tpl-1', $details);
    }

    public function testUpdateTemplateAsyncDelegatesToClient(): void
    {
        $details = $this->createMock(TemplatePatchDetails::class);

        $this->templatesClient
            ->expects($this->once())
            ->method('updateTemplateAsync')
            ->with('tpl-1', $details, [])
            ->willReturn('promise');

        $result = $this->service->updateTemplateAsync('tpl-1', $details);

        $this->assertSame('promise', $result);
    }

    public function testGetTemplatesDelegatesToClient(): void
    {
        $response = $this->createMock(TemplatesSummaryResponse::class);

        $this->templatesClient
            ->expects($this->once())
            ->method('getTemplates')
            ->with([])
            ->willReturn($response);

        $result = $this->service->getTemplates();

        $this->assertSame($response, $result);
    }

    public function testGetTemplatesAsyncDelegatesToClient(): void
    {
        $this->templatesClient
            ->expects($this->once())
            ->method('getTemplatesAsync')
            ->with([])
            ->willReturn('promise');

        $result = $this->service->getTemplatesAsync();

        $this->assertSame('promise', $result);
    }

    public function testGetTemplateDelegatesToClient(): void
    {
        $response = $this->createMock(TemplateDetails::class);

        $this->templatesClient
            ->expects($this->once())
            ->method('getTemplate')
            ->with('tpl-1', [])
            ->willReturn($response);

        $result = $this->service->getTemplate('tpl-1');

        $this->assertSame($response, $result);
    }

    public function testGetTemplatePassesHeaders(): void
    {
        $response = $this->createMock(TemplateDetails::class);
        $headers = ['Authorization' => 'Bearer test'];

        $this->templatesClient
            ->expects($this->once())
            ->method('getTemplate')
            ->with('tpl-1', $headers)
            ->willReturn($response);

        $this->service->getTemplate('tpl-1', $headers);
    }

    public function testGetTemplateAsyncDelegatesToClient(): void
    {
        $this->templatesClient
            ->expects($this->once())
            ->method('getTemplateAsync')
            ->with('tpl-1', [])
            ->willReturn('promise');

        $result = $this->service->getTemplateAsync('tpl-1');

        $this->assertSame('promise', $result);
    }

    public function testDeleteTemplateDelegatesToClient(): void
    {
        $this->templatesClient
            ->expects($this->once())
            ->method('deleteTemplate')
            ->with('tpl-1', []);

        $this->service->deleteTemplate('tpl-1');
    }

    public function testDeleteTemplateAsyncDelegatesToClient(): void
    {
        $this->templatesClient
            ->expects($this->once())
            ->method('deleteTemplateAsync')
            ->with('tpl-1', [])
            ->willReturn('promise');

        $result = $this->service->deleteTemplateAsync('tpl-1');

        $this->assertSame('promise', $result);
    }

    public function testGetTemplatesClientReturnsSameInstance(): void
    {
        $first = $this->service->getTemplatesClient();
        $second = $this->service->getTemplatesClient();

        $this->assertSame($first, $second);
    }

    public function testGetTemplatesClientReturnsTemplatesClient(): void
    {
        $this->assertInstanceOf(TemplatesClient::class, $this->service->getTemplatesClient());
    }
}
