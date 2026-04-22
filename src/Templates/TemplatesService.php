<?php

namespace mirrorps\Yii2Taler\Templates;

use mirrorps\Yii2Taler\Taler;
use Taler\Api\Templates\Dto\TemplateAddDetails;
use Taler\Api\Templates\Dto\TemplateDetails;
use Taler\Api\Templates\Dto\TemplatePatchDetails;
use Taler\Api\Templates\Dto\TemplatesSummaryResponse;
use Taler\Api\Templates\TemplatesClient;
use yii\base\Component;

/**
 * TemplatesService - Yii2 service component for GNU Taler Templates API.
 *
 * Provides a thin, testable wrapper over the underlying TemplatesClient.
 */
class TemplatesService extends Component
{
    /** @var Taler The parent Taler component */
    private Taler $_taler;

    /** @var TemplatesClient|null Lazily resolved TemplatesClient */
    private ?TemplatesClient $_templatesClient = null;

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
     * Returns the underlying TemplatesClient, creating it on first access.
     */
    public function getTemplatesClient(): TemplatesClient
    {
        if ($this->_templatesClient === null) {
            $this->_templatesClient = $this->_taler->getClient()->templates();
        }

        return $this->_templatesClient;
    }

    /**
     * Create template.
     *
     * @param TemplateAddDetails $details
     * @param array<string, string> $headers
     * @return void
     */
    public function createTemplate(TemplateAddDetails $details, array $headers = []): void
    {
        $this->getTemplatesClient()->createTemplate($details, $headers);
    }

    /**
     * Create template asynchronously.
     *
     * @param TemplateAddDetails $details
     * @param array<string, string> $headers
     * @return mixed
     */
    public function createTemplateAsync(TemplateAddDetails $details, array $headers = []): mixed
    {
        return $this->getTemplatesClient()->createTemplateAsync($details, $headers);
    }

    /**
     * Update template.
     *
     * @param string $templateId
     * @param TemplatePatchDetails $details
     * @param array<string, string> $headers
     * @return void
     */
    public function updateTemplate(string $templateId, TemplatePatchDetails $details, array $headers = []): void
    {
        $this->getTemplatesClient()->updateTemplate($templateId, $details, $headers);
    }

    /**
     * Update template asynchronously.
     *
     * @param string $templateId
     * @param TemplatePatchDetails $details
     * @param array<string, string> $headers
     * @return mixed
     */
    public function updateTemplateAsync(string $templateId, TemplatePatchDetails $details, array $headers = []): mixed
    {
        return $this->getTemplatesClient()->updateTemplateAsync($templateId, $details, $headers);
    }

    /**
     * List templates.
     *
     * @param array<string, string> $headers
     * @return TemplatesSummaryResponse|array<string, mixed>
     */
    public function getTemplates(array $headers = []): TemplatesSummaryResponse|array
    {
        return $this->getTemplatesClient()->getTemplates($headers);
    }

    /**
     * List templates asynchronously.
     *
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getTemplatesAsync(array $headers = []): mixed
    {
        return $this->getTemplatesClient()->getTemplatesAsync($headers);
    }

    /**
     * Get a single template.
     *
     * @param string $templateId
     * @param array<string, string> $headers
     * @return TemplateDetails|array<string, mixed>
     */
    public function getTemplate(string $templateId, array $headers = []): TemplateDetails|array
    {
        return $this->getTemplatesClient()->getTemplate($templateId, $headers);
    }

    /**
     * Get a single template asynchronously.
     *
     * @param string $templateId
     * @param array<string, string> $headers
     * @return mixed
     */
    public function getTemplateAsync(string $templateId, array $headers = []): mixed
    {
        return $this->getTemplatesClient()->getTemplateAsync($templateId, $headers);
    }

    /**
     * Delete template.
     *
     * @param string $templateId
     * @param array<string, string> $headers
     * @return void
     */
    public function deleteTemplate(string $templateId, array $headers = []): void
    {
        $this->getTemplatesClient()->deleteTemplate($templateId, $headers);
    }

    /**
     * Delete template asynchronously.
     *
     * @param string $templateId
     * @param array<string, string> $headers
     * @return mixed
     */
    public function deleteTemplateAsync(string $templateId, array $headers = []): mixed
    {
        return $this->getTemplatesClient()->deleteTemplateAsync($templateId, $headers);
    }
}
