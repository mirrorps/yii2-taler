<?php

namespace mirrorps\Yii2Taler\Webhooks;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\Webhooks\Dto\WebhookAddDetails;
use Taler\Api\Webhooks\Dto\WebhookDetails;
use Taler\Api\Webhooks\Dto\WebhookPatchDetails;
use Taler\Api\Webhooks\Dto\WebhookSummaryResponse;
use Taler\Api\Webhooks\WebhooksClient;
use yii\base\Component;

/**
 * WebhooksService - Yii2 service component for GNU Taler Webhooks API.
 *
 * Provides a thin, testable wrapper over the underlying WebhooksClient.
 */
class WebhooksService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var WebhooksClient|null Lazily resolved WebhooksClient */
    private ?WebhooksClient $_webhooksClient = null;

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
     * Returns the underlying WebhooksClient, creating it on first access.
     */
    public function getWebhooksClient(): WebhooksClient
    {
        if ($this->_webhooksClient === null) {
            $this->_webhooksClient = $this->_taler->getClient()->webhooks();
        }

        return $this->_webhooksClient;
    }

    /**
     * Create webhook.
     *
     * @param WebhookAddDetails $details
     * @param array<string, string> $headers
     * @return void
     */
    public function createWebhook(WebhookAddDetails $details, array $headers = []): void
    {
        $this->getWebhooksClient()->createWebhook($details, $headers);
    }

    /**
     * Create webhook asynchronously.
     *
     * @param WebhookAddDetails $details
     * @param array<string, string> $headers
     * @return mixed
     */
    public function createWebhookAsync(WebhookAddDetails $details, array $headers = []): mixed
    {
        return $this->getWebhooksClient()->createWebhookAsync($details, $headers);
    }

    /**
     * Update webhook.
     *
     * @param string $webhookId
     * @param WebhookPatchDetails $details
     * @param array<string, string> $headers
     * @return void
     */
    public function updateWebhook(string $webhookId, WebhookPatchDetails $details, array $headers = []): void
    {
        $this->getWebhooksClient()->updateWebhook($webhookId, $details, $headers);
    }

    /**
     * Update webhook asynchronously.
     *
     * @param string $webhookId
     * @param WebhookPatchDetails $details
     * @param array<string, string> $headers
     * @return mixed
     */
    public function updateWebhookAsync(string $webhookId, WebhookPatchDetails $details, array $headers = []): mixed
    {
        return $this->getWebhooksClient()->updateWebhookAsync($webhookId, $details, $headers);
    }

    /**
     * List webhooks.
     *
     * @param array<string, string> $headers
     * @return WebhookSummaryResponse|array<string, mixed>
     */
    public function getWebhooks(array $headers = []): WebhookSummaryResponse|array
    {
        return $this->getWebhooksClient()->getWebhooks($headers);
    }

    /**
     * List webhooks asynchronously.
     *
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getWebhooksAsync(array $headers = []): mixed
    {
        return $this->getWebhooksClient()->getWebhooksAsync($headers);
    }

    /**
     * Get a single webhook.
     *
     * @param string $webhookId
     * @param array<string, string> $headers
     * @return WebhookDetails|array<string, mixed>
     */
    public function getWebhook(string $webhookId, array $headers = []): WebhookDetails|array
    {
        return $this->getWebhooksClient()->getWebhook($webhookId, $headers);
    }

    /**
     * Get a single webhook asynchronously.
     *
     * @param string $webhookId
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getWebhookAsync(string $webhookId, array $headers = []): mixed
    {
        return $this->getWebhooksClient()->getWebhookAsync($webhookId, $headers);
    }

    /**
     * Delete webhook.
     *
     * @param string $webhookId
     * @param array<string, string> $headers
     * @return void
     */
    public function deleteWebhook(string $webhookId, array $headers = []): void
    {
        $this->getWebhooksClient()->deleteWebhook($webhookId, $headers);
    }

    /**
     * Delete webhook asynchronously.
     *
     * @param string $webhookId
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteWebhookAsync(string $webhookId, array $headers = []): mixed
    {
        return $this->getWebhooksClient()->deleteWebhookAsync($webhookId, $headers);
    }
}
