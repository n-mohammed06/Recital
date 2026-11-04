<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    /**
     * Conditions d'utilisation.
     */
    public function terms(): View
    {
        return $this->show('conditions-utilisation.md', 'Conditions d’utilisation');
    }

    /**
     * Politique de confidentialité.
     */
    public function privacy(): View
    {
        return $this->show('politique-confidentialite.md', 'Politique de confidentialité');
    }

    /**
     * Les textes sont écrits en Markdown dans resources/legal/ :
     * on les modifie comme un simple fichier texte, sans toucher au HTML.
     */
    private function show(string $file, string $title): View
    {
        $markdown = file_get_contents(resource_path('legal/'.$file));

        return view('legal.page', [
            'title' => $title,
            // « strip » retire tout HTML écrit dans le fichier : seule la syntaxe Markdown est convertie.
            'content' => Str::markdown($markdown, [
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]),
        ]);
    }
}
