<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\ShowBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function test()
    {
        $brands = Brand::all();

        foreach ($brands as $brand) {
            $brand->update(['uuid' => Str::uuid()->toString()]);
        }
    }

    public function list()
    {
        $brands = Brand::select('id', 'title')->get();
        return response()->json($brands);
    }

    public function statusChange($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Marka tapılmadı'], 404);
        }

        // Status'u dəyişdir
        $brand->status = $brand->status == 1 ? 0 : 1;
        $brand->save();

        return response()->json([
            'message' => 'Markanın statusu uğurla yeniləndi',
            'data' => [
                'id' => $brand->id,
                'title' => $brand->title,
                'status' => $brand->status
            ]
        ]);
    }

    public function singleView(Request $request)
    {
        $query = Brand::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%$search%");
        }

        $brands = $query->get();

        $formattedBrands = $brands->map(function ($brand) {
            return [
                'id' => $brand->id,
                'title' => $brand->title
            ];
        });

        return response()->json($formattedBrands);
    }

    public function filterBy(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('selectedIds')) {
            $selectedIds = $request->input('selectedIds', []);
            $query->whereIn('id', $selectedIds);
        }

        $brands = $query->get();

        return response()->json(['brands' => $brands]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Brand::query();

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

        $brands = $query->paginate(20);

        return response()->json($brands);
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
    public function store(StoreBrandRequest $request)
    {
        $validated = $request->validated();
        $brand = Brand::create($validated);

        $chatIds = [
            config('telegram.chat_id'),
            // 1850847839
        ];

        $telegram = new TelegramController($chatIds, config('telegram.bot_token'));
        $sendMsg = $telegram->notificate(
            $brand->title,
            '/onayla ' . $brand->id,
            '/gericevir ' . $brand->id
        );

        $msg = 'Marka uğurla əlavə edildi';
        $msg .= $sendMsg['successCount'] > 0 ? ', ' . $sendMsg['successCount'] . ' mesaj uğurla göndərildi' : ', Mesaj göndərilmədi';

        return response()->json(['message' => $msg, 'data' => $brand], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowBrandRequest $request)
    {
        $validated = $request->validated();
        $brand = Brand::select('id', 'uuid', 'title', 'status', 'created_at', 'updated_at')->where('uuid', $validated['brand'])->first();

        return response()->json($brand);
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
    public function update($uuid, UpdateBrandRequest $request)
    {
        DB::beginTransaction();

        try {
            $brand = Brand::where("uuid", $uuid)->first();

            $brand->update($request->validated());

            DB::commit();

            $chatIds = [
                config('telegram.chat_id'),
                "2096068519"
                // 1850847839
            ];

            $telegram = new TelegramController($chatIds, config('telegram.bot_token'));
            $sendMsg = $telegram->notificate(
                $brand->title,
                '/onayla ' . $brand->id,
                '/gericevir ' . $brand->id
            );

            $msg = 'Marka uğurla yeniləndi';
            $msg .= $sendMsg['successCount'] > 0 ? ', ' . $sendMsg['successCount'] . ' mesaj uğurla göndərildi' : ', Mesaj göndərilmədi';

            return response()->json(['message' => $msg, 'data' => $brand], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Marka yenilənmədi', 'debug' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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
