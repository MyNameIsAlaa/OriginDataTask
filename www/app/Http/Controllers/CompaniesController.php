<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Company;
use App\Models\Project;
use Predis\Client as Redis;


class CompaniesController extends Controller
{

    protected $redis;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->redis = new Redis([
            'scheme' => 'tcp',
            'host'   => env('REDIS_HOST'),
            'port'   => env('REDIS_PORT'),
          ]);
    }

    //


    public function index(){
        $companies = [];
        $cachedCompanies = [];

        $keys = $this->redis->keys('companies:*');

        foreach ($keys as $key) {
            array_push($cachedCompanies, $this->redis->hgetall($key));
          }

        if($cachedCompanies){
            $companies = $cachedCompanies;
        }else{
            $companies = Company::all();
        }
        return response()->json($companies);
    }
    



    public function store(Request $request)
    {
        // get all parameters
        $input = $request->all();

        $this->validate($request, [
            'name' => 'required',
         ]);

        // create company
        $company = Company::create([
          'name' => $input['name'],
        ]);

        // set company to redis
        $this->redis->del('companies:' . $company->id);
        $this->redis->hmset('companies:' . $company->id, $company->toArray());

        // prepare response
        $response = [
          'message' => 'Company Created',
          'data' => $company,
        ];

        return response()->json($response, 201);
    }




    public function show(Request $request, $id)
    {

        $cachedCompany = $this->redis->HGETALL('companies:'. $id);
        // prepare response

        if($cachedCompany){
           $company =  $cachedCompany;
        }else{
            $company = Company::where('id', $id)->with('employees')->first();
        }


        $response = [
          'message' => 'Company Detail',
          'data' => $company
        ];

        return response()->json($response, 200);
    }


    public function update (Request $request, $id){
        $input = $request->all();
   
        $this->validate($request, [
            'name' => 'required',
         ]);


        $company = Company::find($id);   
        $company->name = $input['name'];
        $company->save();

        // set company to redis
        $this->redis->del('companies:' . $company->id);
        $this->redis->hmset('companies:' . $company->id, $company->toArray());
   
        return response()->json('Company Updated Successfully.');
    }


    public function destroy(Request $request, $id){
        $company = Company::where('id',$id)->first();
        $this->redis->del('companies:' . $id);
        if($company){
            $company->delete();
            return response()->json('success', 200);
        }else{
            return response()->json('not found', 404);
        }

        


    }



    public function projects (Request $request, $id){

        $cachedCompany = $this->redis->HGETALL('companies:'. $id);
        // prepare response

        if($cachedCompany){
           $company =  $cachedCompany;
        }else{
           $company = Company::where('id', $id)->with('projects')->first();
        }

        return response()->json($company['projects'], 200);


    }

}
