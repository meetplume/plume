<?php

namespace App\Http\Controllers\Content;

use App\Models\ContentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Uri;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class ShowContentController
{
    public function __invoke(Request $request): View
    {
        $nonLocalizedURI = Uri::of(
            LaravelLocalization::getNonLocalizedURL()
        );

        $currentLocale = LaravelLocalization::getCurrentLocale();

        $contentFile = ContentFile::query()
            ->get()
            ->first(fn(ContentFile $file) => $file->isPath(
                $nonLocalizedURI->path(),
                $currentLocale,
            ));

        abort_if(blank($contentFile), 404);

        $object = YamlFrontMatter::parseFile($contentFile->realPath);

        return view('content.show', [
            'contentFile' => $contentFile,
            'object' => $object,
            'dangerouslyAllowBladeRender' => true,
        ]);
    }
}
