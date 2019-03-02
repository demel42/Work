<?php
// -----------------------------------------------------------------------------
// WebFront-freundliche HTML-Ausgabe aller defekten Instanzen, Skripte und Links
// -----------------------------------------------------------------------------

// Variablen-ID zur Speicherung der Meldung.
$ContentVariableID = 48011;
// -----------------------------------------------------------------------------

$content = '<body bgcolor="#0f2d4b">';  

$instanceStatusCodes = array(
    100 => 'module base status',
    101 => 'module is being created',
    102 => 'module created and running',
    103 => 'module is being deleted',
    104 => 'module is not beeing used'
);

$errorCount = 0;

$ids = IPS_GetInstanceList();
foreach ($ids as $id)
{
    $instance = IPS_GetInstance($id);
    if ($instance['InstanceStatus'] > 103)
    {
        if ($errorCount == 0)
        {
            $content .= '<b>Defekte Instanzen:</b><br />'."\r\n";
        }
        $errorCount++;
		$instanceStatus = $instance['InstanceStatus'];
		if (isset($instanceStatusCodes[$instanceStatus])) {
			$s = $instanceStatusCodes[$instanceStatus];
		} else {
			$s = 'unknown status ' . $instanceStatus;
		}
        $content .= '<span style="color: '.($instanceStatus >= 200 ? 'red' : 'grey').';">#'.$id.': '.IPS_GetLocation($id).': '.$s.'</span><br />'."\r\n";
    }
}

if ($errorCount > 0)
{
    $content .= '<br />'."\r\n";
    $errorCount = 0;
}

$ids = IPS_GetScriptList();

foreach ($ids as $id)
{
    $script = IPS_GetScript($id);
	
    if ($script['ScriptIsBroken'])
    {
        if ($errorCount == 0)
        {
            $content .= '<b>Defekte Skripte:</b><br />'."\r\n";
        }
        $errorCount++;
        $content .= '<span style="color: red;">#'.$id.': '.IPS_GetLocation($id).'</span><br />'."\r\n";
    }
}

if ($errorCount > 0)
{
    $content .= '<br />'."\r\n";
    $errorCount = 0;
}

$ids = IPS_GetLinkList();

foreach ($ids as $id)
{
    $link = IPS_GetLink($id);
	
    if (!IPS_ObjectExists($link['LinkID']))
    {
        if ($errorCount == 0)
        {
            $content .= '<b>Defekte Links:</b><br />'."\r\n";
        }
        $errorCount++;
        $content .= '<span style="color: red;">#'.$id.': '.IPS_GetLocation($id).'</span><br />'."\r\n";
    }
}

$printContent = true;

if (IPS_VariableExists((int)$ContentVariableID))
{
    $variable = IPS_GetVariable($ContentVariableID);
	
    if ($variable['VariableType'] === 3)
    {
        $printContent = false;
        SetValueString($ContentVariableID, $content);
    }
}

if ($printContent)
{
    echo $content;
}
?>