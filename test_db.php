<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$section = \App\Models\Section::where('slug', 'elysion-ostuni')->first();
echo "Section elysion-ostuni: ";
if ($section) {
    echo "Exists. Visibile: " . $section->visibile . " ID: " . $section->id . "\n";
} else {
    echo "Not found\n";
}

$article = \App\Models\Article::where('slug', 'elysion-ostuni-trulli-puglia')->first();
echo "Article elysion-ostuni-trulli-puglia: ";
if ($article) {
    echo "Exists. Section ID: " . $article->section_id . " ID: " . $article->id . "\n";
} else {
    echo "Not found\n";
}
