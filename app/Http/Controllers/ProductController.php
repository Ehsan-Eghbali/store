<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\Product\ProductServiceRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct (private ProductServiceRepository $productServiceRepository)
    {

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return $this->productServiceRepository->transferDataToElastic(10,1);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }

    public function search (Request $request)

    {
        $request->validate([
            'q'=>'required'
        ]);
        $filter = $request->except('q');
        return $this->productServiceRepository->search($request,$filter);
    }
}
