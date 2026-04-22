<?php

namespace mirrorps\Yii2Taler\Tests\Unit\OtpDevices;

use mirrorps\Yii2Taler\OtpDevices\OtpDevicesService;
use mirrorps\Yii2Taler\Taler;
use PHPUnit\Framework\TestCase;
use Taler\Api\OtpDevices\Dto\GetOtpDeviceRequest;
use Taler\Api\OtpDevices\Dto\OtpDeviceAddDetails;
use Taler\Api\OtpDevices\Dto\OtpDeviceDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicePatchDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicesSummaryResponse;
use Taler\Api\OtpDevices\OtpDevicesClient;
use Taler\Taler as TalerClient;

class OtpDevicesServiceTest extends TestCase
{
    private Taler $taler;
    private OtpDevicesClient $otpDevicesClient;
    private OtpDevicesService $service;

    protected function setUp(): void
    {
        $this->otpDevicesClient = $this->createMock(OtpDevicesClient::class);

        $talerClient = $this->createMock(TalerClient::class);
        $talerClient->method('otpDevices')->willReturn($this->otpDevicesClient);

        $this->taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $this->taler->method('getClient')->willReturn($talerClient);

        $this->service = new OtpDevicesService($this->taler);
    }

    public function testTalerOtpDevicesReturnsSameInstance(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $first = $taler->otpDevices();
        $second = $taler->otpDevices();

        $this->assertSame($first, $second);
    }

    public function testTalerOtpDevicesReturnsService(): void
    {
        $taler = $this->getMockBuilder(Taler::class)
            ->setConstructorArgs([['baseUrl' => 'https://example.com']])
            ->onlyMethods(['getClient'])
            ->getMock();

        $talerClient = $this->createMock(TalerClient::class);
        $taler->method('getClient')->willReturn($talerClient);

        $this->assertInstanceOf(OtpDevicesService::class, $taler->otpDevices());
    }

    public function testCreateOtpDeviceDelegatesToClient(): void
    {
        $details = new OtpDeviceAddDetails(
            otp_device_id: 'device-1',
            otp_device_description: 'Front desk',
            otp_key: 'JBSWY3DPEHPK3PXP',
            otp_algorithm: 'NONE'
        );

        $this->otpDevicesClient
            ->expects($this->once())
            ->method('createOtpDevice')
            ->with($details, []);

        $this->service->createOtpDevice($details);
    }

    public function testCreateOtpDeviceAsyncDelegatesToClient(): void
    {
        $details = new OtpDeviceAddDetails(
            otp_device_id: 'device-2',
            otp_device_description: 'Warehouse terminal',
            otp_key: 'JBSWY3DPEHPK3PXP',
            otp_algorithm: 1
        );

        $this->otpDevicesClient
            ->expects($this->once())
            ->method('createOtpDeviceAsync')
            ->with($details, [])
            ->willReturn('promise');

        $result = $this->service->createOtpDeviceAsync($details);

        $this->assertSame('promise', $result);
    }

    public function testUpdateOtpDeviceDelegatesToClient(): void
    {
        $details = new OtpDevicePatchDetails(
            otp_device_description: 'Updated front desk',
            otp_algorithm: 'TOTP_WITHOUT_PRICE'
        );

        $this->otpDevicesClient
            ->expects($this->once())
            ->method('updateOtpDevice')
            ->with('device-1', $details, []);

        $this->service->updateOtpDevice('device-1', $details);
    }

    public function testUpdateOtpDeviceAsyncDelegatesToClient(): void
    {
        $details = new OtpDevicePatchDetails(
            otp_device_description: 'Updated warehouse',
            otp_algorithm: 2
        );

        $this->otpDevicesClient
            ->expects($this->once())
            ->method('updateOtpDeviceAsync')
            ->with('device-2', $details, [])
            ->willReturn('promise');

        $result = $this->service->updateOtpDeviceAsync('device-2', $details);

        $this->assertSame('promise', $result);
    }

    public function testGetOtpDevicesDelegatesToClient(): void
    {
        $summary = $this->createMock(OtpDevicesSummaryResponse::class);

        $this->otpDevicesClient
            ->expects($this->once())
            ->method('getOtpDevices')
            ->with([])
            ->willReturn($summary);

        $result = $this->service->getOtpDevices();

        $this->assertSame($summary, $result);
    }

    public function testGetOtpDevicesAsyncDelegatesToClient(): void
    {
        $this->otpDevicesClient
            ->expects($this->once())
            ->method('getOtpDevicesAsync')
            ->with([])
            ->willReturn('promise');

        $result = $this->service->getOtpDevicesAsync();

        $this->assertSame('promise', $result);
    }

    public function testGetOtpDeviceDelegatesToClient(): void
    {
        $details = $this->createMock(OtpDeviceDetails::class);
        $request = new GetOtpDeviceRequest(price: 'KUDOS:1.00');

        $this->otpDevicesClient
            ->expects($this->once())
            ->method('getOtpDevice')
            ->with('device-1', $request, [])
            ->willReturn($details);

        $result = $this->service->getOtpDevice('device-1', $request);

        $this->assertSame($details, $result);
    }

    public function testGetOtpDeviceAsyncDelegatesToClient(): void
    {
        $request = new GetOtpDeviceRequest(faketime: 1711111111);

        $this->otpDevicesClient
            ->expects($this->once())
            ->method('getOtpDeviceAsync')
            ->with('device-2', $request, [])
            ->willReturn('promise');

        $result = $this->service->getOtpDeviceAsync('device-2', $request);

        $this->assertSame('promise', $result);
    }

    public function testDeleteOtpDeviceDelegatesToClient(): void
    {
        $this->otpDevicesClient
            ->expects($this->once())
            ->method('deleteOtpDevice')
            ->with('device-3', []);

        $this->service->deleteOtpDevice('device-3');
    }

    public function testDeleteOtpDeviceAsyncDelegatesToClient(): void
    {
        $this->otpDevicesClient
            ->expects($this->once())
            ->method('deleteOtpDeviceAsync')
            ->with('device-4', [])
            ->willReturn('promise');

        $result = $this->service->deleteOtpDeviceAsync('device-4');

        $this->assertSame('promise', $result);
    }

    public function testGetOtpDevicesClientReturnsSameInstance(): void
    {
        $first = $this->service->getOtpDevicesClient();
        $second = $this->service->getOtpDevicesClient();

        $this->assertSame($first, $second);
    }

    public function testGetOtpDevicesClientReturnsOtpDevicesClient(): void
    {
        $this->assertInstanceOf(OtpDevicesClient::class, $this->service->getOtpDevicesClient());
    }
}
