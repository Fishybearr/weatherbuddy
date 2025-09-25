<?php

function isMobile()
{
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
	$mobileKeywords = array(
            '/iphone/i', '/ipod/i', '/ipad/i', '/android/i', '/blackberry/i',
            '/webos/i', '/windows phone/i', '/iemobile/i', '/opera mini/i',
            '/mobile/i' // General mobile keyword
        );

	foreach ($mobileKeywords as $keyword)
	{
		if(preg_match($keyword, $userAgent))
		{
			return true;
		}
	}
return false;
}

$result = isMobile();

if($result == false)
{
$contents = file_get_contents('desktopIndex.html');
	echo($contents);
}
else
{
$contents = file_get_contents('mobileIndex.html');
	echo($contents);
}


?>

