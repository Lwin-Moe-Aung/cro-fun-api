<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

use App\Page;
use App\Library\Library;
use DB;


class PagesController extends ApiController
{


    /*
     * Retrieve all the pages' record
    */
    public function index()
    {

        $pages = DB::table('pages')->select('id','title','description','route')->get();
        return $this->respond([

            'data'=>$pages

        ]);    
    }


    /*
     * Insert the page's record into the database
     */
    public function store(Request $request)
    {

        $page = Page::where('route',$request->get('route'))->first();
        if($page) {

            return $this->respondNotFound('This page is already created');

        }
        $page = new Page();
        $page->title = $request->get('title');
        $page->description = $request->get('description');
        $page->route = $request->get('route');
        $page->admin_id = $request->get('admin_id');
        $page->save();
        return $this->respondCreated("Page is successfully created");

    }

    /*
     * Retrieve a page's record
     * By page's route
     */
    public function show($slug)
    {
        $page = page::where('route',$slug)->first();
        if($page) {
            return $this->respond([
                'data'=>$page
            ]);
        }
        return $this->respondNotFound('The page is not found');
    }


    /*
     * Retrieve a page's record
     * By page's id
     */
    public function showById($id)
    {
        $page = page::where('id',$id)->first();

        if($page) {

            return $this->respond([

                'data'=>$page

            ]);
        }
        return $this->respondNotFound('The page is not found');

    }


    /*
      * Update a page's record
      * By page's id
      */
    public function update(Request $request , $id)
    {
        $page = Page::find($id);
        if(!$page) {

            return $this->respondNotFound('This page is already created');

        }
        $page->title = $request->get('title');
        $page->description = $request->get('description');
        $page->route = $request->get('route');
        $page->admin_id = $request->get('admin_id');
        $page->save();
        return $this->respondCreated("Page is successfully Updated");

    }

    /*
    * Soft delete the page's record
    * By page's id
    */
    public function destroy($id)
    {
        $page = Page::find($id);
        if(!$page) {

            return $this->respondNotFound('Page does not exist');

        }
        $page->delete();

    }
}
