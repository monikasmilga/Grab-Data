<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use DateTimeZone;
use Gidlov\Copycat\Copycat;
use Illuminate\Routing\Controller;

class GDDataGrabbingController extends Controller
{

    public function grabAllData()
    {
        // quantity of days from the first of october that need to be grabbed
        $i = 31;


        /*$cc = new Copycat;
        $cc->setCURL(array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER, "Content-Type: text/html; charset=iso-8859-1",
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17',
        ));


        // loops for cycle as many times as days to be grabbed starting from 1st of october
        for ($day = 1; $day <= $i; $day++) {


            // find data in URL given
            $cc->match([
                'day' => '/class="layoutcell" align="right">&nbsp;(.*?)</ms',
                'departure_airport' => '/class="depdest" title="Flight ......"><div class="content emphasize">(.*?)</ms',
                'arrival_airport' => '/class="arrdest"><div class="content">(.*?)</ms',
                'connection' => 'direct flights only',
                'departure_time' => '/class="depdest" title="Flight ......"><div class="content">(.*?)</ms',
                'arrival_time' => '/class="arrdest"><div class="content emphasize">(.*?)</ms'])
                ->matchAll(['prices' => '/title="EUR">(.*?)</ms'])->URLs('https://www.norwegian.com/uk/booking/flight-tickets/select-flight/?D_City=OSL&A_City=RIX&TripType=1&D_Day=' . $day . '&D_Month=201710&D_SelectedDay=' . $day . '&R_Day=' . $day . '&R_Month=201710&R_SelectedDay=' . $day . '&IncludeTransit=false&AgreementCodeFK=-1&CurrencyCode=EUR&rnd=65098');

            $result[] = $cc->get();
        }

        file_put_contents(storage_path('data.json'), json_encode($result));*/

        $result = json_decode(file_get_contents(storage_path('data.json')), true);

//        dd($result);

        foreach ($result as &$day) {
            foreach ($day as &$flight) {

                $priceArray = [];

                foreach ($flight['prices'] as $price) {
                    array_push($priceArray, $price);
                };

                if (sizeof($priceArray) != 0)
                    $flight['best_price'] = min($priceArray);

            }
        };
//        dd($day);
        dd($result);
//        dd($result[3]);
        return view('result', $result);


        //TODO: graziau sudeti duomenis
        // file set contents
        //TODO: sukurti storage direktorija, i kuria bus talpinami file "OSL_RIX_2017_(uzklausos laikas)"
        //TODO: front-ende dropdown'as, kuris pagal failiuko data atvaizduos duomenis ekrane
    }


    public function grabData()
    {
        // TAKES DATA FROM CALENDAR

        $startTime = time();
//        dd($startTime);

        $urlCalendar = 'https://www.norwegian.com/uk/booking/flight-tickets/farecalendar/?D_City=OSL&A_City=RIX&TripType=1&D_Day=01&D_Month=201710&R_Day=01&R_Month=201710&IncludeTransit=false&AgreementCodeFK=-1&CurrencyCode=EUR';

        $cc = new Copycat;
        $cc->setCURL(array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER, "Content-Type: text/html; charset=iso-8859-1",
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17',
        ));

        $cc->matchAll([
            'data' => '!<div class="fareCalPrice">(.*?)<\/div>!',
        ])->URLs($urlCalendar);
        $result = $cc->get();

        $i = 1;
        foreach ($result as &$array) {
            foreach ($array as &$item) {
                foreach ($item as $key => $price) {

                    $item[$key] = [
                        'day' => $i,
                        'price' => (float)$price,
                        'url_fare' => intval(round($price))
                    ];
                    $i++;
                }
            }
        }

        // CHECKS EACH DAY (THAT HAS FLIGHTS) FOR TAXES & OTHER REQUIRED FLIGHT DETAILS, COUNTS DURATION

        $cc = new Copycat;
        $cc->setCURL(array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER, "Content-Type: text/html; charset=iso-8859-1",
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17',
        ));


        foreach ($item as $dayData) {


            if ($dayData['url_fare'] !== 0) {

                $urlDay = 'https://www.norwegian.com/uk/booking/flight-tickets/select-flight/?D_City=OSL&A_City=RIX&TripType=1&D_SelectedDay=' . $dayData['day'] . '&D_Day=' . $dayData['day'] . '&D_Month=201710&R_Day=' . $dayData['day'] . '&R_Month=201710&dFare=' . $dayData['url_fare'] . '&IncludeTransit=false&AgreementCodeFK=-1&CurrencyCode=EUR';

                $cc->match([
                    'taxes' => '/class="rightcell emphasize" align="right" valign="bottom">(.*?)</ms',
                    'day' => '/class="layoutcell" align="right">&nbsp;(.*?)</ms',
                    'departure_time' => '/class="depdest" title="Flight ......"><div class="content emphasize">(.*?)</ms',
                    'arrival_airport' => '/class="arrdest"><div class="content">(.*?)</ms',
                    'departure_airport' => '/class="depdest" title="Flight ......"><div class="content">(.*?)</ms',
                    'arrival_time' => '/class="arrdest"><div class="content emphasize">(.*?)</ms'])
                    ->URLs($urlDay);

                $item = $cc->get();
                $item[0]['day'] = str_replace('&nbsp;', ' ', $item[0]['day']);
                $item[0]['taxes'] = (float)(str_replace('€', '', $item[0]['taxes']));
                $item[0]['price'] = $dayData['price'];
                $item[0]['connection airport'] = 'Task: "Data should only be collected for direct flights"';

                $fullData[] = $item;
            }
        }


        $fullData['duration'] = (time()) - $startTime;
        dd($fullData);

        return view('result', $fullData);

        // takes 28 requests to grab exact veriefied data
        // if we say we know the schedule pattern, we could do it with 3 queries: 1 for all calendar with lowest prices, 1 for taxes and departure/arrival times on sundays, 1 for departure/arrival times on other days
    }
}