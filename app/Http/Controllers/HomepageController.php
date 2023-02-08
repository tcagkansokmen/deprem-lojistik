<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleContent;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Facades\Validator;

class HomepageController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        $page_title = 'Yardım Organizasyonu';
        $page_description = 'Yardım Organizasyonu';

        $categories = Category::all();

        $cities = array('','Adana', 'Adıyaman', 'Afyon', 'Ağrı', 'Amasya', 'Ankara', 'Antalya', 'Artvin',
            'Aydın', 'Balıkesir', 'Bilecik', 'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa', 'Çanakkale',
            'Çankırı', 'Çorum', 'Denizli', 'Diyarbakır', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir',
            'Gaziantep', 'Giresun', 'Gümüşhane', 'Hakkari', 'Hatay', 'Isparta', 'Mersin', 'İstanbul', 'İzmir',
            'Kars', 'Kastamonu', 'Kayseri', 'Kırklareli', 'Kırşehir', 'Kocaeli', 'Konya', 'Kütahya', 'Malatya',
            'Manisa', 'Kahramanmaraş', 'Mardin', 'Muğla', 'Muş', 'Nevşehir', 'Niğde', 'Ordu', 'Rize', 'Sakarya',
            'Samsun', 'Siirt', 'Sinop', 'Sivas', 'Tekirdağ', 'Tokat', 'Trabzon', 'Tunceli', 'Şanlıurfa', 'Uşak',
            'Van', 'Yozgat', 'Zonguldak', 'Aksaray', 'Bayburt', 'Karaman', 'Kırıkkale', 'Batman', 'Şırnak',
            'Bartın', 'Ardahan', 'Iğdır', 'Yalova', 'Karabük', 'Kilis', 'Osmaniye', 'Düzce');

        $arriving = array('', 'Adana', 'Adıyaman', 'Hatay', 'Kahramanmaraş', 'Elazığ', 'Kilis', 'Diyarbakır');

        return view('homepage.index', compact('page_title', 'page_description', 'cities', 'categories', 'arriving'));
    }

    public function json()
    {
        $parameters = $this->request->query();

        $vehicles = Vehicle::with(['contents.product', 'contents.category']);

        if(isset($parameters['name'])){
            $vehicles->where('name', 'like', '%'.$parameters['name'].'%');
        }

        if(isset($parameters['to'])){
            $vehicles->where('to', 'like', '%'.$parameters['to'].'%');
        }

        if(isset($parameters['category_id'])){
            $vehs = VehicleContent::where('category_id', $parameters['category_id'])->pluck('vehicle_id')->toArray();
            $vehicles->whereIn('id', $vehs);
        }

        if(isset($parameters['status'])){
            if($parameters['status'] == 'is_arrived'){
                $vehicles->where('is_arrived', 1);
            }
            if($parameters['status'] == 'is_done'){
                $vehicles->where('is_done', 1);
            }
            if($parameters['status'] == 'on_road'){
                $vehicles->where('is_done', 0)->where('is_arrived', 0);
            }
        }

        return Datatables::of($vehicles)
            ->make(true);
    }

    public function vehicleDetail($vehicleId)
    {
        $vehicles = Vehicle::where('id', $vehicleId)->with(['contents.product', 'contents.category'])->first();

        return view('homepage.detail', compact('vehicles'));
    }

    public function vehicleUpdate($vehicleId)
    {
        $vehicles = Vehicle::where('id', $vehicleId)->with(['contents.product', 'contents.category'])->first();

        return view('homepage.update', compact('vehicles'));
    }

    public function save(Request $request)
    {
        $data = $this->request->all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'contact_name' => 'required',
            'contact_phone' => 'required',
            'from' => 'required',
            'to' => 'nullable',
            'contents.*.category_id' => 'required',
            'contents.*.product' => 'required',
            'contents.*.unit' => 'required',
            'contents.*.quantity' => 'required',
        ]);

        $niceNames = array(
            'name' => 'Plaka bilgisi',
            'contact_name' => 'İletişim kişi',
            'contact_phone' => 'İletişim telefon',
            'from' => 'Nereden',
            'to' => 'Nereye',
            'contents.*.category_id' => 'Yardım Türü',
            'contents.*.product' => 'Ürün',
            'contents.*.unit' => 'Birim',
            'contents.*.quantity' => 'Adet',
        );

        $validator->setAttributeNames($niceNames);

        if ($validator->fails()) {
            return response()->json([
                'message' => error_formatter($validator),
                'errors' => $validator->errors(),
            ]);
        }

        $vehicle = new Vehicle();
        $vehicle->name = $data['name'];
        $vehicle->from = $data['from'] ?? null;
        $vehicle->to = $data['to'] ?? null;
        $vehicle->start_at = isset($data['start_at']) ? date('Y-m-d H:i:s', strtotime($data['start_at'])) : null;
        $vehicle->end_at = isset($data['end_at']) ? date('Y-m-d H:i:s', strtotime($data['end_at'])) : null;
        $vehicle->contact_name = $data['contact_name'] ?? null;
        $vehicle->contact_phone = $data['contact_phone'] ?? null;
        $vehicle->save();

        if(isset($data['contents'])){
            foreach($data['contents'] as $content){

                $ara = Product::where('category_id', $content['category_id'])->where('name', $content['product'])->first();
                if($ara){
                    $product = Product::find($ara->id);
                }else{
                    $product = new Product();
                    $product->name = $content['product'];
                    $product->category_id = $content['category_id'];
                    $product->save();
                }

                $vehicleContent = new VehicleContent();
                $vehicleContent->vehicle_id = $vehicle->id;
                $vehicleContent->category_id = $content['category_id'];
                $vehicleContent->product_id = $product->id;
                $vehicleContent->unit = $content['unit'];
                $vehicleContent->quantity = $content['quantity'];
                $vehicleContent->save();
            }
        }
        $result = array(
            'status' => 1,
            'message' => 'Başarıyla kaydettiniz.',
        );
        return response()->json($result);
    }


    public function saveUpdate(Request $request)
    {
        $data = $this->request->all();

        $validator = Validator::make($data, [
            'id' => 'required',
            'status' => 'required',
        ]);

        $niceNames = array(
            'status' => 'Statü bilgisi',
        );

        $validator->setAttributeNames($niceNames);

        if ($validator->fails()) {
            return response()->json([
                'message' => error_formatter($validator),
                'errors' => $validator->errors(),
            ]);
        }

        $vehicle = Vehicle::find($data['id']);

        if($data['status'] == 'is_done'){
            $vehicle->is_done = 1;
        }elseif($data['status'] == 'is_arrived'){
            $vehicle->is_arrived = 1;
        }
        $vehicle->save();

        $result = array(
            'status' => 1,
            'message' => 'Başarıyla kaydettiniz.',
        );
        return response()->json($result);
    }

}
