<?php

// -----------------------------------------------------------------------------
// WebFront-freundliche HTML-Ausgabe aller defekten Instanzen, Skripte und Links
// -----------------------------------------------------------------------------

// Variablen-ID zur Speicherung der Meldung.
$ContentVariableID = 48011;
// -----------------------------------------------------------------------------

$content = '<body bgcolor="#0f2d4b">';

$instanceStatusCodes = [
    100 => 'module base status',
    101 => 'module is being created',
    102 => 'module created and running',
    103 => 'module is being deleted',
    104 => 'module is not beeing used'
];

$errorCount = 0;

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
        $s = $instanceStatusCodes[$instanceStatus];
    } else {
        $s = 'unknown status ' . $instanceStatus;
    }
    $col = $instanceStatus >= 200 ? 'red' : 'grey';
    $loc = IPS_GetLocation($id);
    $content .= '<span style="color: ' . $col . ';">&nbsp;&nbsp;&nbsp;#' . $id . ': ' . $loc . ': ' . $s . '</span><br>' . PHP_EOL;
}

if ($errorCount > 0) {
    $content .= '<br>' . PHP_EOL;
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

$printContent = true;

if (IPS_VariableExists($ContentVariableID)) {
    $variable = IPS_GetVariable($ContentVariableID);
    if ($variable['VariableType'] === 3) {
        $printContent = false;
        SetValueString($ContentVariableID, $content);
    }
}

if ($printContent) {
    echo $content;
}
