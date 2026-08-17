<?php
namespace ProductsBanner\Controllers;

use ProductsBanner\Repositories\SettingsRepository;
use ProductsBanner\Services\SettingsService;
use ProductsBanner\Views\BannersFieldsView;
use ProductsBanner\Views\RepeatFieldView;
use ProductsBanner\Views\SettingsPageView;
use ProductsBanner\Views\SkipFieldView;

class SettingsController
{
    private const SECTION = SettingsService::OPTION_NAME . "_section";

    private readonly SettingsPageView $page_view;

    private readonly SkipFieldView $skip_field_view;

    private readonly RepeatFieldView $repeat_field_view;

    private readonly BannersFieldsView $banners_fields_view;

    private readonly SettingsService $service;

    public function __construct(
        SettingsPageView $settings_page_view,
        SettingsService $settings_service,
        SkipFieldView $skip_field_view,
        RepeatFieldView $repeat_field_view,
        BannersFieldsView $banners_fields_view
    ) {
        $this->page_view = $settings_page_view;
        $this->service = $settings_service;
        $this->skip_field_view = $skip_field_view;
        $this->repeat_field_view = $repeat_field_view;
        $this->banners_fields_view = $banners_fields_view;
    }

    public function register_editing_page(): void
    {
        add_submenu_page(
            "woocommerce",
            __(SettingsService::TITLE, SettingsService::DOMAIN),
            __(SettingsService::TITLE, SettingsService::DOMAIN),
            'manage_options',
            SettingsService::OPTION_NAME,
            [$this, 'render_editor_page']
        );
    }

    public function render_editor_page(): void
    {
        echo $this->page_view->render();
    }

    public function register_settings(): void
    {
        register_setting(
            SettingsService::OPTION_NAME,
            SettingsService::OPTION_NAME,
            [
                'type' => 'array',
                'default' => [],
                'sanitize_callback' => [$this->service, 'sanitize']
            ]
        );
        add_settings_section(
            self::SECTION,
            __("Налаштування", SettingsService::DOMAIN),
            null,
            SettingsService::OPTION_NAME
        );
        add_settings_field(
            SettingsRepository::BANNERS,
            __("Банери", SettingsService::OPTION_NAME),
            [$this, 'render_banners_fields'],
            SettingsService::OPTION_NAME,
            self::SECTION,
            ['settings' => $this->service->get_settings()]
        );
        add_settings_field(
            SettingsRepository::SKIP,
            __("Через число товарів", SettingsService::DOMAIN),
            [$this, 'render_skip_field'],
            SettingsService::OPTION_NAME,
            self::SECTION,
            [
                'label_for' => SettingsRepository::SKIP,
                'settings' => $this->service->get_settings()
            ]
        );

        add_settings_field(
            SettingsRepository::REPEAT,
            __("Чи можуть банери повторюватися", SettingsService::DOMAIN),
            [$this, 'render_repeat_field'],
            SettingsService::OPTION_NAME,
            self::SECTION,
            [
                'label_for' => SettingsRepository::REPEAT,
                'settings' => $this->service->get_settings()
            ]
        );
    }

    public function render_banners_fields(): void
    {
        echo $this->banners_fields_view->render($this->service->get_settings()->get_banners());
    }

    public function render_repeat_field(): void
    {
        echo $this->repeat_field_view->render($this->service->get_settings()->is_repeat());
    }

    public function render_skip_field(): void
    {
        echo $this->skip_field_view->render($this->service->get_settings()->get_skip_count());
    }
}
