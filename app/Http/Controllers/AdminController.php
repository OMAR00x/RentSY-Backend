<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function pendingUsers()
    {
        $users = User::where('status', 'pending')->latest()->paginate(20);
        return response()->json($users);
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'approved']);
        return response()->json(['message' => 'تمت الموافقة على المستخدم']);
    }

    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'rejected']);
        return response()->json(['message' => 'تم رفض المستخدم']);
    }

    public function pendingProperties()
    {
        $properties = Property::with(['owner', 'city'])->where('status', 'pending')->latest()->paginate(20);
        return response()->json($properties);
    }

    public function approveProperty($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['status' => 'active']);
        return response()->json(['message' => 'تمت الموافقة على العقار']);
    }

    public function rejectProperty($id)
    {
        $property = Property::findOrFail($id);
        $property->update(['status' => 'inactive']);
        return response()->json(['message' => 'تم رفض العقار']);
    }
}
