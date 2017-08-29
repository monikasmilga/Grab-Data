<?php namespace App\Http\Controllers;

use Gidlov\Copycat\Copycat;
use Illuminate\Routing\Controller;

class GDDataGrabbingController extends Controller
{

    /**
     * Display a listing of the resource.
     * GET /datagrabbing
     *
     * @return Response
     */
    public function index()
    {

        $config['grabbed'] = file_get_contents('https://www.norwegian.com/uk/booking/flight-tickets/select-flight/?D_City=OSL&A_City=RIX&TripType=1&D_Day=01&D_Month=201710&D_SelectedDay=01&R_Day=01&R_Month=201710&R_SelectedDay=01&IncludeTransit=false&AgreementCodeFK=-1&CurrencyCode=EUR&rnd=65098');

        return view('grabbed', $config);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * Collects required information from one day, when exact URL is given
     */

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
            foreach ($day as  &$flight) {

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


    /**
     * Show the form for creating a new resource.
     * GET /datagrabbing/create
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * POST /datagrabbing
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     * GET /datagrabbing/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     * GET /datagrabbing/{id}/edit
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * PUT /datagrabbing/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /datagrabbing/{id}
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}