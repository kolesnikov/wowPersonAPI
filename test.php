<?
namespace WOWAPI;

require __DIR__ . '/autoload.php';

$cacheUrl = 'http://eu.battle.net/wow/ru/character/%s/%s/simple';

try {
    $Page = new SYSTEM\UrlRequest( $cacheUrl );
    $html = $Page->load('свежеватель-душ', 'Чекабой');
} catch (\Exception $e) {
    echo 'Message: ' .$e->getMessage();
}

var_dump($html);
