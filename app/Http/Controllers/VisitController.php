<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    // 📖 جلب كل زيارات المستخدم
    public function index(Request $request)
    {
        $visits = Visit::where('user_id', $request->user()->id)
                       ->orderBy('visit_date', 'desc')
                       ->get()
                       ->map(function ($visit) {
                           if ($visit->photo) {
                               $visit->photo_url = asset('storage/' . $visit->photo);
                           }
                           return $visit;
                       });

        return response()->json($visits);
    }

    // ➕ إضافة زيارة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'country'      => 'required|string',
            'country_code' => 'required|string|size:2',
            'city'         => 'required|string',
            'visit_date'   => 'required|date',
            'notes'        => 'nullable|string',
            'rating'       => 'nullable|integer|min:1|max:5',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'photo'        => 'nullable|image|max:5120',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('visits', 'public');
        }

        $visit = Visit::create([
            'user_id'      => $request->user()->id,
            'country'      => $request->country,
            'country_code' => $request->country_code,
            'city'         => $request->city,
            'visit_date'   => $request->visit_date,
            'notes'        => $request->notes,
            'rating'       => $request->rating ?? 5,
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'photo'        => $photoPath,
        ]);

        return response()->json($visit, 201);
    }

    // 🔍 جلب زيارة واحدة
    public function show(Request $request, Visit $visit)
    {
        if ($visit->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        if ($visit->photo) {
            $visit->photo_url = asset('storage/' . $visit->photo);
        }

        return response()->json($visit);
    }

    // ✏️ تعديل زيارة
    public function update(Request $request, Visit $visit)
    {
        if ($visit->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $request->validate([
            'country'      => 'sometimes|string',
            'country_code' => 'sometimes|string|size:2',
            'city'         => 'sometimes|string',
            'visit_date'   => 'sometimes|date',
            'notes'        => 'nullable|string',
            'rating'       => 'nullable|integer|min:1|max:5',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'photo'        => 'nullable|image|max:5120',
        ]);

        // تحديث البيانات النصية
        $visit->update($request->except(['photo', '_method']));

        // تحديث الصورة لو أُرسلت
        if ($request->hasFile('photo')) {
            if ($visit->photo) {
                \Storage::disk('public')->delete($visit->photo);
            }
            $visit->photo = $request->file('photo')->store('visits', 'public');
            $visit->save();
        }

        if ($visit->photo) {
            $visit->photo_url = asset('storage/' . $visit->photo);
        }

        return response()->json($visit);
    }

    // 🗑️ حذف زيارة
    public function destroy(Request $request, Visit $visit)
    {
        if ($visit->user_id !== $request->user()->id) {
            return response()->json(['message' => 'غير مصرح'], 403);
        }

        $visit->delete();

        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
}