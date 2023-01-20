<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Attendance;
use App\Models\Attendant;
use App\Models\UserCard;

class AttendanceController extends Controller
{   
    public function registerAttendance(Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'attendant_id' => 'required|string|numeric',
            'attendant_name' => 'required|string',
            'client_name' => 'nullable|string',
            'client_phone' => 'nullable|string|digits:11',
            'user_card_code' => 'nullable|string|digits:6',
            'type' => 'required|string',
        ], [
            'client_phone.numeric' => 'O número de celular deve conter apenas números sem espaços ou caracteres especiais',
            'user_card_code.digits' => 'O número da cartela deve conter 6 caracteres.'
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $data = $validator->validated();

        if($data['user_card_code']) {
            $user_card = UserCard::where('code', $data['user_card_code'])->first();
        }

        $data['user_card_id'] = $user_card->id ?? null;
        

        $response['attendance'] = Attendance::create($data);

        if($response['attendance']) {
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Erro interno ao inserir registro. Por favor tente novamente.';

            return response()->json($response, 500);
        }
    }

    public function allAttendances(Request $request) {
        $response = ['status' => null];
        
        $last = $request->last ? date('Y-m-d H:i:s', strtotime("-$request->last days")) : null;

        if($request->filter && $last) {
            $attendances = Attendance::where('type', $request->filter)->where('created_at', '>', $last)->orderBy('created_at', 'DESC')->paginate(30);
        } else if($request->filter) {
            $attendances = Attendance::where('type', $request->filter)->orderBy('created_at', 'DESC')->paginate(30);
        } else if($last) {
            $attendances = Attendance::where('created_at', '>', $last)->orderBy('created_at', 'DESC')->paginate(30);
        } else {
            $attendances = Attendance::select('*')->orderBy('created_at', 'DESC')->paginate(30);
        }

        if($attendances) {
            foreach($attendances as $key => $attendance) {
                $attendance->date = date('d/m/Y H:i', strtotime($attendance->created_at));
                unset($attendance->created_at);
                unset($attendance->updated_at);
            }

            $response['attendances'] = $attendances;
            $response['status'] = 'success';
            
            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Erro interno ao consultar registro. Por favor tente novamente.';

            return response()->json($response, 500);
        }
    }

    public function attendantAttendance($attendant_id, Request $request) {
        $response = ['status' => null];

        $attendant = Attendant::find($attendant_id);
        if($attendant) {
            $last = $request->last ? date('Y-m-d H:i:s', strtotime("-$request->last days")) : null;

            if($request->filter && $last) {
                $attendances = Attendance::where('type', $request->filter)->where('created_at', '>', $last)->orderBy('created_at', 'DESC')->paginate(30);
            } else if($request->filter) {
                $attendances = Attendance::where('type', $request->filter)->orderBy('created_at', 'DESC')->paginate(30);
            } else if($last) {
                $attendances = Attendance::where('created_at', '>', $last)->orderBy('created_at', 'DESC')->paginate(30);
            } else {
                $attendances = Attendance::select('*')->orderBy('created_at', 'DESC')->paginate(30);
            }

            foreach($attendances as $attendance) {
                $attendance->date = date('d/m/Y H:i', strtotime($attendance->created_at));
                unset($attendance->created_at);
                unset($attendance->updated_at);
            }
            $response['attendances'] = $attendances;
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Atendente inexistente.';

            return response()->json($response, 422);
        }
    }

    public function searchClient(Request $request) {
        $response = ['status' => null];

        $q = $request->q;
        $attendant = $request->attendant;

        if($q && trim($q) != '') {
            if(Auth::user()->level === 'admin' || Auth::user()->level === 'sub-admin') {
                if(is_string($q)) {
                    $attendances = [];
                    if($attendant) {
                        $byName = Attendance::where('attendant_id', $attendant)->where('client_name', 'like', "%$q%")->get();
                        $byPhone = Attendance::where('attendant_id', $attendant)->where('client_phone', 'like', "%$q%")->get();
                        $byCardCode = Attendance::where('attendant_id', $attendant)->where('user_card_code', $q)->get();

                    } else {
                        $byName = Attendance::where('client_name', 'like', "%$q%")->get();
                        $byPhone = Attendance::where('client_phone', 'like', "%$q%")->get();
                        $byCardCode = Attendance::where('user_card_code', $q)->get();
                    }
                    

                    foreach($byName as $attendance) {
                        $attendance->date = date('d/m/Y H:i', strtotime($attendance->created_at));
                        unset($attendance->created_at);
                        unset($attendance->updated_at);

                        $attendances[] = $attendance;
                    }
                    foreach($byPhone as $attendance) {
                        $attendance->date = date('d/m/Y H:i', strtotime($attendance->created_at));
                        unset($attendance->created_at);
                        unset($attendance->updated_at);

                        $attendances[] = $attendance;                    
                    }
                    foreach($byCardCode as $attendance) {
                        $attendance->date = date('d/m/Y H:i', strtotime($attendance->created_at));
                        unset($attendance->created_at);
                        unset($attendance->updated_at);

                        $attendances[] = $attendance;
                    }

                    $response['status'] = 'success';
                    $response['attendances'] = $attendances;

                    return response()->json($response, 200);
                }
            } else if(Auth::user()->level === 'attendant') {
                $attendant = Auth::user();
                $attendances = [];

                $byName = Attendance::where('attendant_id', $attendant->id)->where('client_name', 'like', "%$q%")->get();
                $byPhone = Attendance::where('attendant_id', $attendant->id)->where('client_phone', 'like', "%$q%")->get();
                $byCardCode = Attendance::where('attendant_id', $attendant->id)->where('user_card_code', $q)->get();

                foreach($byName as $attendance) {
                    $attendance->date = date('d/m/Y H:i', strtotime($attendance->created_at));
                    unset($attendance->created_at);
                    unset($attendance->updated_at);

                    $attendances[] = $attendance;
                }
                foreach($byPhone as $attendance) {
                    $attendance->date = date('d/m/Y H:i', strtotime($attendance->created_at));
                    unset($attendance->created_at);
                    unset($attendance->updated_at);

                    $attendances[] = $attendance;                    
                }
                foreach($byCardCode as $attendance) {
                    $attendance->date = date('d/m/Y H:i', strtotime($attendance->created_at));
                    unset($attendance->created_at);
                    unset($attendance->updated_at);

                    $attendances[] = $attendance;
                }

                $response['status'] = 'success';
                $response['attendances'] = $attendances;

                return response()->json($response, 200);
            }
        }
    }
}
