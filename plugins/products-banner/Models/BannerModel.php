<?php
namespace ProductsBanner\Models;

class BannerModel
{
    private readonly string $image_url;

    private readonly string $url;

    public function __construct(string $image_url, string $url)
    {
        $this->image_url = $image_url;
        $this->url = $url;
    }

    public function get_image_url(): string
    {
        return $this->image_url;
    }

    public function get_url(): string
    {
        return $this->url;
    }
}