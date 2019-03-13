<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Http\Controllers\Api\ApiController;

class CategoryController extends ApiController
{

    /*
     * Retrieve all the categories' record
     */
    public function index()
    {
        $categories = Category::all();
        return $this->respond([
            'data' => $categories->toArray()
        ]);
    }
}
