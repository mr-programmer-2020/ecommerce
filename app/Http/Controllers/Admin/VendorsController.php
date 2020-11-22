<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use App\Models\MainCategory;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VendorCreated;
use DB;
use Illuminate\Support\Str; 

class VendorsController extends Controller
{
    public function index()
    {
        $vendors = Vendor::selection()->paginate(PAGINATION_COUNT);

        return view('admin.vendors.index',compact('vendors')); 
    }

    public function create()
    {
        $categories = MainCategory::where('translation_of', 0)->active()->get();
        return view('admin.vendors.create', compact('categories'));
        
    }

    public function store(VendorRequest $request)
    {
        try{

            if(!$request->has('active'))
            $request->request->add(['active' => 0 ]); 
             
            else
            $request->request->add(['active' => 1]); 
            
            $filepath= "";
            if($request->has('logo')) {
                
                $filepath= uploadImage('vendors', $request->logo); 
            }

            $vendor = Vendor::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'active' => $request->active,
                'logo' => $filepath,
                'password' => $request->password, 
                'category_id' => $request->category_id
            ]); 

            
            Notification::send($vendor, new VendorCreated($vendor));

            return redirect()->route('admin.vendors')->with(['success' => 'تم الحفظ بنجاح ']);

        } catch(\Exception $ex) {
            
           return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }   
    }

    public function edit($id)
    {
       
        try {

            $vendor = Vendor::Selection()->find($id);

            if(!$vendor){
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا ']);
            }
            
            $categories = MainCategory::where('translation_of', 0)->active()->get();

            return view('admin.vendors.edit',compact('vendor','categories'));
        } catch (\Exeption $ex) {
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
               
    }

    public function update($id, VendorRequest $request)
    {
        try{

            $vendor = Vendor::Selection()->find($id);
            if(!$vendor) {
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا ']); 
            }
            DB::beginTransaction();
            
            //chech photo
            if($request->has('logo')){
                $filepath = uploadImage('vendors',$request->logo);
                Vendor::where('id',$id)
                ->update([
                    'logo' =>$filepath,
                ]);
            }
            
            //check active
            if(!$request->has('active'))
            $request->request->add(['active'=> 0 ]);
            else
            $request->request->add(['active'=> 1 ]); 
            
            
            // bring all elements from the table except those
            $data=$request->except('_token','id','logo','password');
            
            if($request->has('password') && !is_null($request->password) ){

                $data['password'] = $request->password;
            }
           
            Vendor::where('id',$id) 
            ->update(
                $data
            );
            

            DB::commit();
            return redirect()->route('admin.vendors')->with(['success' => 'تم التحديث بنجاح']);

        } catch(\Exeption $ex) {
            DB::rollback();
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا' ]);
        }
    }

      public function destroy($id)
    {
        try{

            $vendor=Vendor::find($id);
            if(!$vendor){
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا ']);
            }

           
            $image = Str::after($vendor->logo,'assets/'); 
           
            $image = base_path('assets/'.$image);
            
            $vendor->delete(); 
            return redirect()->route('admin.vendors')->with(['success' => 'تم حذف المتجر بنجاح']);

        } catch(\Exception $ex) {
            
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
    

     

     public function changeStatus($id)
    {
        try{
            $vendor=Vendor::find($id);
            if(!$vendor){
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا']);
            }

            $status = $vendor->active == 0 ? 1 : 0;
            
            $vendor->update(['active' => $status]); 

            return redirect()->route('admin.vendors')->with(['success' => ' تم تغيير الحالة بنجاح ']);
            
        } catch(\Exception $ex) {
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

}