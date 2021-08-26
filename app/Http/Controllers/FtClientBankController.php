<?php

namespace App\Http\Controllers;

use App\Models\ClientPacket;
use App\Models\BankPacket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Validator;
use Spatie\ArrayToXml\ArrayToXml;

class FtClientBankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getClientData(Request $request)
    {
      $input = $request->all();
      $validation = Validator::make($input, [
            'packet' => 'required'
      ]);
      if($validation->fails()){
        return $this->sendError('No client data.', $validation->errors());
      }

      $client_data = json_decode($input['packet'], true);
      $client_data_to_db = ClientPacket::create($client_data);

      $bank_data_xml = ArrayToXml::convert($client_data);
      $token = $request->bearerToken();
      $this->sendToBank($bank_data_xml, $token);
    }

    public function toBankData()
    {
        //
    }

    public function sendToBank($xml, $token)
    {
      $url = url('/api/bankPaketData');
      $options = [
      'headers' => [
        'Authorization' => 'Bearer '.$token,
        'Content-Type' => 'text/xml; charset=UTF8'
      ],
      'body' => $xml,
      ];

      $respons = Http::withToken($token)->post($url);
      return response()->json($response, 200);
    }

    private function sendResponse($result, $message)
    {
      $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404)
    {
      $response = [
            'success' => false,
            'message' => $error,
        ];
        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        return response()->json($response, $code);
    }

}
