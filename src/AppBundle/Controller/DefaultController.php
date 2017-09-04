<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Service\MoonPhaseCalculator;
use AppBundle\Service\SunInfoSet;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        //define variables	
        $timestamp = time(); 
	$res = array();
	
	//moon info, get service
	$calculator = $this->get('app.MoonPhaseCalculator');
	$aPomMoon = $calculator->calculateMoonphase(date('Y', $timestamp), date('n', $timestamp), date('j', $timestamp));

	//sun info, get service
        $suninfo = $this->get('app.SunInfoSet');
	$res = $suninfo->calculateSunInfoSet();

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
	    'aPom' =>  $res['aPom'], 
	    'aPomVn' => $res['aPomVn'],
	    'aPomPo' => $res['aPomPo'],
	    'aPomCe' => $res['aPomCe'],
	    'aPomPi' => $res['aPomPi'],
	    'aPomTa' => $res['aPomTa'],
	    'aPomVo' => $res['aPomVo'],
	    'aPomSk' => $res['aPomSk'],
	    'aPomVoK' => $res['aPomVoK'],
	    'aPomSr' => $res['aPomSr'],
	    'aPomMp' => $res['aPomMp'],
	    'aPomNa' => $res['aPomNa'],
	    'aPomPl' => $res['aPomPl'],
	    'aPomCv' => $res['aPomCv'],
	    'aPomBu' => $res['aPomBu'],
	    'aPomLi' => $res['aPomLi'],
	    'aPomNi' => $res['aPomNi'],
	    'aPomMf' => $res['aPomMf'],
	    'aPomOr' => $res['aPomOr'],
	    'aPomZv' => $res['aPomZv'],
	    'aPomBe' => $res['aPomBe'],
	    'aPomBk' => $res['aPomBk'],
	    'aPomSou' => $res['aPomSou'],
	    'aPomSla' => $res['aPomSla'],
	    'aPomSum' => $res['aPomSum'],
	    'aPomVys' => $res['aPomVys'],
	    'aPomVyz' => $res['aPomVyz'],
	    'aPomBr' => $res['aPomBr'],
	    'aPomKr' => $res['aPomKr'],
	    'aPomJi' => $res['aPomJi'],
	    'aPomSn' => $res['aPomSn'],
	    'aPomDe' => $res['aPomDe'],
	    'aPomJe' => $res['aPomJe'],
	    'aPomDr' => $res['aPomDr'],
	    'aPomRa' => $res['aPomRa'],
	    'datum' => date('d.m.Y',$timestamp),
	    'aPomMoon' => $aPomMoon,
        ]);
    }

    /**
     * @Route("/jedenbod/{lat}/{lng}", name="jedenbod")
     */
    public function jedenbodAction($lat=48, $lng=20)
    {
	$calculator = $this->get('app.MoonPhaseCalculator');

        $timestamp = time();
        $aPomMoon = $calculator->calculateMoonphase(date('Y', $timestamp), date('n', $timestamp), date('j', $timestamp));
	$lat5dec = round($lat,5);
	$lng5dec = round($lng,5);
	$aPom = date_sun_info(time(),$lat5dec,$lng5dec); 

	//sun info, get service
        $suninfo = $this->get('app.SunInfoSet');
	$res = $suninfo->calculateSunInfoRange($lat5dec,$lng5dec);

        return $this->render('default/jedenbod.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
	    'aPom' => $aPom,
   	    'datum' => date('d.m.Y',$timestamp),
	    'aPomMoon' => $aPomMoon,
	    'lat' => $lat5dec,
	    'lng' => $lng5dec,
	    'aPomDalsie' => $res['aPomDalsie'],
	    'tPomDalsie'  => $res['tPomDalsie'],
	    'aPomMoonDalsie'  => $res['aPomMoonDalsie'],

        ]);
    }    

    /**
     * @Route("/csvexport/{lat}/{lng}", name="csvexport")
     */


    public function csvExportAction($lat=48, $lng=20)
    {
      //variables
      $aData = array();

      //row data for today
      $lat5dec = round($lat,5);
      $lng5dec = round($lng,5);
      $timestamp = time();

      $calculator = $this->get('app.MoonPhaseCalculator');
      $aPomMoon = $calculator->calculateMoonphase(date('Y', $timestamp), date('n', $timestamp), date('j', $timestamp));
      $aPom = date_sun_info(time(),$lat5dec,$lng5dec); 

      date_default_timezone_set('Europe/Bratislava');

      //sun info, get service
      $suninfo = $this->get('app.SunInfoSet');
      $res = $suninfo->calculateSunInfoRange($lat5dec,$lng5dec);


      $excel_subor = '/tmp/dummy_1_'.date('Ymd');
      $fp = fopen($excel_subor, 'w');
      //field names definition for csv export file,
      //example of conversion from utf8 to windows : $polia_nazvy[] = iconv('UTF-8', 'cp1250', __('Id'));
      $polia_nazvy[] = 'lat';
      $polia_nazvy[] = 'lng';
      $polia_nazvy[] = 'dátum';
      $polia_nazvy[] = 'začiatok astronomického šera';
      $polia_nazvy[] = 'začiatok nautického šera';
      $polia_nazvy[] = 'začiatok občianskeho šera';
      $polia_nazvy[] = 'východ Slnka';
      $polia_nazvy[] = 'západ Slnka';
      $polia_nazvy[] = 'koniec občianskeho šera';
      $polia_nazvy[] = 'koniec nautického šera';
      $polia_nazvy[] = 'koniec astronomického šera';
      $polia_nazvy[] = 'fáza mesiaca';


      // example of tabulator as delimiter (http://www.asciitable.com/)
      if(isset($fp))       fputcsv($fp, $polia_nazvy, chr(011)); 

      $aData[] = $lat5dec;
      $aData[] = $lng5dec;
      $aData[] =  date('d.m.Y',$timestamp);
      $aData[] = date('H:i:s',$aPom['astronomical_twilight_begin']);
      $aData[] = date('H:i:s',$aPom['nautical_twilight_begin']);
      $aData[] = date('H:i:s',$aPom['civil_twilight_begin']);
      $aData[] = date('H:i:s',$aPom['sunrise']);
      $aData[] = date('H:i:s',$aPom['sunset']);
      $aData[] = date('H:i:s',$aPom['civil_twilight_end']);
      $aData[] = date('H:i:s',$aPom['nautical_twilight_end']);
      $aData[] = date('H:i:s',$aPom['astronomical_twilight_end']);
      $aData[] = $aPomMoon['phaseNr'];

      if(isset($fp)) fputcsv($fp, $aData, chr(011)); // ak chcem ako oddelovac poli pouzit tabulator (http://www.asciitable.com/)
      unset($aData);
      $iPom = 1;
      $aPomDalsie = $res['aPomDalsie'];
      $tPomDalsie = $res['tPomDalsie'];
      $aPomMoonDalsie = $res['aPomMoonDalsie'];

      foreach ($aPomDalsie as $aPomCyk):
        $aData[] = $lat5dec;
        $aData[] = $lng5dec;
        $aData[] =  date('d.m.Y',$tPomDalsie[$iPom]);
        $aData[] = date('H:i:s',$aPomCyk['astronomical_twilight_begin']);
        $aData[] = date('H:i:s',$aPomCyk['nautical_twilight_begin']);
        $aData[] = date('H:i:s',$aPomCyk['civil_twilight_begin']);
        $aData[] = date('H:i:s',$aPomCyk['sunrise']);
        $aData[] = date('H:i:s',$aPomCyk['sunset']);
        $aData[] = date('H:i:s',$aPomCyk['civil_twilight_end']);
        $aData[] = date('H:i:s',$aPomCyk['nautical_twilight_end']);
        $aData[] = date('H:i:s',$aPomCyk['astronomical_twilight_end']);
        $aData[] = $aPomMoonDalsie[$iPom]['phaseNr'];
        $iPom++;
        if(isset($fp)) fputcsv($fp, $aData, chr(011)); // ak chcem ako oddelovac poli pouzit tabulator (http://www.asciitable.com/)

        unset($aData);
      endforeach;	  

      if(isset($fp))
      {
        fclose($fp);
      }
      $response = new Response();
      $response->headers->set("Cache-Control", "no-store, no-cache, must-revalidate");
      $response->headers->set("Cache-Control", "post-check=0, pre-check=0");
      $response->headers->set('Content-Type', 'application/force-download');
      $response->headers->set('Content-Type', 'application/download');
      $response->headers->set('Expires', gmdate('D, d M Y H:i:s') . ' GMT');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Pragma', 'no-cache');
      if(isset($fp))
      {
        $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=windows-1250');
        $response->headers->set('Content-Disposition', 'attachment; filename="export_data_csv_'.date('Ymd').'.xls"');
      }

      $response->setContent(file_get_contents($excel_subor, 'rb'));
      $response->send();
      unlink($excel_subor);
    }

     /**
     * @Route("/csvexportyear/{lat}/{lng}", name="csvexportyear")
     */


    public function csvExportYearAction($lat=48, $lng=20)
    {
      //variables
      $aData = array();

      //row data for today
      $lat5dec = round($lat,5);
      $lng5dec = round($lng,5);
      $thisYear = date('Y', time());
      date_default_timezone_set('Europe/Bratislava');

      //sun info, get service
      $suninfo = $this->get('app.SunInfoSet');
      $res = $suninfo->calculateSunInfoYear($lat5dec,$lng5dec,$thisYear);
 //     $res = $suninfo->calculateSunInfoRange($lat5dec,$lng5dec);


      $excel_subor = '/tmp/dummy_1_'.date('Ymd');
      $fp = fopen($excel_subor, 'w+');
      //field names definition for csv export file,
      //example of conversion from utf8 to windows : $polia_nazvy[] = iconv('UTF-8', 'cp1250', __('Id'));
      $polia_nazvy[] = 'lat';
      $polia_nazvy[] = 'lng';
      $polia_nazvy[] = 'dátum';
      $polia_nazvy[] = 'začiatok astronomického šera';
      $polia_nazvy[] = 'začiatok nautického šera';
      $polia_nazvy[] = 'začiatok občianskeho šera';
      $polia_nazvy[] = 'východ Slnka';
      $polia_nazvy[] = 'západ Slnka';
      $polia_nazvy[] = 'koniec občianskeho šera';
      $polia_nazvy[] = 'koniec nautického šera';
      $polia_nazvy[] = 'koniec astronomického šera';
      $polia_nazvy[] = 'fáza mesiaca';


      // example of tabulator as delimiter (http://www.asciitable.com/)
      if(isset($fp))       fputcsv($fp, $polia_nazvy, chr(011)); 

      $iPom = 0;
      $aPomDalsie = $res['aPomDalsie'];
      $tPomDalsie = $res['tPomDalsie'];
      $aPomMoonDalsie = $res['aPomMoonDalsie'];

      foreach ($aPomDalsie as $aPomCyk):
        $aData[] = $lat5dec;
        $aData[] = $lng5dec;
        $aData[] = date('d.m.Y',$tPomDalsie[$iPom]);
        $aData[] = date('H:i:s',$aPomCyk['astronomical_twilight_begin']);
	/*
        $aData[] = date('H:i:s',$aPomCyk['nautical_twilight_begin']);

        $aData[] = date('H:i:s',$aPomCyk['civil_twilight_begin']);
        $aData[] = date('H:i:s',$aPomCyk['sunset']);
        $aData[] = date('H:i:s',$aPomCyk['civil_twilight_end']);
        $aData[] = date('H:i:s',$aPomCyk['nautical_twilight_end']);
*/

        $aData[] = date('H:i:s',$aPomCyk['astronomical_twilight_end']);
        $aData[] = $aPomMoonDalsie[$iPom]['phaseNr'];
        if(isset($fp)) fputcsv($fp, $aData, chr(011)); // ak chcem ako oddelovac poli pouzit tabulator (http://www.asciitable.com/)
        unset($aData);

        $iPom++;
      endforeach;	  

      if(isset($fp))
      {
        fclose($fp);
      }

      $response = new Response();
      $response->headers->set("Cache-Control", "no-store, no-cache, must-revalidate");
      $response->headers->set("Cache-Control", "post-check=0, pre-check=0");
      $response->headers->set('Content-Type', 'application/force-download');
      $response->headers->set('Content-Type', 'application/download');
      $response->headers->set('Expires', gmdate('D, d M Y H:i:s') . ' GMT');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Pragma', 'no-cache');
      if(isset($fp))
      {
        $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=windows-1250');
        $response->headers->set('Content-Disposition', 'attachment; filename="export_data_'.$thisYear.'_csv_'.date('Ymd').'.xls"');
      }

      $response->setContent(file_get_contents($excel_subor, 'rb'));
      $response->send();
      unlink($excel_subor);
    }
 
     /**
     * @Route("/amsrviniyear/{lat}/{lng}", name="amsrviniyear")
     */


    public function amsrviniyear($lat=48, $lng=20)
    {
     $cPom = ''; 
     $polia_nazvy = '[2016]';
      //row data for today
      $lat5dec = round($lat,5);
      $lng5dec = round($lng,5);
      $thisYear = date('Y', time());
      date_default_timezone_set('Europe/Bratislava');

      //sun info, get service
      $suninfo = $this->get('app.SunInfoSet');
      $res = $suninfo->calculateSunInfoYear($lat5dec,$lng5dec,$thisYear);


      $amsrvini_subor = '/tmp/dummy_1_'.date('Ymdhs');
      $fp = fopen($amsrvini_subor, 'w+');

      //field names definition for csv export file,
      //example of conversion from utf8 to windows : $polia_nazvy[] = iconv('UTF-8', 'cp1250', __('Id'));
      $polia_nazvy = '['.$thisYear.']'."\r\n"; //only \n creates a unix type file \r\n creates a dos type

      // example of tabulator as delimiter (http://www.asciitable.com/)

     if(isset($fp))       fwrite($fp, $polia_nazvy); 

      $iPom = 0;
      $aPomDalsie = $res['aPomDalsie'];
      $tPomDalsie = $res['tPomDalsie'];
      $iPomPole = count($aPomDalsie);
      foreach ($aPomDalsie as $aPomCyk):
        $cPom = date('md',$tPomDalsie[$iPom]);
        $cPom .= ' = ';
        $cPom .= date('H:i',$aPomCyk['sunrise']);
	$cPom .= ' ';
        $cPom .= date('H:i',$aPomCyk['sunset']);
        $iPom++;
        if($iPom < $iPomPole) $cPom .= "\r\n";

        if(isset($fp)) fwrite($fp, $cPom); // ak chcem ako oddelovac poli pouzit tabulator (http://www.asciitable.com/)
        unset($cPom);
      endforeach;	  

      if(isset($fp))
      {
        fclose($fp);
      }
      $response = new Response();
      $response->headers->set("Cache-Control", "no-store, no-cache, must-revalidate");
      $response->headers->set("Cache-Control", "post-check=0, pre-check=0");
      $response->headers->set('Content-Type', 'application/force-download');
      $response->headers->set('Content-Type', 'application/download');
      $response->headers->set('Expires', gmdate('D, d M Y H:i:s') . ' GMT');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Pragma', 'no-cache');

      if(isset($fp))
      {
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="AMSrv_slunce.ini"');
      }

      $response->setContent(file_get_contents($amsrvini_subor,'rb'));
      $response->send();
      unlink($amsrvini_subor);
    }
}
?>
