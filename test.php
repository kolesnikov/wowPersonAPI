<?
namespace WOWAPI;

require __DIR__ . '/wowapi.php';

//$Guild = new API\Guild( 'свежеватель-душ', 'Больничка' );
//echo $Guild->name;
$Character = new API\Character('свежеватель-душ','Чекабой');
$Item = $Character->slot(12, true);
var_dump($Item);

