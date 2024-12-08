<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;


class UserController extends Controller
{ 
    public function index(Request $request){

        if($request->ajax()){

            $startDate = $request->startDate  ?? null;
            $endDate = $request->endDate  ?? null;

          $users = User::query();

        if ($startDate && $endDate) {
            $users->whereBetween('created_at',[$startDate , $endDate]);
        }
            return DataTables::eloquent($users)

             // Add Index Column
             ->addIndexColumn()

            
            //Add Date Format Column
            
            ->addColumn('created_at',function($user){
                return Carbon::parse($user->created_at)->format('d-M-Y');
            }) 

             // Add Action Column

             ->addColumn('action',function($user){
                return '
                <button data-id="'. $user->id.'" class="btn btn-success btn-sm edit-user">Edit</button>
                <button data-id="'. $user->id.'" class="btn btn-danger btn-sm delete-user">Delete</button>
                ';
            })

            
            ->rawColumns(['action'])

            ->make(true);


        }

        return view('users');

        // $users = User::all();
        // return view('users',compact('users'));
    }

    public function destroy($id){
        $user = User::findOrFail($id);
    
        if($user){
          $user->delete();
          return response()->json(['status' => 'success', 'message'=> 'User Deleted Successfully!']);
        }
    
        return response()->json(['status' => 'failed', 'message'=> 'Unable to Delete User!']);
    
    
    }

    /**
 * function: update
 * Description: Update User
 * @param object $request
 * @return void
 */

 public function update(Request $request){

    try{
        $user = User::findOrFail($request->id);
if ($user) {
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone_number = $request->phone_number;
    $user->save();

    return response()->json(['status'=> 'success', 'message' => 'User Updated Successfully']);
    
}
return response()->json(['status'=> 'error', 'message' => 'Unable to find user']);

    }catch(Exception $e){
        return response()->json(['status'=> 'error', 'message' => 'Unable to Update user'.$e->getMessage()]);


    }

 }

}
