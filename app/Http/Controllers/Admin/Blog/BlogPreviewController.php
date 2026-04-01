<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Models\blogs\Blog;
use Illuminate\Support\Facades\DB;

class BlogPreviewController extends Controller
{
    public function blogPreview($encId)
    {
        $id = Crypt::decryptString($encId);

        $blog = Blog::findOrFail($id);

        $blogAttributes = DB::connection('mysql_dev')
            ->table('blog_attributes')
            ->where('blog_id', $id)
            ->get()
            ->groupBy('attribute_type');

        return view('admin.blogs.blogPreview', compact('blog', 'blogAttributes'));
    }
    public function updateStatus(Request $request)
    {
        $id = Crypt::decryptString($request->blog_id);
        $status = $request->status;

        $blog = Blog::findOrFail($id);

        $blog->active_status = $status;

        if ($status == 1) {
            $blog->published_at = now();
        }

        $blog->save();

        return redirect()->route('blogs.index')->with([
            'level' => 'success',
            'message' => $status == 1 ? 'Blog Published Successfully' : 'Blog Saved as Draft'
        ]);
    }
}
