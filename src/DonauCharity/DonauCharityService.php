<?php

namespace mirrorps\Yii2Taler\DonauCharity;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\DonauCharity\DonauCharityClient;
use Taler\Api\DonauCharity\Dto\DonauInstancesResponse;
use Taler\Api\DonauCharity\Dto\PostDonauRequest;
use Taler\Api\TwoFactorAuth\Dto\ChallengeResponse;
use yii\base\Component;

/**
 * DonauCharityService - Yii2 service component for GNU Taler Donau Charity API.
 */
class DonauCharityService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var DonauCharityClient|null Lazily resolved DonauCharityClient */
    private ?DonauCharityClient $_donauCharityClient = null;

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
     * Returns the underlying DonauCharityClient, creating it on first access.
     */
    public function getDonauCharityClient(): DonauCharityClient
    {
        if ($this->_donauCharityClient === null) {
            $this->_donauCharityClient = $this->_taler->getClient()->donauCharity();
        }

        return $this->_donauCharityClient;
    }

    /**
     * Return all linked Donau charity instances.
     *
     * @param array<string, string> $headers
     * @return DonauInstancesResponse|array
     */
    public function getInstances(array $headers = []): DonauInstancesResponse|array
    {
        return $this->getDonauCharityClient()->getInstances($headers);
    }

    /**
     * Return all linked Donau charity instances (async).
     *
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getInstancesAsync(array $headers = []): mixed
    {
        return $this->getDonauCharityClient()->getInstancesAsync($headers);
    }

    /**
     * Link a Donau charity instance.
     *
     * @param PostDonauRequest $request
     * @param array<string, string> $headers
     * @return ChallengeResponse|null
     */
    public function createDonauCharity(PostDonauRequest $request, array $headers = []): ?ChallengeResponse
    {
        return $this->getDonauCharityClient()->createDonauCharity($request, $headers);
    }

    /**
     * Link a Donau charity instance (async).
     *
     * @param PostDonauRequest $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function createDonauCharityAsync(PostDonauRequest $request, array $headers = []): mixed
    {
        return $this->getDonauCharityClient()->createDonauCharityAsync($request, $headers);
    }

    /**
     * Unlink a Donau charity instance by serial.
     *
     * @param int $donauSerial
     * @param array<string, string> $headers
     * @return void
     */
    public function deleteDonauCharityBySerial(int $donauSerial, array $headers = []): void
    {
        $this->getDonauCharityClient()->deleteDonauCharityBySerial($donauSerial, $headers);
    }

    /**
     * Unlink a Donau charity instance by serial (async).
     *
     * @param int $donauSerial
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteDonauCharityBySerialAsync(int $donauSerial, array $headers = []): mixed
    {
        return $this->getDonauCharityClient()->deleteDonauCharityBySerialAsync($donauSerial, $headers);
    }
}
