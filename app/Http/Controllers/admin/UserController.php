<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Auth;
class UserController extends Controller
{
    public function index(){
        $users=User::orderBy('created_at','DESC')->paginate(10);
        return view('admin.users.users',[
            'users'=>$users
        ]);
    }
    public function edit($id){
$user=User::findOrFail($id);

            return view('admin.users.edit',[
                'user'=>$user
            ]);
    }
    public function update($id, Request $request)
    {
        // Use the $id from the route, not the authenticated user's ID
        $user = User::find($id);
    
        // Check if the user exists
        if (!$user) {
            return response()->json([
                'status' => false,
                'errors' => ['User not found']
            ]);
        }
    
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5',
        ]);
    
        if ($validator->passes()) {
            // Update the user's fields
            $user->name = $request->name;
            $user->mobile = $request->mobile;
            $user->designation = $request->designation;
            $user->save();
    
            return response()->json([
                'status' => true,
                'errors' => []
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function destroy(Request $request)
{
    $id = $request->id;
    $user = User::find($id);
    
    if ($user === null) {
        session()->flash('error', 'User not found');
        return response()->json(['status' => false]);
    }

    $user->delete();  // Correct deletion method
    session()->flash('success', 'User deleted');
    return response()->json(['status' => true]);
}

}