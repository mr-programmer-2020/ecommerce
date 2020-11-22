<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MainCategory; 
use App\Http\Requests\MainCategoryRequest;
use Illuminate\Support\Facades\Config; // for autoload function 
use DB;
use Illuminate\Support\Str; 

class MainCategoriesController extends Controller
{
    public function index()
    {
        $default_lang= get_default_lang(); 
        $categories = MainCategory::where('translation_lang',$default_lang) ->selection()->get();

        return view('admin.maincategories.index',compact('categories')); 
    }

    public function create()
    {
        $default_lang= get_default_lang(); 
        $categories = MainCategory::where('translation_lang',$default_lang) ->selection()->get();

        return view('admin.maincategories.create'); 
    }

    public function store(MainCategoryRequest $request)
    {
        try
        {

        $main_categories = collect($request->category);
        
        // to filter all objects and take the default language
        $filter = $main_categories->filter(function ($value, $key) {
            return $value['abbr'] == get_default_lang(); 
        });  
         
        // array_values to return the object as array 
        $default_category = array_values($filter->all())[0];  
    
     
        $filePath = "";
        if($request->has('photo')) {
            
            // maincategories this is the file which his address created in config => filesystem
            $filePath = uploadImage('maincategories',$request->photo);
        }

 
        DB::beginTransaction();

        // to insert the default language 
        $default_category_id = MainCategory::insertGetId([
            
            'translation_lang' => $default_category['abbr'],
            'translation_of' => 0,
            'name' => $default_category['name'],
            'slug' => $default_category['name'],
            'photo' => $filePath
        ]);  

        // this to bring remainding languages which is not rarbic language 
        $categories = $main_categories->filter(function ($value, $key) {
            return $value['abbr'] != get_default_lang();
        });
        
        // to loop on remainding languages 
        if(isset($categories) && $categories->count() ) 
        {
            $categories_arr=[];
            foreach($categories as $category) {

                $categories_arr[] = [

                    'translation_lang' => $category['abbr'],
                    'translation_of' => $default_category_id, // to make translation from default langauge which is arabic language
                    'name' => $category['name'],
                    'slug' => $category['name'],
                    'photo' => $filePath
                ];
            }
            
            // to save the remainding languages 
            MainCategory::insert($categories_arr);
        }

        DB::commit(); 

        return redirect()->route('admin.maincategories')->with(['success' => 'تم الحفظ بنجاح ']);

        } catch(\Exeption $ex) {

            DB::rollback();

            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
             
    }

    public function edit($mainCat_id)
    {
        $mainCategory = MainCategory::with('categories')->selection()->find($mainCat_id);

        if(!$mainCategory)
        {
            return redirect('admin.maincategories') ->with(['error' => 'هذا القسم غير موجود']) ;
        }

        return view('admin.maincategories.edit',compact('mainCategory') );  
       
    }

    public function update($mainCat_id,MainCategoryRequest $request)
    {
        try{
            //validation
        $mainCategory = MainCategory::find($mainCat_id);

        if(!$mainCategory) {

            return redirect('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']) ;
        }

        //update
        $category = array_values($request->category) [0]; // because it is noly one element

        if(!$request->has('category.0.active') )  
        $request->request->add(['active' => 0 ]); 

        else 
        $request->request->add(['active' => 1 ]);   

        MainCategory::where('id',$mainCat_id) ->update([ 

            'name'=> $category['name'],
            'active' =>$request->active,
        ]);

        if($request->has('photo')) { 
            
            $filePath = uploadImage('maincategories', $request->photo);
            
            MainCategory::where('id',$mainCat_id) ->update([ // update the photo if the photo is exist 
                
            'photo' => $filePath,
        ]);
        }

        return redirect()->route('admin.maincategories')->with(['success' => 'تم ألتحديث بنجاح']);

        } catch(\Exeption $ex) {
            
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function destroy($id)
    {
        try{

            $maincayegory=MainCategory::find($id);
            if(!$maincayegory){
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);
            }

            $vendors = $maincayegory->vendors();
            if($vendors->count()>0) {
                return redirect()->route('admin.maincategories')->with(['error' => 'لأ يمكن حذف هذا القسم  ']);
            }
             
            
            $image = Str::after($maincayegory->photo,'assets/'); 
            
            $image = base_path('assets/'.$image);
            
            
            //delete transation languages
            $maincayegory->categories()->delete(); 
            
            $maincayegory->delete();
            return redirect()->route('admin.maincategories')->with(['success' => 'تم حذف القسم بنجاح']);

        } catch(\Exception $ex) {
            
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    public function changeStatus($id)
    {
        try{
            $maincayegory=MainCategory::find($id);
            if(!$maincayegory){
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود']);
            }

            $status = $maincayegory->active == 0 ? 1 : 0;
            
            $maincayegory->update(['active' => $status]); 

            return redirect()->route('admin.maincategories')->with(['success' => ' تم تغيير الحالة بنجاح ']);
            
        } catch(\Exception $ex) {
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

}