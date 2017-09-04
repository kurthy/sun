<?php

namespace AppBundle\Service;

use AppBundle\Service\MoonPhaseCalculator;

class SunInfoSet 
{
    protected $moonphasecalculator;

    public function __construct(MoonPhaseCalculator $moonphasecalculator)
    {
      $this->moonphasecalculator = $moonphasecalculator;
    }


    public function calculateSunInfoSet()
    {
	//Benková Potôň (3 km severne)
	$aPom = date_sun_info(time(),48.06096,17.50437); 
	$aPom['lat'] = 48.06096;
	$aPom['lng'] = 17.50437;

	//Východoslovenská nížina
	$aPomVn = date_sun_info(time(),48.65959,22.03273); 
	$aPomVn['lat'] = 48.65959;
	$aPomVn['lng'] = 22.03273;

	//Národný park Poloniny - Stužica (najvýchodnejší cip)
	$aPomPo = date_sun_info(time(),48.08368,22.5381); 
	$aPomPo['lat'] = 48.08368;
	$aPomPo['lng'] = 22.5381;

	//pohorie Čergov pri Bardejove
	$aPomCe = date_sun_info(time(),49.22671,21.10644); 
	$aPomCe['lat'] = 49.22671;
	$aPomCe['lng'] = 21.10644;

	//NP Pieniny
	$aPomPi = date_sun_info(time(),49.39436,20.45413); 
	$aPomPi['lat'] = 49.39436;
	$aPomPi['lng'] = 20.45413;

	//TANAP
	$aPomTa = date_sun_info(time(),49.17855,19.9739); 
	$aPomTa['lat'] = 49.17855;
	$aPomTa['lng'] = 19.9739;

	//Slovenské Rudohorie, Volovské vrchy, Volovec
	$aPomVo = date_sun_info(time(),49.74712,20.56716); 
	$aPomVo['lat'] = 49.74712;
	$aPomVo['lng'] = 20.56716;

	//NP Slovenský Kras, planina pri Hačave 
	$aPomSk = date_sun_info(time(),48.65785,20.9146); 
	$aPomSk['lat'] = 48.65785;
	$aPomSk['lng'] = 20.9146;

	//Slovenské Rudohorie, Volovské vrchy, Kamenný hrb
	$aPomVoK = date_sun_info(time(),48.75131,21.17158); 
	$aPomVoK['lat'] = 48.75131;
	$aPomVoK['lng'] = 21.17158;

	//NP Slovenský raj  
	$aPomSr = date_sun_info(time(),48.91087,20.35018); 
	$aPomSr['lat'] = 48.91087;
	$aPomSr['lng'] = 20.35018;

	//NP Muránska planina 
	$aPomMp = date_sun_info(time(),48.76037,19.99175); 
	$aPomMp['lat'] = 48.76037;
	$aPomMp['lng'] = 19.99175;

	//NAPANT 
	$aPomNa = date_sun_info(time(),48.95463,19.69237); 
	$aPomNa['lat'] = 48.95463;
	$aPomNa['lng'] = 19.69237;

	//CHKO Poľana 
	$aPomPl = date_sun_info(time(),48.65615,19.48775); 
	$aPomPl['lat'] = 48.65615;
	$aPomPl['lng'] = 19.48775;

	//CHKO Cerová vrchovina 
	$aPomCv = date_sun_info(time(),48.20442,19.9224); 
	$aPomCv['lat'] = 48.20442;
	$aPomCv['lng'] = 19.9224;

	//Dunaj - Burda 
	$aPomBu = date_sun_info(time(),47.83908,18.786); 
	$aPomBu['lat'] = 47.83908;
	$aPomBu['lng'] = 18.786;

	//Váh, Listové jazero  
	$aPomLi = date_sun_info(time(),47.89366,18.03447); 
	$aPomLi['lat'] = 47.89366;
	$aPomLi['lng'] = 18.03447;

	//Ponitrie, Tríbeč, Michalov vrch  
	$aPomNi = date_sun_info(time(),48.55569,18.41109); 
	$aPomNi['lat'] = 48.55569;
	$aPomNi['lng'] = 18.41109;

	//NP Malá Fatra  
	$aPomMf = date_sun_info(time(),49.19561,19.0447); 
	$aPomMf['lat'] = 49.19561;
	$aPomMf['lng'] = 19.0447;

	//Orava, Babia hora  
	$aPomOr = date_sun_info(time(),49.57320,19.52912); 
	$aPomOr['lat'] = 49.57320;
	$aPomOr['lng'] = 19.52912;

	//NPR Horný les, Angern/Záhorská Ves, západný cíp   
	$aPomZv = date_sun_info(time(),48.35601,16.86151); 
	$aPomZv['lat'] = 48.35601;
	$aPomZv['lng'] = 16.86151;

	//CHKO Beskydy   
	$aPomBe = date_sun_info(time(),49.44629,18.40198); 
	$aPomBe['lat'] = 49.44629;
	$aPomBe['lng'] = 18.40198;

	//CHKO Biele / Bílé Karpaty, Malý Lopeník   
	$aPomBk = date_sun_info(time(),48.92513,17.78151); 
	$aPomBk['lat'] = 48.92513;
	$aPomBk['lng'] = 17.78151;

	//Soutok Dyje/Morava (CHKO Záhorie, SK/CZ/AT)  
	$aPomSou = date_sun_info(time(),48.61718,16.94029); 
	$aPomSou['lat'] = 48.61718;
	$aPomSou['lng'] = 16.94029;

	//CHKO Slavkovský les (západný cíp CZ)  
	$aPomSla = date_sun_info(time(),50.06302,12.70145); 
	$aPomSla['lat'] = 50.06302;
	$aPomSla['lng'] = 12.70145;

	//NP Šumava (Moorkopf)  
	$aPomSum = date_sun_info(time(),48.96899,13.5074); 
	$aPomSum['lat'] = 48.96899;
	$aPomSum['lng'] = 13.5074;

	//Vysočina - Javořice  
	$aPomVys = date_sun_info(time(),49.22143,15.33871); 
	$aPomVys['lat'] = 49.22143;
	$aPomVys['lng'] = 15.33871;

	//Vysočina - Žďárské vrchy  
	$aPomVyz = date_sun_info(time(),49.66876,16.03360); 
	$aPomVyz['lat'] = 49.66876;
	$aPomVyz['lng'] = 16.03360;

	//Brdy  
	$aPomBr = date_sun_info(time(),49.70430,13.87684); 
	$aPomBr['lat'] = 49.70430;
	$aPomBr['lng'] = 13.87684;

	//Krušné hory - Loučná  
	$aPomKr = date_sun_info(time(),50.64940,13.61249); 
	$aPomKr['lat'] = 50.64940;
	$aPomKr['lng'] = 13.61249;

	//Jizerské hory - Jizera  
	$aPomJi = date_sun_info(time(),50.83264,15.25838); 
	$aPomJi['lat'] = 50.83264;
	$aPomJi['lng'] = 15.25838;

	//Krkonoše - Sněžka 
	$aPomSn = date_sun_info(time(),50.73539,15.74041); 
	$aPomSn['lat'] = 50.73539;
	$aPomSn['lng'] = 15.74041;

	//Orlické hory - Deštná 
	$aPomDe = date_sun_info(time(),50.30187,16.39753); 
	$aPomDe['lat'] = 50.30187;
	$aPomDe['lng'] = 16.39753;

	//Jeseníky - Praděd 
	$aPomJe = date_sun_info(time(),50.08515,17.23043); 
	$aPomJe['lat'] = 50.08515;
	$aPomJe['lng'] = 17.23043;

	//Drahanská vrchovina - Skalky 
	$aPomDr = date_sun_info(time(),49.50094,16.79097); 
	$aPomDr['lat'] = 49.50094;
	$aPomDr['lng'] = 16.79097;

	//Ralsko - Bezděz 
	$aPomRa = date_sun_info(time(),50.54204,14.72005); 
	$aPomRa['lat'] = 50.54204;
	$aPomRa['lng'] = 14.72005;


       return compact('aPom','aPomVn','aPomPo','aPomCe','aPomPi','aPomTa','aPomVo','aPomSk','aPomVoK','aPomSr','aPomMp','aPomNa','aPomPl','aPomCv','aPomBu','aPomLi','aPomNi','aPomMf','aPomOr','aPomZv','aPomBe','aPomBk','aPomSou','aPomSla','aPomSum','aPomVys','aPomVyz','aPomBr','aPomKr','aPomJi','aPomSn','aPomDe','aPomJe','aPomDr','aPomRa');
    }

    public function calculateSunInfoRange($lat=48, $lng=20, $nextdays=20)
    {
	//variables
	$tPom           = '';
	$aPomDalsie     = array();
	$tPomDalsie     = array();
	$aPomMoonDalsie = array();

	//fill variables
	$iPomDni    = $nextdays;
//	$iPom  = 24 * 60 * 60;
	$lat5dec = round($lat,5);
	$lng5dec = round($lng,5);

	$calculator = $this->moonphasecalculator;

        //calculate sun and moon info
	for($i = 1;$i <= $iPomDni;$i++):
//           $tPom = time() + ($i * $iPom);
	    $tPom = strtotime('+'.$i.' days',time());
	   $aPomDalsie[$i] = date_sun_info($tPom,$lat5dec,$lng5dec); 
           $tPomDalsie[$i] = $tPom;

           $aPomMoonDalsie[$i] = $calculator->calculateMoonphase(date('Y', $tPom), date('n', $tPom), date('j', $tPom));

	endfor;

        return compact('aPomDalsie','tPomDalsie','aPomMoonDalsie');

    }

    public function calculateSunInfoYear($lat=48, $lng=20, $year=2017)
    {
	//variables
	$tPom           = '';
	$aPomDalsie     = array();
	$tPomDalsie     = array();
	$aPomMoonDalsie = array();

	//fill variables
	$timestamp = strtotime($year."-01-01");
	$iPomDni    = 365;
//	$iPom  = 24 * 60 * 60;
	$lat5dec = round($lat,5);
	$lng5dec = round($lng,5);

	$calculator = $this->moonphasecalculator;

        //calculate sun and moon info
	for($i = 0;$i < $iPomDni;$i++):
       //    $tPom = $timestamp + ($i * $iPom);
	   $tPom = strtotime('+'.$i.' days',$timestamp);

	   $aPomDalsie[$i] = date_sun_info($tPom,$lat5dec,$lng5dec); 
           $tPomDalsie[$i] = $tPom;

           $aPomMoonDalsie[$i] = $calculator->calculateMoonphase(date('Y', $tPom), date('n', $tPom), date('j', $tPom));

	endfor;

        return compact('aPomDalsie','tPomDalsie','aPomMoonDalsie');

    }

}
?>
