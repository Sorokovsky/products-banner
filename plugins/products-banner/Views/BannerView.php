<?php
namespace ProductsBanner\Views;

use ProductsBanner\Models\BannerModel;

class BannerView
{
    public function __construct()
    {
    }

    public function render(BannerModel $banner): string
    {
        $html = $this->render_html($banner);
        $styles = $this->render_styles();
        $scripts = $this->render_script();
        return $html.$styles.$scripts;
    }

    private function render_html(BannerModel $banner): string
    {
        $image = esc_url($banner->get_image_url());
        $url = esc_url($banner->get_url());
        $html = <<<HTML
        <div class="products-banner-item">
            <a href="{$url}" target="_blank">
                <img src="{$image}" alt="Banner" />
            </a>
        </div>
        HTML;
        return $html;
    }

    private function render_styles(): string
    {
        $styles = <<<HTML
        <style>
        .products-banner-item {
            height: 100%;
            max-height: 600px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .products-banner-item img {
            width: 100%;
            height: 100%;
            object-fit: contains;
            max-height: inherit;
        }
        </style>
        HTML;
        return $styles;
    }

    private function render_script(): string
    {
        $scripts = <<<HTML
        <script>
        jQuery(document).ready(function($) {
            $('.products-banner-item').each(function() {
                $(this).closest('li').after($(this).closest('li').find('.products-banner-item'));
            });
        });
        </script>
        HTML;
        return $scripts;
    }
}
