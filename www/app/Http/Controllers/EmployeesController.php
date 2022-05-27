<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Employee;

use Predis\Client as Redis;


class EmployeesController extends Controller
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
        $emplyees = [];
        $cachedEmployees = [];

        $keys = $this->redis->keys('employees:*');

        foreach ($keys as $key) {
            array_push($cachedEmployees, $this->redis->hgetall($key));
          }

        if($cachedEmployees){
            $emplyees = $cachedEmployees;
        }else{
            $emplyees = Employee::with('company')->get();
        }
        return response()->json($emplyees);
    }
    



    public function store(Request $request)
    {
        // get all parameters
        $input = $request->all();

        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required',
         ]);

        // create employee
        $employee = Employee::create([
          'company_id'=> $input['company_id'],
          'first_name' => $input['first_name'],
          'last_name' => $input['last_name'],
          'phone' => $input['phone'],
          'email' => $input['email'],
        ]);

        // set company to redis
        $this->redis->del('employees:' . $employee->id);
        $this->redis->hmset('employees:' . $employee->id, $employee->toArray());

        // prepare response
        $response = [
          'message' => 'Employee Created',
          'data' => $employee,
        ];

        return response()->json($response, 201);
    }




    public function show(Request $request, $id)
    {

        $cachedEmployee = $this->redis->HGETALL('employees:'. $id);
        // prepare response

        if($cachedEmployee){
           $employee =  $cachedEmployee;
        }else{
            $employee = Employee::where('id', $id)->with('company')->first();
        }


        $response = [
          'message' => 'Employee Detail',
          'data' => $employee
        ];

        return response()->json($response, 200);
    }


    public function destroy(Request $request, $id){

        $emplyee = Employee::where('id',$id)->first();

        $this->redis->del('employees:' . $id);
        if($emplyee){
            $emplyee->delete();
            return response()->json('success', 200);
        }else{
            return response()->json('not found', 404);
        }

    }


    public function update (Request $request, $id){
        $input = $request->all();
   
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'email' => 'required',
         ]);


        $employee = Employee::find($id);   
        $employee->first_name = $input['first_name'];
        $employee->last_name = $input['last_name'];
        $employee->phone = $input['phone'];
        $employee->email = $input['email'];
        $employee->save();

        // set employee to redis
        $this->redis->del('employees:' . $employee->id);
        $this->redis->hmset('employees:' . $employee->id, $employee->toArray());
   
        return response()->json('Employee Updated Successfully.');
    }




    public function projects (Request $request, $id){

        $cachedEmployee = $this->redis->HGETALL('employees:'. $id);
        // prepare response

        if($cachedEmployee){
           $employee =  $cachedEmployee;
        }else{
           $employee = Employee::where('id', $id)->with('projects')->first();
        }

        return response()->json($employee['projects'], 200);


    }



}
