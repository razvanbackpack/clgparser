<style>
body{background:black; color: white;}
</style>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . '/../vendor/autoload.php';
use ClgView\ClgView;

$options = [
    'space' => '&nbsp;',
    'subtitles_as_labels' => true,
    'item_ids' => [
        'title' => 'title_id',
        'subtitle' => 'subtitle_id',
        'marker' => 'marker_id',
        'list_container' => 'container_id',
        'list_item' => 'item_id',
    ],
    'item_classes' => [
        'title' => 'title_class',
        'subtitle' => 'subtitle_class',
        'marker' => 'marker_class',
        'list_container' => 'container_class',
        'list_item' => 'item_class',
    ],
];

$ClgView = new ClgView('CHANGELOG.md', $options);
$ClgView->parse();
