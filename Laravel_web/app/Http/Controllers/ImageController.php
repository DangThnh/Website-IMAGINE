<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;




class ImageController extends Controller
{
    // Hiển thị form tải lên ảnh
    public function index()
    {
        return view('upload');
    }

    // Xử lý tải lên ảnh
    public function store(Request $request)
    {

        if (!Auth::check()) {
            return redirect()->back()->withErrors(['msg' => 'You must be logged in to upload images.']);
        }

        // Xác thực dữ liệu
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:8192',
            'filename' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category' => 'required|string|max:255',

        ]);


        // Lưu file
        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = time(). '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images', $filename, 'public');

            // Lưu thông tin vào cơ sở dữ liệu

            Image::insert([
                'filename' => $request->input('filename'), // Lấy tên ảnh từ input
                'path' => 'storage/' . $path,
                'description' => $request->input('description'), // Lấy mô tả từ input
                'category' => $request->input('category'),
                'user_id' => Auth::user()->id,

            ]);
        }

        return redirect()->back()->with('success', 'Image uploaded successfully!');
    }
    public function edit($id)
{
    $image = Image::findOrFail($id); // Tìm ảnh theo ID
    if ($image->user_id !== Auth::user()->id) {
        return redirect()->route('images.gallery')->withErrors(['msg' => 'You do not have permission to edit this image.']);
    }
    return view('edit', compact('image')); // Truyền ảnh đến view sửa
}

public function update(Request $request, $id)
{
    $image = Image::findOrFail($id);

    // Xác thực dữ liệu
    $request->validate([
        'filename' => 'required|string|max:255',
        'description' => 'nullable|string|max:2000',
        'category' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:8192',
    ]);

    // Cập nhật tên và mô tả
    $image->filename = $request->input('filename');
    $image->description = $request->input('description');
    $image->category = $request->input('category');

    // Nếu có file mới, lưu file và cập nhật đường dẫn
    if ($request->file('image')) {
        // Xóa file cũ nếu cần
        if (file_exists(public_path($image->path))) {
            unlink(public_path($image->path));
        }

        $file = $request->file('image');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('images', $filename, 'public');
        $image->path = 'storage/' . $path; // Cập nhật đường dẫn mới
    }

    $image->save(); // Lưu thay đổi vào cơ sở dữ liệu


    return redirect()->back()->with('success', 'Image update successfully!');
}

    public function destroy($id)
    {
        $image = Image::findOrFail($id);

        // Xóa file từ hệ thống
        if (file_exists(public_path($image->path))) {
            unlink(public_path($image->path));
        }

        $image->delete(); // Xóa bản ghi từ cơ sở dữ liệu

        return redirect('/images/gallery');
    }
    public function show()
    {
            $images = Image::all(); // Lấy tất cả ảnh từ cơ sở dữ liệu
            return view('gallery', compact('images')); // Truyền danh sách ảnh đến view
    }

    public function showDetail($id) // Thêm tham số ID
{
    $image = Image::with('user')->findOrFail($id); // Lấy bức ảnh và tác giả
    $take = Image::where('id', $id)->get(); // Lấy bức ảnh để sử dụng trong comment
    $otherImages = Image::where('user_id', $image->user_id)
                         ->where('id', '!=', $image->id) // Lọc ra các bức tranh khác
                            ->get();
    $collections = auth()->check() ? Collection::where('user_id', auth()->id())->get() : collect(); // Ân tạo biến $collections

    return view('show', compact('image', 'take', 'otherImages','collections'));
}
    public function search(Request $request)
{
    $request->validate([
        'query' => 'required|string|max:255',
        //'query2' => 'required|string|max:255'
    ]);

    $query = $request->input('query');

    // Tìm kiếm hình ảnh theo tên và sắp xếp theo filename
    $images = Image::where('filename', 'like', "%{$query}%")
                   ->orderBy('filename') // Sắp xếp theo tên
                   ->get();

                //    $query2 = $request->input('query2'); // Ví dụ từ khóa nhập vào
                //    $keywords = explode(' ', $query2); // Tách chuỗi thành mảng từ khóa

                //    $images = Image::where(function ($queryBuilder) use ($keywords) {
                //        foreach ($keywords as $keyword) {
                //            $queryBuilder->where(function ($subQuery) use ($keyword) {
                //                $subQuery->where('category', 'like', "% #{$keyword} %") // Tìm kiếm với dấu cách trước và sau
                //                         ->orWhere('category', 'like', "#{$keyword} %") // Dấu # ở đầu
                //                         ->orWhere('category', 'like', "% #{$keyword}") // Dấu # ở cuối
                //                         ->orWhere('category', 'like', "#{$keyword}"); // Chỉ từ khóa với dấu #
                //            });
                //        }
                //    })
                //    ->orderBy('category')
                //    ->get();

                $images = Image::where('category', 'like', "% #{$query} %") // Tìm kiếm với dấu cách trước và sau
                ->orWhere('category', 'like', "#{$query} %") // Dấu # ở đầu và không có dấu cách sau
                ->orWhere('category', 'like', "% #{$query}") // Dấu # ở cuối và không có dấu cách trước
                ->orWhere('category', 'like', "#{$query}") // Chỉ từ khóa với dấu #
                ->orderBy('category')
                ->get();

    return view('gallery', compact('images'));
}
}

