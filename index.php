<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php
$example_persons_array = include 'array.php';

function getFullnameFromParts($surname, $name, $patronomyc)
{
    return implode(' ', [$surname, $name, $patronomyc]);
}

function getPartsFromFullname($fullname)
{
    $arr = explode(' ', $fullname);
    $new['surname'] = $arr[0];
    $new['name'] = $arr[1];
    $new['patronomyc'] = $arr[2];
    return $new;
}

function getShortName($fullname)
{
    $fullname = getPartsFromFullname($fullname);
    $surname = preg_split('//u', $fullname['surname'], null, PREG_SPLIT_NO_EMPTY);
    return $fullname['name'] . ' ' . $surname[0] . '.';
}

function getGenderFromName($fullname)
{
    $fullname = getPartsFromFullname($fullname);
    $gen = 0;
    $surname = preg_split('//u', $fullname['surname'], null, PREG_SPLIT_NO_EMPTY);
    $name = preg_split('//u', $fullname['name'], null, PREG_SPLIT_NO_EMPTY);
    $patronomyc = preg_split('//u', $fullname['patronomyc'], null, PREG_SPLIT_NO_EMPTY);

    if (implode(array_slice($name, -1)) == 'а' or
        implode(array_slice($surname, -2)) == 'ва' or
        implode(array_slice($patronomyc, -3)) == 'вна') {
        $gen--;
    } elseif ((implode(array_slice($name, -1)) == 'й' or implode(array_slice($name, -1)) == 'н') or
        implode(array_slice($surname, -1)) == 'в' or
        implode(array_slice($patronomyc, -3)) == 'вич') {
        $gen++;
    }

    return $gen;

}


function getGenderDescription($example_persons_array)
{
    function ifMan($fullname)
    {
        if (getGenderFromName($fullname) == 1) {
            return true;
        } else {
            return false;
        }
    }
    function ifWoman($fullname)
    {
        if (getGenderFromName($fullname) == -1) {
            return true;
        } else {
            return false;
        }
    }
    $all = array_column($example_persons_array, 'fullname');
    $m = array_filter($all, 'ifMan');
    $w = array_filter($all, 'ifWoman');
    $m = count($m);
    $w = count($w);
    $total = count($all);
    $u = $total - $m - $w;
    $m = round($m / $total * 100, 1);
    $w = round($w / $total * 100, 1);
    $u = round($u / $total * 100, 1);
    return <<<EOT
Гендерный состав аудитории:<br>
---------------------------<br>
Мужчины - $m% <br>
Женщины - $w%<br>
Не удалось определить - $u%<br>
EOT;
}

function getPerfectPartner($surname, $name, $patronomyc, $example_persons_array)
{
    $array = array_column($example_persons_array, 'fullname');

    $fullname = getFullnameFromParts($surname, $name, $patronomyc);
    $fullname = mb_convert_case($fullname, MB_CASE_TITLE, "UTF-8");
    $gen = getGenderFromName($fullname);
    $shortName = getShortName($fullname);

    a:
    $rand_keys = array_rand($array);
    $arrGen = getGenderFromName($array[$rand_keys]);
    if ($arrGen !== -$gen) {
        goto a;
    }

    $par = $array[$rand_keys];
    $comp = round(mt_rand(5000, 10000) / 100, 2);

    $shortName = getShortName($fullname);
    $shortPar = getShortName($par);

    return <<<EOT
$shortName + $shortPar = <br>
♡ Идеально на $comp% ♡ <br>
EOT;
}


?>
</body>
</html>
