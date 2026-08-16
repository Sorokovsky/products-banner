<?php

namespace ProductsBanner\Models;

class SettingsModel
{
    /**
     * @var array<BannerModel>
     */
    private readonly array $banners;

    private readonly int $skip_count;

    private readonly bool $repeat;

    /**
     * @var array<BannerModel>
     */
    public function __construct(array $banners, int $skip_count, bool $repeat)
    {
        $this->banners = $banners;
        $this->skip_count = $skip_count;
        $this->repeat = $repeat;
    }

    /**
     * @return array<BannerModel>
     */
    public function get_banners(): array
    {
        return $this->banners;
    }

    public function get_skip_count(): int
    {
        return $this->skip_count;
    }

    public function is_repeat(): bool
    {
        return $this->repeat;
    }
}