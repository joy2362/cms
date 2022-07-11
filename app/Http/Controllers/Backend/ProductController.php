<?php
//@dev: abdullah zahid joy
namespace App\Http\Controllers\Backend;

use App\Helpers\Interface\CrudOperation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{

    /**
     * @var
     */
     private $crud;

    /**
     * @param CrudOperation $crud
     */
     public function __construct(CrudOperation $crud){
        $this->crud = $crud;
     }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->crud->getAll();
        }
        $controller = str_replace("Controller", "", "ProductController") ;
        return view('admin.pages.'. $controller .'.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(),[
             'title' => 'required|max:191',
         ]);

               if ($validator->fails()){
                   return response()->json([
                       'status' => 400,
                       'errors' => $validator->messages()
                   ]);
               }

               $data  = $request->all();
               $this->crud->createOrUpdate($data);

               return response()->json([
                   'status' => 200,
                   'message' => "Added Successfully!!"
               ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

         $data = $this->crud->getById($id);
         return response()->json([
            'status' => 200,
            'data' =>  $data
         ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
       $validator = Validator::make($request->all(),[
            'title' => 'required|max:191',
       ]);

       if ($validator->fails()){
           return response()->json([
               'status' => 400,
               'errors' => $validator->messages()
           ]);
       }

       $data  = $request->all();
       $this->crud->createOrUpdate($data , $id);

       return response()->json([
           'status' => 200,
           'message' => "Updated Successfully!!"
       ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->crud->destroy($id);

         return response()->json([
            'status' => 200,
            'message' => "Deleted successfully!!"
         ]);
    }
}
