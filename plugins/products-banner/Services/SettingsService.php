<?php
namespace ProductsBanner\Services;

use ProductsBanner\Models\SettingsModel;
use ProductsBanner\Repositories\SettingsRepository;

class SettingsService
{
    public const DOMAIN = "products-banner";
    public const OPTION_NAME = "products_banner_settings";

    public const TITLE = "Банери серед продуктів";

    private readonly SettingsRepository $repository;

    public function __construct(SettingsRepository $settings_repository)
    {
        $this->repository = $settings_repository;
    }

    public function get_settings(): SettingsModel
    {
        return $this->repository->get_settings();
    }

    public function sanitize(array $input): array
    {
        return $this->repository->sanitize($input);
    }
}