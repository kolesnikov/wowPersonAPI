<?
namespace WOWAPI;

require __DIR__ . '/autoload.php';
$Guild = new API\Guild( 'свежеватель-душ', 'Больничка' );

echo $Guild->name;
