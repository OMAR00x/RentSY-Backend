<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Apartment;
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

    public function pendingApartments()
    {
        $apartments = Apartment::with(['owner', 'city'])->where('status', 'pending')->latest()->paginate(20);
        return response()->json($apartments);
    }

    public function approveApartment($id)
    {
        $apartment = Apartment::findOrFail($id);
        $apartment->update(['status' => 'active']);
        return response()->json(['message' => 'تمت الموافقة على العقار']);
    }

    public function rejectApartment($id)
    {
        $apartment = Apartment::findOrFail($id);
        $apartment->update(['status' => 'inactive']);
        return response()->json(['message' => 'تم رفض العقار']);
    }
}
