<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Collection;
use App\Models\Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;




class CollectionController extends Controller
{
    /**
     * Display a listing of the user's collections.
     */
    public function index($userId = null)
    {

        // If no user ID is provided, use the authenticated user
        if (!$userId && Auth::check()) {
            $userId = Auth::id();
        }

        // Get collections for the specified user
        $collections = Collection::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('index', compact('collections', 'userId'));
    }

    /**
     * Show the form for creating a new collection.
     */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['msg' => 'You must be logged in to create a collection.']);
        }

        return view('create');
    }

    /**
     * Store a newly created collection in storage.
     */
    public function store(Request $request)
{
    if (!Auth::check()) {
        return response()->json(['error' => 'Bạn cần đăng nhập để tạo collection'], 401);
    }

    try {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'image_id' => 'nullable|exists:images,id',
        ]);

        $collection = new Collection([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'is_public' => $request->input('is_public', true),
            'user_id' => Auth::id(),
        ]);

        $collection->save();

        // Gắn ảnh vào collection nếu có image_id
        if ($request->has('image_id')) {
            $imageId = $request->input('image_id');
            $collection->images()->attach($imageId, [
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Nếu request là AJAX, trả về JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => 'Collection đã được tạo' . ($request->has('image_id') ? ' và ảnh đã được lưu' : ''),
                'collection_id' => $collection->id
            ], 201);
        }

        // Nếu là request thông thường, chuyển hướng đến trang chi tiết collection
        return redirect()->route('collections.show', $collection->id)
            ->with('success', 'Collection created successfully!');
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'error' => 'Dữ liệu không hợp lệ',
            'messages' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Lỗi tạo collection: ' . $e->getMessage() . ' | Stack: ' . $e->getTraceAsString());
        return response()->json([
            'error' => 'Lỗi hệ thống khi tạo collection',
            'message' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Display the specified collection.
     */
    public function show($id)
    {
        $collection = Collection::with(['images' => function ($query) {
            $query->orderBy('collection_images.order');
        }])->findOrFail($id);

        // Check if the collection is private and not owned by the current user
        if (!$collection->is_public && (!Auth::check() || $collection->user_id !== Auth::id())) {
            return redirect()->route('collections.index')
                ->withErrors(['msg' => 'This collection is private.']);
        }

        return view('showcollections', compact('collection'));
    }

    /**
     * Show the form for editing the specified collection.
     */
    public function edit($id)
    {
        $collection = Collection::findOrFail($id);

        // Check if the user is authorized to edit the collection
        if (!Auth::check() || $collection->user_id !== Auth::id()) {
            return redirect()->route('collections.index')
                ->withErrors(['msg' => 'You do not have permission to edit this collection.']);
        }

        return view('editcollections', compact('collection'));
    }

    /**
     * Update the specified collection in storage.
     */
    public function update(Request $request, $id)
    {
        $collection = Collection::findOrFail($id);

        // Check if the user is authorized to update the collection
        if (!Auth::check() || $collection->user_id !== Auth::id()) {
            return redirect()->route('collections.index')
                ->withErrors(['msg' => 'You do not have permission to update this collection.']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
        ]);

        $collection->title = $request->input('title');
        $collection->description = $request->input('description');
        $collection->is_public = $request->input('is_public', true);
        $collection->save();

        return redirect()->route('collections.show', $collection->id)
            ->with('success', 'Collection updated successfully!');
    }

    /**
     * Remove the specified collection from storage.
     */
    public function destroy($id)
    {
        $collection = Collection::findOrFail($id);

        // Check if the user is authorized to delete the collection
        if (!Auth::check() || $collection->user_id !== Auth::id()) {
            return redirect()->route('collections.index')
                ->withErrors(['msg' => 'You do not have permission to delete this collection.']);
        }

        $collection->delete();

        return redirect()->route('collections.index')
            ->with('success', 'Collection deleted successfully!');
    }

    /**
     * Add an image to a collection.
     */
    public function addImage(Request $request)
{
    if (!Auth::check()) {
        return response()->json(['error' => 'Not authenticated'], 401);
    }

    try {
        $request->validate([
            'collection_id' => 'required|exists:collections,id',
            'image_id' => 'required|exists:images,id',
        ]);

        $collection = Collection::findOrFail($request->input('collection_id'));
        $image = Image::findOrFail($request->input('image_id'));

        // Kiểm tra quyền sở hữu
        if ($collection->user_id !== Auth::id()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        // Kiểm tra xem ảnh đã tồn tại chưa
        if ($collection->images()->where('image_id', $image->id)->exists()) {
            return response()->json(['error' => 'Ảnh đã tồn tại trong collection này'], 400);
        }

        // Gắn ảnh vào collection
        $maxOrder = $collection->images()->max('collection_images.order') ?? 0;
        $collection->images()->syncWithoutDetaching([
            $image->id => [
                'order' => $maxOrder + 1, // Xóa dòng này nếu không có cột order
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        //$collection->thumbnail = $image->path;
        $collection->save();

        return response()->json([
            'success' => 'Đã lưu ảnh vào bộ sưu tập!',
            'collection_id' => $collection->id
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'error' => 'Dữ liệu không hợp lệ',
            'messages' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Lỗi thêm ảnh vào collection: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        return response()->json(['error' => 'Lỗi hệ thống khi lưu ảnh', 'details' => $e->getMessage()], 500);
    }
}

    /**
     * Remove an image from a collection.
     */
    public function removeImage($collectionId, $imageId)
    {
        if (!Auth::check()) {
            return redirect()->back()->withErrors(['msg' => 'You must be logged in to remove images.']);
        }

        $collection = Collection::findOrFail($collectionId);

        // Check if the user owns the collection
        if ($collection->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['msg' => 'You do not have permission to modify this collection.']);
        }

        // Detach the image from the collection
        $collection->images()->detach($imageId);

        // Reorder remaining images
        $images = $collection->images()->orderBy('collection_images.order')->get();
        $order = 1;
        foreach ($images as $image) {
            DB::table('collection_images')
                ->where('collection_id', $collection->id)
                ->where('image_id', $image->id)
                ->update(['order' => $order]);
            $order++;
        }

        return redirect()->back()->with('success', 'Image removed from collection.');
    }

    /**
     * Update the order of images in a collection.
     */
    public function updateImageOrder(Request $request, $collectionId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $collection = Collection::findOrFail($collectionId);

        // Check if the user owns the collection
        if ($collection->user_id !== Auth::id()) {
            return response()->json(['error' => 'Not authorized'], 403);
        }

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'exists:images,id',
        ]);

        $images = $request->input('images');
        foreach ($images as $index => $imageId) {
            DB::table('collection_images')
                ->where('collection_id', $collection->id)
                ->where('image_id', $imageId)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['success' => 'Image order updated']);
    }

    /**
     * Display a form to select which collection to add an image to.
     */
    public function selectCollection($imageId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['msg' => 'You must be logged in to add images to collections.']);
        }

        $image = Image::findOrFail($imageId);
        $collections = Collection::where('user_id', Auth::id())->get();

        return view('showcollections', compact('image', 'collections'));
    }

    /**
     * Get user's collections for AJAX request.
     */
    public function getUserCollections()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $collections = Collection::where('user_id', Auth::id())
            ->select('id', 'title')
            ->get();

        return response()->json($collections);
    }
    public function showImage($id)
    {
        $image = Image::with('user')->findOrFail($id);
        $collections = auth()->check() ? Collection::where('user_id', auth()->id())->get() : collect();
        return view('images.show', compact('image', 'collections'));
    }
}
