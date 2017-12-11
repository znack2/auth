<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use EllipseSynergie\ApiResponse\Contracts\Response;
use App\Task;
use Illuminate\Support\Facades\Redis;
use App\TaskTransformer;


class TaskController extends Controller
{
    protected $response;
 
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    //Method that list of tasks
    public function index()
    {

        $posts = Redis::get('tasks:index');
        
        
        if (!$posts) {
            $posts = Task::all();
            $redis = Redis::connection();
            $redis->set('tasks:index', $posts);
        }

    	$tasks = Task::paginate(5);
        // Return a collection of $task with pagination
        $valor = $this->response->withPaginator($tasks, new  TaskTransformer());
        
        return view('tasks', ["data" => $valor->getData()->data,"pagination" => $valor->getData()->meta->pagination ] );
    }
    //Method that shows the search of a task
    public function show($id)
    {  
        $posts = Redis::get('tasks:find');
        
        if (!$posts) {
            $posts = Task::find($id);
            $redis = Redis::connection();
            $redis->set('tasks:find', $posts);
        }
        //Get the task
        $task = Task::find($id);
        if (!$task) {
            return $this->response->errorNotFound('Task Not Found');
        }
        // Return a single task
        return $this->response->withItem($task, new  TaskTransformer());
    }
 
    public function destroy($id)
    {
        
        //Get the task
        $task = Task::find($id);
        if (!$task) {
            return $this->response->errorNotFound('Task Not Found');
        }
 
        if($task->delete()) {
             return $this->response->withItem($task, new  TaskTransformer());
        } else {
            return $this->response->errorInternalError('Could not delete a task');
        }
        
 
 
    }
 
    public function store(Request $request)  {
        
        if ($request->isMethod('put')) {
            
            //Get the task
            $task = Task::find($request->task_id);
            
            if (!$task) {
                return $this->response->errorNotFound('Task Not Found');
            }
          
        } else {
            $task = new Task;
            $task->created_at = date("Y-m-d H:i:s");    
        }
        if ($request->input('title') != "" || $request->input('due_date') != "" ){
            $task->title = $request->input('title');
            $task->description = $request->input('description');
            $task->due_date = $request->input('due_date');
            $task->completed = $request->input('completed');
            $task->updated_at = date("Y-m-d H:i:s");
        }else{
            return $this->response->errorInternalError('Incomplete data error');
        }
 
        if($task->save()) {
            return $this->response->withItem($task, new  TaskTransformer());
        } else {
             return $this->response->errorInternalError('Could not updated/created a task');
        }
    }

    public function store2($id, Request $request)
    {
        $message = Message::create([
            'body' => $request->input('message'),
            'conversation_id' => $id,
            //need to change sender_id to use $this->user->id;
            'sender_id' => Auth::user()->id,
            'type' => 'user_message'
        ]);
        $redis = Redis::connection();
        $data = ['message' => $request->input('message'), 'user' => Auth::user()->name,
         'room' => $message->conversation_id];
        $redis->publish('message', json_encode($data));
        Redis::get('message');
        response()->json([]);
        
    }
}