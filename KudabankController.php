<?php

namespace Modules\Kudabank\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log; 
use Kuda\KudaEncyption\KudaEncyption;


class KudabankController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $bank_list = $this->bank_list();
        dd($bank_list);

       $name_enquiry = $this->name_enquiry(["beneficiaryAccountNumber"=> '1100000734','bankCode' => '999129']);
        dd($name_enquiry);

        $transactions = $this->transactions(["startDate"=> '2019-10-22T10:22:25','endDate' => '2020-10-22T10:22:25']);
        dd( $transactions );

        dd( (collect($transactions->Data->transactions)->where('part_tran_type', 'C')->sum('total')) );

        return view('kudabank::index');
    }
    public  function bank_list($bank_code = null)
    {
        $BANK_LIST = ["ServiceType" => "BANK_LIST","RequestRef"=>rand()];
        $bank_list =  $this->fetchkuda($BANK_LIST);

        return $bank_list;
    }

    public  function fetchkuda($payload){
    
        $payload = json_encode($payload);

        $encryption = new KudaEncyption();
        $client_key = 'CGZFw5lPQbMaXf3Y426v';
        $random_str = rand();

        $aes_password = $client_key.'-'.$random_str;
        $ecrypted_data =  $encryption->AESEncrypt($payload, $aes_password);
        $kuda_public_key = file_get_contents('xml/kuda.xml');
        $ecrypted_password =  $encryption->RSAEncrypt($aes_password, $kuda_public_key);

        $request_body =   ['data' => $ecrypted_data];
        $request_header =  ['password' => $ecrypted_password];
        $request = ['header' => $request_header, 'body' => $request_body ];
   

        $headers = array( 
        "Accept: */*",
        "Content-Encoding: gzip",
        "Content-Type: application/json",
        "password: $ecrypted_password");
      
        $data_string = json_encode($request_body);    
                                                                                                                
        $ch = curl_init('https://kudaopenapi.azurewebsites.net/v1');                                                                      
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);                                                                                                                   
                                                                                                                     
        $result = curl_exec($ch);

        $response = json_decode($result, true);
        if(isset($response['data'])){
            $kuda_private_key = file_get_contents('xml/CGZFw5lPQbMaXf3Y426v.xml');
            $decrypted_password =  $encryption->RSADecrypt($response['password'], $kuda_private_key);
            $decrypted_data =  $encryption->AESDecrypt($response['data'], $decrypted_password); 
            return json_decode($decrypted_data); 
        }

        return false;

    }

    public  function name_enquiry($data)
    {
        $NAME_ENQUIRY  = ["ServiceType" => "NAME_ENQUIRY","RequestRef"=>rand(), "Data"=>["beneficiaryAccountNumber"=> '1100000734','bankCode' => '999129']];

        $result =  $this->fetchkuda($NAME_ENQUIRY);
    
        dd($result);
        return $result;
    }
    public  function transactions($data)
    {
        $TRANSACTIONS_AND_BALANCE_ENQUIRY = ["ServiceType" => "TRANSACTIONS_AND_BALANCE_ENQUIRY","RequestRef"=>rand(), "Data"=> $data];
        $transactions =  $this->fetchkuda($TRANSACTIONS_AND_BALANCE_ENQUIRY);

        return $transactions;
    }

    public  function create_virtual_account($data)
    {
        $CREATE_VIRTUAL_ACCOUNT = ["ServiceType" => "CREATE_VIRTUAL_ACCOUNT","RequestRef"=>rand(), "Data"=> $data];
        $result =  $this->fetchkuda($CREATE_VIRTUAL_ACCOUNT);

        return $result;
    }

    public  function onboarding($data)
    {
        $ONBOARDING = ["ServiceType" => "ONBOARDING","RequestRef"=>rand(), "Data"=> $data];
        $result =  $this->fetchkuda($ONBOARDING);

        return $result;
    }

    

    
    public  function single_fund_transfer($data)
    {
        $SINGLE_FUND_TRANSFER = ["ServiceType" => "SINGLE_FUND_TRANSFER","RequestRef"=>rand(), "Data"=> $data];
        $result =  $this->fetchkuda($SINGLE_FUND_TRANSFER);

        return $result;
    }

    public  function retrieve_virtual_account($data = ['trackingReference'=>'000000'])
    {
        $RETRIEVE_VIRTUAL_ACCOUNT = ["ServiceType" => "RETRIEVE_VIRTUAL_ACCOUNT","RequestRef"=>rand(), "Data"=> $data];
        $result =  $this->fetchkuda($RETRIEVE_VIRTUAL_ACCOUNT);


        return ($result);
    }

    public  function virtual_account_fund_transfer($data)
    {
        $VIRTUAL_ACCOUNT_FUND_TRANSFER = ["ServiceType" => "VIRTUAL_ACCOUNT_FUND_TRANSFER","RequestRef"=>rand(), "Data"=> $data];
        $result =  $this->fetchkuda($VIRTUAL_ACCOUNT_FUND_TRANSFER);

        return $result;
    }

 
}
