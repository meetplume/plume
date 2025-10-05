<?php

namespace App\Http\Controllers\Content;

use App\Models\ContentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Uri;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class ShowContentController
{
    /**
     * @throws \Mcamara\LaravelLocalization\Exceptions\SupportedLocalesNotDefined
     * @throws \Mcamara\LaravelLocalization\Exceptions\UnsupportedLocaleException
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $nonLocalizedURI = Uri::of(
            LaravelLocalization::getNonLocalizedURL()
        );

        $currentLocale = LaravelLocalization::getCurrentLocale();
        $defaultLocale = LaravelLocalization::getDefaultLocale();
        $files = ContentFile::query()->get();

        $contentFile = $files->first(fn(ContentFile $file) => $file->isPath(
                $nonLocalizedURI->path(),
                $currentLocale,
            ));

        // Redirect if the file is not found in the current locale but exists in the default locale
        if (blank($contentFile) && $currentLocale !== $defaultLocale) {
            $contentFile = $files->first(fn(ContentFile $file) => $file->isPath(
                    $nonLocalizedURI->path(),
                    $defaultLocale,
                ));
            if (!blank($contentFile)) {
                return redirect()->to(LaravelLocalization::getLocalizedURL($defaultLocale, $nonLocalizedURI->path(), [], true));
            }
        }

        abort_if(blank($contentFile), 404);

        $object = YamlFrontMatter::parseFile($contentFile->realPath);

        return view('content.show', [
            'contentFile' => $contentFile,
            'object' => $object,
            'dangerouslyAllowBladeRender' => true,
        ]);
    }
}
