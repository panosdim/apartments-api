<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckTinController extends Controller
{
    protected $soapClient;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Create the client object
        $this->soapClient = new \SoapClient('https://ec.europa.eu/taxation_customs/tin/checkTinService.wsdl');
    }

    /**
     * Check for valid TIN.
     *
     * @param Request $request
     * @param  $tin
     * @return LesseeResourceCollection
     */
    public function checkTin(Request $request, $tin)
    {
        //Use the functions of the client, the params of the function are in
        //the associative array
        $params = array('countryCode' => 'EL', 'tinNumber' => $tin);
        $response = $this->soapClient->checkTin($params);

        return json_encode($response);
    }
}
