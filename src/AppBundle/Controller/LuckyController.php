<?php
// src/AppBundle/Controller/LuckyController.php
namespace AppBundle\Controller;

//aby som mohol ist cez kontroller twig, toto pridame
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Ob\HighchartsBundle\Highcharts\Highchart;
use AppBundle\Service\SunInfoSet;


//aby som mohol ist cez controller twig, pridal som extends Controller
class LuckyController extends Controller
{
  /**
   * @Route("/lucky/number", name="luckyn")
   */
  public function numberAction()
  {
  $number = rand(0, 100);
  
//ak nejdem cez Controller twig
// return new Response(
//  '<html><body>Šťastné číslo: ' . $number. '</body></html>'
//  );
  
  //render twigom
  return $this->render('lucky/number.html.twig', array(
  'number' => $number,
  ));

  }

  /**
   * @Route("/lucky/chart/{lat}/{lng}", name="chart")
   */
  public function chartAction($lat=48, $lng=20)
  {
        //variables 
        date_default_timezone_set('Europe/Bratislava');
	$lat5dec = round($lat,5);
	$lng5dec = round($lng,5);

	//sun info, get service
        $suninfo = $this->get('app.SunInfoSet');
	$res = $suninfo->calculateSunInfoRange($lat5dec,$lng5dec,20);
        $aPomDalsie = $res['aPomDalsie'];
        $tPomDalsie = $res['tPomDalsie'];

        $aData = array();
        $iPom = 1;

      foreach ($aPomDalsie as $aPomCyk):
//        $aData[] =  array(date('H',$aPomCyk['sunrise']), $iPom  );
	$aData[] = array($tPomDalsie[$iPom]*1000,$this->time_to_decimal(date('H:i:s',$aPomCyk['sunrise'])));
	$fData[] = array($tPomDalsie[$iPom]*1000,$this->time_to_decimal(date('H:i:s',$aPomCyk['civil_twilight_begin'])));
	$cData[] = array($tPomDalsie[$iPom]*1000,$this->time_to_decimal(date('H:i:s',$aPomCyk['sunset'])));

	$dData[] = array($tPomDalsie[$iPom]*1000,$this->time_to_decimal(date('H:i:s',$aPomCyk['nautical_twilight_begin'])));
	$eData[] = array($tPomDalsie[$iPom]*1000,$this->time_to_decimal(date('H:i:s',$aPomCyk['astronomical_twilight_begin'])));

//	$aData[] = $tPomDalsie[$iPom];

//	$aData[] = date('d.m.Y',$tPomDalsie[$iPom]);
//	 $aData[]  = $iPom;
        $iPom++;

      endforeach;	  

    //chart
    $series = array(
 	array("type" => 'line', "name" => "Začiatok astronomického šera",  "data" => $eData,"pointInterval" => 3600 * 1000 * 24 ),
	array("type" => 'line', "name" => "Začiatok nautického šera",  "data" => $dData,"pointInterval" => 3600 * 1000 * 24 ),
   //     array("type" => 'line', "name" => "Západ slnka",  "data" => $cData,"pointInterval" => 3600 * 1000 * 24),
        //array("type" => 'line', "name" => "Začiatok občianskeho šera",  "data" => $fData,"pointInterval" => 3600 * 1000 * 24),
	array("type" => 'line', "name" => "Východ slnka",  "data" => $aData, "pointInterval" => 3600 * 1000 * 24)

    );

    $ob = new Highchart();
    $ob->chart->renderTo('linechart'); //The $id of the div where to render the chart
    $ob->chart->zoomType('xy');
    $ob->title->text('Východ slnka');
    $ob->xAxis->title(array('text' => "Dni" ));
    $ob->xAxis->type('datetime');
    $formatter = new \Zend\Json\Expr('function () {
	       // nemožno to použiť takto, lebo vyššie robím z timestampu decimal, pre poučenie som nechal tento kod,
	       // var date = new Date(this.y); //toto je šikovné  new Date(this.y).toString().split(" ")[4];
	       //var hh = date.getUTCHours();
	       //var mm = date.getUTCMinutes();
	       //var ss = date.getSeconds();

               //return date + "-" + hh + ":" + mm + ":" + ss;
	      // return date ;
	
	      var time = 0;
	      var hh = 0; 
	      var mm = 0;
	      var ss = 0;

	      time = this.y;
	      hh = Math.floor(time) 
	      mm = Math.floor( (time - hh)* 60 )
	      ss =  (((time - hh) * 60) - mm)*60
	      ss = Math.round(ss);
	      return hh+":"+mm+":"+ss;
  }');
    $formatter2 = new \Zend\Json\Expr('function() {
	      // užitočné: var date = new Date(this.value).toString().split(" ")[4];
	      var time = 0;
	      var hh = 0; 
	      var mm = 0;
	      var ss = 0;

	      time = this.value; //toto je ine ako vo formatter vyssie
	      hh = Math.floor(time) 
	      mm = Math.floor( (time - hh)* 60 )
	      ss = (((time - hh) * 60) - mm)*60
              ss = Math.round(ss);

          //    window.alert(hh+":"+mm+":"+ss);
              return  hh+":"+mm+":"+ss;

  }');

//      $ob->yAxis->min('01:00:00');
    $ob->yAxis(array('labels' => array('formatter' => $formatter2, 'style' => array('color' => '#AA4643')), 'title' => array( 'text' => "Čas"), 'type' => ('linear')));
//    $ob->yAxis->type('time');
//    $ob->yAxis->dateTimeLabelFormats(array( 'millisecond' => "%Hx%Mx%S", 'second' => "%Hx%Mx%S",'minute' => '%Hx%Mx%S','hour' => '%Hx%Mx%S','day' => '%Hx%Mx%S','week' => '%a %H:%M:%S:%L','month' => '%H:%M:%S','year' => '%H:%M:%S'   ));
    //   $ob->yAxis->dateTimeLabelFormats(array( 'hour' => '%H:%M:%S'  ));
   // $ob->yAxis->min('01:00:00');

    $ob->tooltip->formatter($formatter);
    $ob->series($series);

    return $this->render('lucky/chart.html.twig', array( 'chart' => $ob));
  }
  public function time_to_decimal($time) 
  {
    $aPom = explode(':', $time);
//    die(print_r($aPom));
    $decTime = abs($aPom[0]) + abs($aPom[1]/60) + abs($aPom[2]/3600);
    return $decTime;
  }
}

?>
