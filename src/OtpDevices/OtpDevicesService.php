<?php

namespace mirrorps\Yii2Taler\OtpDevices;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\OtpDevices\Dto\GetOtpDeviceRequest;
use Taler\Api\OtpDevices\Dto\OtpDeviceAddDetails;
use Taler\Api\OtpDevices\Dto\OtpDeviceDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicePatchDetails;
use Taler\Api\OtpDevices\Dto\OtpDevicesSummaryResponse;
use Taler\Api\OtpDevices\OtpDevicesClient;
use yii\base\Component;

/**
 * OtpDevicesService - Yii2 service component for GNU Taler OTP Devices API.
 *
 * Provides a thin, testable wrapper over the underlying OtpDevicesClient.
 */
class OtpDevicesService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var OtpDevicesClient|null Lazily resolved OtpDevicesClient */
    private ?OtpDevicesClient $_otpDevicesClient = null;

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
     * Returns the underlying OtpDevicesClient, creating it on first access.
     */
    public function getOtpDevicesClient(): OtpDevicesClient
    {
        if ($this->_otpDevicesClient === null) {
            $this->_otpDevicesClient = $this->_taler->getClient()->otpDevices();
        }

        return $this->_otpDevicesClient;
    }

    /**
     * Create OTP device.
     *
     * @param OtpDeviceAddDetails $details
     * @param array<string, string> $headers
     * @return void
     */
    public function createOtpDevice(OtpDeviceAddDetails $details, array $headers = []): void
    {
        $this->getOtpDevicesClient()->createOtpDevice($details, $headers);
    }

    /**
     * Create OTP device asynchronously.
     *
     * @param OtpDeviceAddDetails $details
     * @param array<string, string> $headers
     * @return mixed
     */
    public function createOtpDeviceAsync(OtpDeviceAddDetails $details, array $headers = []): mixed
    {
        return $this->getOtpDevicesClient()->createOtpDeviceAsync($details, $headers);
    }

    /**
     * Update OTP device.
     *
     * @param string $deviceId
     * @param OtpDevicePatchDetails $details
     * @param array<string, string> $headers
     * @return void
     */
    public function updateOtpDevice(string $deviceId, OtpDevicePatchDetails $details, array $headers = []): void
    {
        $this->getOtpDevicesClient()->updateOtpDevice($deviceId, $details, $headers);
    }

    /**
     * Update OTP device asynchronously.
     *
     * @param string $deviceId
     * @param OtpDevicePatchDetails $details
     * @param array<string, string> $headers
     * @return mixed
     */
    public function updateOtpDeviceAsync(string $deviceId, OtpDevicePatchDetails $details, array $headers = []): mixed
    {
        return $this->getOtpDevicesClient()->updateOtpDeviceAsync($deviceId, $details, $headers);
    }

    /**
     * List OTP devices.
     *
     * @param array<string, string> $headers
     * @return OtpDevicesSummaryResponse|array<string, mixed>
     */
    public function getOtpDevices(array $headers = []): OtpDevicesSummaryResponse|array
    {
        return $this->getOtpDevicesClient()->getOtpDevices($headers);
    }

    /**
     * List OTP devices asynchronously.
     *
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getOtpDevicesAsync(array $headers = []): mixed
    {
        return $this->getOtpDevicesClient()->getOtpDevicesAsync($headers);
    }

    /**
     * Get a single OTP device.
     *
     * @param string $deviceId
     * @param GetOtpDeviceRequest|null $request
     * @param array<string, string> $headers
     * @return OtpDeviceDetails|array<string, mixed>
     */
    public function getOtpDevice(
        string $deviceId,
        ?GetOtpDeviceRequest $request = null,
        array $headers = []
    ): OtpDeviceDetails|array {
        return $this->getOtpDevicesClient()->getOtpDevice($deviceId, $request, $headers);
    }

    /**
     * Get a single OTP device asynchronously.
     *
     * @param string $deviceId
     * @param GetOtpDeviceRequest|null $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getOtpDeviceAsync(
        string $deviceId,
        ?GetOtpDeviceRequest $request = null,
        array $headers = []
    ): mixed {
        return $this->getOtpDevicesClient()->getOtpDeviceAsync($deviceId, $request, $headers);
    }

    /**
     * Delete OTP device.
     *
     * @param string $deviceId
     * @param array<string, string> $headers
     * @return void
     */
    public function deleteOtpDevice(string $deviceId, array $headers = []): void
    {
        $this->getOtpDevicesClient()->deleteOtpDevice($deviceId, $headers);
    }

    /**
     * Delete OTP device asynchronously.
     *
     * @param string $deviceId
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteOtpDeviceAsync(string $deviceId, array $headers = []): mixed
    {
        return $this->getOtpDevicesClient()->deleteOtpDeviceAsync($deviceId, $headers);
    }
}
