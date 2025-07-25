<?php

namespace Misusonu18\DocumentEditor;

use Illuminate\Support\HtmlString;

class DocumentEditor
{
    /**
     * Get CSS for the document editor
     */
    public static function css(): \Illuminate\Support\HtmlString
    {
        // Load built CSS
        $appCssPath = __DIR__.'/../resources/dist/style.css';
        $appCssContent = file_exists($appCssPath) ? file_get_contents($appCssPath) : '';

        return new HtmlString('
            <style>'.$appCssContent.'</style>
        ');
    }

    /**
     * Get JavaScript for the document editor
     */
    public static function js(): \Illuminate\Support\HtmlString
    {
        // Load built app.js
        $appJsPath = __DIR__.'/../resources/dist/app.js';
        $appJsContent = file_exists($appJsPath) ? file_get_contents($appJsPath) : '';

        return new HtmlString('
            <script>'.$appJsContent.'</script>
        ');
    }
}
