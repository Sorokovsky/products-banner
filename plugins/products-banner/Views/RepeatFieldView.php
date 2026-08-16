<?php
namespace ProductsBanner\Views;

use ProductsBanner\Repositories\SettingsRepository;
use ProductsBanner\Services\SettingsService;

class RepeatFieldView
{
    public function __construct()
    {
    }

    public function render(bool $default): string
    {
        $option_name = SettingsService::OPTION_NAME;
        $key = SettingsRepository::REPEAT;
        $checked = checked($default, true, false);
        $message = __('Дозволити повторення банерів', SettingsService::DOMAIN);
        $info = __('Якщо включено, банери можуть повторюватися на різних сторінках. Якщо вимкнено - кожен банер буде показано лише один раз.', SettingsService::DOMAIN);
        $html = <<<HTML
        <fieldset>
            <label>
                <input type="checkbox" 
                       id="{$key}" 
                       name="{$option_name}[{$key}]" 
                       value="1" 
                       {$checked} />
                {$message}
            </label>
            <p class="description">{$info}</p>
        </fieldset>
HTML;
        return $html;
    }
}