<?
namespace WOWAPI;

require __DIR__ . '/autoload.php';

$cacheUrl = 'http://eu.battle.net/wow/ru/character/%s/%s/simple';

$Page = new SYSTEM\UrlRequest( $cacheUrl );
try {
    $html = $Page->load('свежеватель-душ', 'Чекабой');
} catch (\Exception $e) {
    echo 'Message: ' .$e->getMessage();
}

