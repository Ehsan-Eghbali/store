<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchProductRequest;
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

    public function search (SearchProductRequest $request)
    {
        try {
            $filter = $request->except('q');
            $data = $this->productServiceRepository->search($request, $filter);

            // Determine the status code based on conditions
            $statusCode = $this->getStatusCode($data);

            return $this->successResponse($data, $statusCode);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    private function successResponse($data, $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => $statusCode,
            'message' => $this->getMessage($statusCode),
            'data' => $data
        ], $statusCode);
    }

    private function errorResponse($errorMessage, $statusCode = 500): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => $statusCode,
            'message' => 'An error occurred: ' . $errorMessage,
            'data' => null // No data to return in case of an error
        ], $statusCode);
    }
    private function getStatusCode($data): int
    {
        return empty($data) ? 404 : 200;
    }

    private function getMessage($statusCode): string
    {
        return $statusCode === 200 ? 'Data retrieved successfully' : 'Data not found';
    }
}
