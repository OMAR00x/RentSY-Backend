<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;

class SearchController extends Controller
{
    use ResponseTrait;

    public function history(Request $request)
    {
        $searches = SearchHistory::where('user_id', $request->user()->id)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json($searches);
    }

    public function delete(Request $request, $id)
    {
        $search = SearchHistory::findOrFail($id);

        if ($search->user_id !== $request->user()->id) {
            return $this->errorResponse('غير مصرح لك بحذف هذا البحث', 403);
        }

        $search->delete();

        return $this->successResponse(null, 'تم حذف البحث');
    }

    public function clear(Request $request)
    {
        SearchHistory::where('user_id', $request->user()->id)->delete();

        return $this->successResponse(null, 'تم مسح سجل البحث');
    }
}
