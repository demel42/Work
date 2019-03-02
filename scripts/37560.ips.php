<?php

$content = '<body bgcolor="#0f2d4b">';

$instanceStatusCodes = [
    101 => 'Instanz wird erstellt',
    102 => 'Instanz ist aktiv',
    103 => 'Instanz wird gelöscht',
    104 => 'Instanz ist inaktiv',
    105 => 'Instanz wurde nicht erzeugt',
];

$errorCount = 0;
$errorTotal = 0;
$ids = IPS_GetInstanceList();
foreach ($ids as $id) {
    $instance = IPS_GetInstance($id);
    if ($instance['InstanceStatus'] <= 103) {
        continue;
    }
    if ($errorCount == 0) {
        $content .= '<b>Defekte Instanzen:</b><br>' . PHP_EOL;
    }
    $errorCount++;
    $instanceStatus = $instance['InstanceStatus'];
    if (isset($instanceStatusCodes[$instanceStatus])) {
        $err = $instanceStatusCodes[$instanceStatus];
    } else {
        $err = 'Status ' . $instanceStatus;
    }
    $col = $instanceStatus >= 200 ? 'red' : 'grey';
    $loc = IPS_GetLocation($id);
    $content .= '<span style="color: ' . $col . ';">&nbsp;&nbsp;&nbsp;#' . $id . ': ' . $loc . ': ' . $err . '</span><br>' . PHP_EOL;
}

if ($errorCount > 0) {
    $content .= '<br>' . PHP_EOL;
    $errorTotal += $errorCount;
    $errorCount = 0;
}

$ids = IPS_GetScriptList();

foreach ($ids as $id) {
    $script = IPS_GetScript($id);
    if (!$script['ScriptIsBroken']) {
        continue;
    }
    if ($errorCount == 0) {
        $content .= '<b>Defekte Skripte:</b><br>' . PHP_EOL;
    }
    $errorCount++;
    $col = 'red';
    $loc = IPS_GetLocation($id);
    $content .= '<span style="color: ' . $col . ';">&nbsp;&nbsp;&nbsp;#' . $id . ': ' . $loc . '</span><br>' . PHP_EOL;
}

if ($errorCount > 0) {
    $content .= '<br>' . PHP_EOL;
    $errorTotal += $errorCount;
    $errorCount = 0;
}

$ids = IPS_GetLinkList();

foreach ($ids as $id) {
    $link = IPS_GetLink($id);
    if (IPS_ObjectExists($link['LinkID'])) {
        continue;
    }
    if ($errorCount == 0) {
        $content .= '<b>Defekte Links:</b><br>' . PHP_EOL;
    }
    $errorCount++;
    $col = 'red';
    $loc = IPS_GetLocation($id);
    $content .= '<span style="color: ' . $col . ';">&nbsp;&nbsp;&nbsp;#' . $id . ': ' . $loc . '</span><br>' . PHP_EOL;
}

if ($errorCount > 0) {
    $content .= '<br>' . PHP_EOL;
    $errorTotal += $errorCount;
    $errorCount = 0;
}

SetValueString(48011, $content);
SetValueInteger(13646, $errorTotal);
