<?php
namespace ProductsBanner\Views;

use ProductsBanner\Repositories\SettingsRepository;
use ProductsBanner\Services\SettingsService;

class SkipFieldView
{
    public function __construct()
    {
    }

    public function render(int $default): string
    {
        $message = __('Вкажіть через скільки товарів показувати банер. Наприклад: 2 - банер буде показуватись після кожного другого товару.', SettingsService::DOMAIN);
        $option_name = SettingsService::OPTION_NAME;
        $key = SettingsRepository::SKIP;
        $html = <<<HTML
        <input type="number" id="{$key}"
            name="{$option_name}[{$key}]" value="{$default}"
            min="1" step="1" class="small-text" />
        <p class="description">{$message}</p>
HTML;
        return $html;
    }
}