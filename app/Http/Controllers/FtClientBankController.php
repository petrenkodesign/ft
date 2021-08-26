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

    public function toBankData(Request $request)
    {
        if($request->hasFile('packet') && $request->file('packet')->isValid()) {
          $input = $request->file('packet');
          $bank_data_xml = $input->getContent();
          $bank_data_sxml_obj = simplexml_load_string($bank_data_xml, "SimpleXMLElement", LIBXML_NOCDATA);
          $bank_data_json = json_encode($bank_data_sxml_obj);
          $bank_data = json_decode($bank_data_json,TRUE);
          $bank_data_to_db = BankPacket::create($bank_data);
          return $this->sendResponse($bank_data_to_db->toArray(), 'Bank data saveed successfully.');
        }
        else return $this->sendError('No file.');
    }

    public function sendToBank($xml, $token)
    {
      $url = url('/api/bankPaketData');
      $response = Http::withToken($token)->attach('packet', $xml, 'packet.xml')->post($url);
      echo $response;

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
