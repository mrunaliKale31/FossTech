<?php

declare(strict_types=1);

namespace OCA\GitIntegration\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class PersonalSettings implements ISettings {
    /**
     * @return TemplateResponse
     */
    public function getForm(): TemplateResponse {
        return new TemplateResponse('gitintegration', 'settings/personal');
    }

    /**
     * @return string
     */
    public function getSection(): string {
        return 'additional';
    }

    /**
     * @return int
     */
    public function getPriority(): int {
        return 50;
    }
}
