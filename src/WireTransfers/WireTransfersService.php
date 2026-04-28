<?php

namespace mirrorps\Yii2Taler\WireTransfers;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\WireTransfers\Dto\GetTransfersRequest;
use Taler\Api\WireTransfers\Dto\TransfersList;
use Taler\Api\WireTransfers\WireTransfersClient;
use yii\base\Component;

/**
 * WireTransfersService - Yii2 service component for GNU Taler Wire Transfers API.
 *
 * Provides a thin, testable wrapper over the underlying WireTransfersClient.
 */
class WireTransfersService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var WireTransfersClient|null Lazily resolved WireTransfersClient */
    private ?WireTransfersClient $_wireTransfersClient = null;

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
     * Returns the underlying WireTransfersClient, creating it on first access.
     */
    public function getWireTransfersClient(): WireTransfersClient
    {
        if ($this->_wireTransfersClient === null) {
            $this->_wireTransfersClient = $this->_taler->getClient()->wireTransfers();
        }

        return $this->_wireTransfersClient;
    }

    /**
     * List transfers.
     *
     * @param GetTransfersRequest|null $request
     * @param array<string, string> $headers
     * @return TransfersList|array{transfers: array<int, array<string, mixed>>}
     */
    public function getTransfers(?GetTransfersRequest $request = null, array $headers = []): TransfersList|array
    {
        return $this->getWireTransfersClient()->getTransfers($request, $headers);
    }

    /**
     * List transfers asynchronously.
     *
     * @param GetTransfersRequest|null $request
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getTransfersAsync(?GetTransfersRequest $request = null, array $headers = []): mixed
    {
        return $this->getWireTransfersClient()->getTransfersAsync($request, $headers);
    }

    /**
     * Delete transfer by transfer serial ID.
     *
     * @param string $tid
     * @param array<string, string> $headers
     * @return void
     */
    public function deleteTransfer(string $tid, array $headers = []): void
    {
        $this->getWireTransfersClient()->deleteTransfer($tid, $headers);
    }

    /**
     * Delete transfer asynchronously.
     *
     * @param string $tid
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteTransferAsync(string $tid, array $headers = []): mixed
    {
        return $this->getWireTransfersClient()->deleteTransferAsync($tid, $headers);
    }
}
