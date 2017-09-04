<?php

namespace AppBundle\Service;

class MoonPhaseCalculator
{
    public function calculateMoonphase($year, $month, $day)
    {
	/*

	modified from http://www.voidware.com/moon_phase.htm

	*/
	//variables
	$aMoonInfo = array();

	$c = $e = $jd = $b = 0;

	if ($month < 3)

	{

		$year--;

		$month += 12;

	}

	++$month;

	$c = 365.25 * $year;

	$e = 30.6 * $month;

	$jd = $c + $e + $day - 694039.09;	//jd is total days elapsed

	$jd /= 29.5305882;					//divide by the moon cycle

	$b = (int) $jd;						//int(jd) -> b, take integer part of jd

	$jd -= $b;							//subtract integer part to leave fractional part of original jd

	$b = round($jd * 8);				//scale fraction from 0-8 and round

	if ($b >= 8 )

	{

		$b = 0;//0 and 8 are the same so turn 8 into 0

	}

	switch ($b)

	{

		case 0:
		        $aMoonInfo['desc'] = 'New Moon'; 
		        $aMoonInfo['phaseNr'] = '1'; 
                        return $aMoonInfo; 

//			return 'Nov (en: New Moon)';

			break;

		case 1:
		        $aMoonInfo['desc'] = 'Waxing Crescent Moon'; 
		        $aMoonInfo['phaseNr'] = '2'; 
                        return $aMoonInfo; 

//			return 'Dorastajúci úzky kosáčik (en: Waxing Crescent Moon)';

			break;

		case 2:
		        $aMoonInfo['desc'] = 'Quarter Moon'; 
		        $aMoonInfo['phaseNr'] = '3'; 
                        return $aMoonInfo; 

//			return 'Prvá štvrť, dorastajúci polmesiac (en: Quarter Moon)';

			break;

		case 3:
		        $aMoonInfo['desc'] = 'Waxing Gibbous Moon'; 
		        $aMoonInfo['phaseNr'] = '4'; 
                        return $aMoonInfo; 

			//return 'Dorastajúci pred splnom (en: Waxing Gibbous Moon)';

			break;

		case 4:
		        $aMoonInfo['desc'] = 'Full Moon'; 
		        $aMoonInfo['phaseNr'] = '5'; 
                        return $aMoonInfo; 
		//	return 'Úplnok, spln (en: Full Moon)';
			break;

		case 5:
		        $aMoonInfo['desc'] = 'Waning Gibbous Moon'; 
		        $aMoonInfo['phaseNr'] = '6'; 
                        return $aMoonInfo; 

//			return 'Zmenšujúci sa po splne (en: Waning Gibbous Moon)';

			break;

		case 6:
		        $aMoonInfo['desc'] = 'Last Quarter Moon'; 
		        $aMoonInfo['phaseNr'] = '7'; 
                        return $aMoonInfo; 
			//return 'Posledná štvrť, zmenšujúci sa polmesiac (en: Last Quarter Moon)';

			break;

		case 7:
		        $aMoonInfo['desc'] = 'Waning Crescent Moon'; 
		        $aMoonInfo['phaseNr'] = '8'; 
                        return $aMoonInfo; 

			//return 'Zmenšujúci sa úzky kosáčik (en: Waning Crescent Moon)';

			break;

		default:

			return 'Error';

	}



    }

}


?>
