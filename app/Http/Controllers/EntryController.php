<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Entry;
use App\Models\Attendant;

class EntryController extends Controller
{
    public function registerEntry(Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'attendant_slug' => 'nullable|string',
        ]);
        if($validator->fails()) {
            $repsonse['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $attendant_slug = $validator->validated()['attendant_slug'] ?? null;

        if($attendant_slug) {
            $attendant = Attendant::where('slug', $attendant_slug)->first();
        }

        $entry = Entry::where('ip', $request->ip())
            ->where('attendant_id', $attendant->id ?? null)
            ->where('sale', false)
        ->first();

        if($entry) {
            $response['entry'] = $entry;
            $response['status'] = 'success';

            return response()->json($response, 200);
            
        } else {
            $response['entry'] = Entry::create([
                'ip' => $request->ip(),
                'attendant_id' => $attendant->id ?? null,
                'sale' => false,
            ]);

            if($response['entry']) {
                $response['status'] = 'success';

                return response()->json($response, 200);
            }
        }
    }   
}
