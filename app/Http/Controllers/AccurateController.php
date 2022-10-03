<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemAccurate;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class AccurateController extends Controller
{
    public function addData(Request $request) {
        try {
            $validate = Validator::make($request->all(), [
                "name" => "required",
                "item_type" => "required",
                "no" => "string|min:6"
            ]);
            if($validate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validate->errors()->first()
                ]);
            }

            ItemAccurate::create([
                "name" => $request->name,
                "item_type" => $request->item_type,
                "no" => $request->no,
                "notes" => $request->notes,
                "percent_taxable" => $request->percent_taxable,
                "unit_price" => $request->unit_price
            ]);
            return response()->json(['status' => true, 'message' => "success save to db"]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function storeData() {
        try {
            $itemAccurate = ItemAccurate::where('status', 0)->first();
            if(!$itemAccurate) {
                return response()->json([
                    'status' => false,
                    'message' => "there are no accurate items that have not been saved via the ACCURATE ID api"
                ]);
            }

            $params = [
                "name" => $itemAccurate->name,
                "itemType" => $itemAccurate->item_type,
                "no" => $itemAccurate->no == null ? "100000" : $itemAccurate->no,
                "notes" => $itemAccurate->notes == null ? "" : $itemAccurate->notes,
                "percentTaxable" => $itemAccurate->percent_taxable == null ? 0 : $itemAccurate->percent_taxable,
                "unitPrice" => $itemAccurate->unit_price == null ? 0.0 : floatval($itemAccurate->unit_price)
            ];

            $x_session_id = "";
            if(isset($_COOKIE['session_id'])) {
                $x_session_id = $_COOKIE['session_id'];
            } else {
                $x_session_id = $this->openDb();
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://zeus.accurate.id/accurate/api/item/save.do',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => array(
                  'x-session-id: ' .$x_session_id,
                  'Authorization: Bearer '.env('ACCESS_TOKEN_ACCURATE'),
                  'Content-Type: application/json'
                ),
              ));
            $response = json_decode(curl_exec($curl));
            curl_close($curl);

            if($response->s) {
                $itemAccurate->status = 1;
                $itemAccurate->save();
                return response()->json([
                    'status' => $response->s,
                    'message' => $response->d
                ]);
            } else {
                return response()->json([
                    'status' => $response->s,
                    'message' => $response->d
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function bulkData() {
        try {
            $itemAccurate = ItemAccurate::where('status', 0)->get();
            if(!$itemAccurate) {
                return response()->json([
                    'status' => false,
                    'message' => "there are no accurate items that have not been saved via the ACCURATE ID api"
                ]);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function openDb() {
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://account.accurate.id/api/open-db.do',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_POSTFIELDS => array('id' => '636311'),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '. env('ACCESS_TOKEN_ACCURATE')
        ),
        ));

        $response = json_decode(curl_exec($curl));
        setcookie("session_id", $response->session, time() + 3600);
        return $response->session;
    }
}
