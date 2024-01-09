<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Models;
use Carbon\Carbon;

class ModelController extends Controller
{
    public function list()
    {
        $models = Models::select('id', 'title')->get();
        return response()->json($models);
    }

    public function statusChange($id)
    {
        $model = Models::find($id);

        if (!$model) {
            return response()->json(['message' => 'Model tapılmadı'], 404);
        }

        // Status'u dəyişdir
        $model->status = $model->status == 1 ? 0 : 1;
        $model->save();

        return response()->json([
            'message' => 'Modelin statusu uğurla yeniləndi',
            'data' => [
                'id' => $model->id,
                'title' => $model->title,
                'brand' => $model->brand,
                'status' => $model->status
            ]
        ]);
    }

    public function singleView(Request $request)
    {
        $query = Models::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%$search%");
        }

        $models = $query->get();

        // timestamps-ları format edib göndərək
        $formattedModels = $models->map(function ($model) {
            return [
                'id' => $model->id,
                'title' => $model->title
            ];
        });

        return response()->json($formattedModels);
    }

    public function filterBy(Request $request)
    {
        $query = Models::query();

        if ($request->filled('selectedIds')) {
            $selectedIds = $request->input('selectedIds', []);
            $query->whereIn('id', $selectedIds);
        }

        $models = $query->get();

        $formattedModels = $models->map(function ($model) {
            return [
                'id' => $model->id,
                'title' => $model->title,
                'status' => $model->status,
                'created_at' => Carbon::parse($model->created_at)->format('d.m.Y H:i'),
                'updated_at' => Carbon::parse($model->updated_at)->format('d.m.Y H:i')
            ];
        });

        return response()->json(['models' => $formattedModels]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Models::query();

        if ($request->has('sort')) {
            $sortKey = $request->input('sort');
            $sortValue = $request->has('sortValue') ? $request->input('sortValue') : 'asc';
            $query->orderBy($sortKey, $sortValue);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%$search%");
        }

        if ($request->has('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        $models = $query->get();

        // timestamps-ları format edib göndərək
        $formattedModels = $models->map(function ($model) {
            return [
                'id' => $model->id,
                'title' => $model->title,
                'status' => $model->status,
                'created_at' => Carbon::parse($model->created_at)->format('d.m.Y H:i'),
                'updated_at' => Carbon::parse($model->updated_at)->format('d.m.Y H:i')
            ];
        });

        return response()->json($formattedModels);
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
    public function store(Request $request)
    {
        $request->validate([
            'brand_id'  => 'required|exists:brands,id', //brand_id 'brands' table-ının id-lərindən biri olmalıdır
            'title'     => 'required|string'
        ]);

        $model = Models::create([
            'brand_id'  => $request->brand_id,
            'title'     => $request->title
        ]);

        return response()->json(['message' => 'Model uğurla əlavə edildi', 'data' => $model], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $model = Models::find($id);

        if (!$model) {
            return response()->json(['message' => 'Model tapılmadı'], 404);
        } else {
            $brand = $model->brand; //modelin markası (brand-i)

            return response()->json([
                'id'         => $model->id,
                'title'      => $model->title,
                'brand'      => $brand ? [
                    'id'         => $brand->id,
                    'title'      => $brand->title,
                    'status'     => $brand->status,
                    'created_at' => Carbon::parse($brand->created_at)->format('d.m.Y H:i'),
                    'updated_at' => Carbon::parse($brand->updated_at)->format('d.m.Y H:i')
                ] : null,
                'status'     => $model->status,
                'created_at' => Carbon::parse($model->created_at)->format('d.m.Y H:i'),
                'updated_at' => Carbon::parse($model->updated_at)->format('d.m.Y H:i')
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $model = Models::find($id);

        if (!$model) {
            return response()->json(['message' => 'Model tapılmadı'], 404);
        } else {
            $request->validate([
                'brand_id'  => 'required|exists:brands,id', //brand_id 'brands' table-ının id-lərindən biri olmalıdır
                'title'     => 'required|string',
                'status'    => 'boolean'
            ]);

            $result = $model->update([
                'brand_id'  => $request->input('brand_id'),
                'title'     => $request->input('title'),
                'status'    => $request->input('status')
            ]);

            if ($result) {
                $brand = $model->brand;

                return response()->json([
                    'message'   => 'Model uğurla yeniləndi',
                    'data'      => [
                        'id'         => $model->id,
                        'title'      => $model->title,
                        'brand'      => $brand ? [
                            'id'         => $brand->id,
                            'title'      => $brand->title,
                            'status'     => $brand->status,
                            'created_at' => Carbon::parse($brand->created_at)->format('d.m.Y H:i'),
                            'updated_at' => Carbon::parse($brand->updated_at)->format('d.m.Y H:i')
                        ] : null,
                        'status'     => $model->status,
                        'created_at' => Carbon::parse($model->created_at)->format('d.m.Y H:i'),
                        'updated_at' => Carbon::parse($model->updated_at)->format('d.m.Y H:i')
                    ]
                ]);
            } else {
                return response()->json(['message' => 'Model təəssüf ki, yenilənmədi', 422]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
