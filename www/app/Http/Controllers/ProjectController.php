<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Project;

use Predis\Client as Redis;


class ProjectController extends Controller
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
        $project = [];
        $cachedProjects = [];

        $keys = $this->redis->keys('projects:*');

        foreach ($keys as $key) {
            array_push($cachedProjects, $this->redis->hgetall($key));
          }

        if($cachedProjects){
            $project = $cachedProjects;
        }else{
            $project = Project::with(['company', 'employee'])->get();
        }
        return response()->json($project);
    }
    



    public function store(Request $request)
    {
        // get all parameters
        $input = $request->all();

        $this->validate($request, [
            'company_id' => 'required',
            'employee_id' => 'required',
            'title' => 'required',
            'description' => 'required',
         ]);

        // create employee
        $project = Project::create([
          'company_id'=> $input['company_id'],
          'employee_id' => $input['employee_id'],
          'title' => $input['title'],
          'description' => $input['description'],
        ]);

        // set company to redis
        $this->redis->del('projects:' . $project->id);
        $this->redis->hmset('projects:' . $project->id, $project->toArray());

        // prepare response
        $response = [
          'message' => 'Project Created',
          'data' => $project,
        ];

        return response()->json($response, 201);
    }




    public function show(Request $request, $id)
    {

        $cachedProject = $this->redis->HGETALL('projects:'. $id);
        // prepare response

        if($cachedProject){
           $project =  $cachedProject;
        }else{
            $project = Project::where('id', $id)->with(['company', 'employee'])->first();
        }


        $response = [
          'message' => 'Project Detail',
          'data' => $project
        ];

        return response()->json($response, 200);
    }




    public function update (Request $request, $id){
        $input = $request->all();
   
        $this->validate($request, [
            'company_id' => 'required',
            'employee_id' => 'required',
            'title' => 'required',
            'description' => 'required',
         ]);


        $project = Project::find($id);   
        $project->first_name = $input['company_id'];
        $project->last_name = $input['employee_id'];
        $project->phone = $input['title'];
        $project->email = $input['description'];
        $project->save();

        // set project to redis
        $this->redis->del('projects:' . $project->id);
        $this->redis->hmset('projects:' . $project->id, $project->toArray());
   
        return response()->json('Project Updated Successfully.');
    }


    public function destroy(Request $request, $id){

        $project = Project::where('id',$id)->first();

        $this->redis->del('projects:' . $id);
        if($project){
            $project->delete();
            return response()->json('success', 200);
        }else{
            return response()->json('not found', 404);
        }

    }



}
