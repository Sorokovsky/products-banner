<?php

namespace ProductsBanner\Views;

class SettingsPageView
{
    public function render(): string
    {
        $html = $this->render_html();
        $styles = $this->render_styles();
        return $html . $styles;
    }

    private function render_html(): string
    {
        $html = <<<HTML
        <h1>Банери серед товарів</h1>
        HTML;
        return $html;
    }

    private function render_styles(): string
    {
        $styles = <<<HTML
        <style>
            
        </style>
        HTML;
        return $styles;
    }
}