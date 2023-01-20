<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\User;
use App\Models\Card;
use App\Models\UserCard;
use App\Models\Bet;
use App\Models\PublicRanking;
use App\Models\Championship;
use App\Models\Game;
use App\Models\AdminOpt;
use App\Models\Receipt;
use App\Models\Attendant;
use App\Models\Attendance;
use App\Models\Sale;

use App\Helpers\APIFootballHelper;
use App\Helpers\GazetaBrasileiraoScrapHelper;

class AdminController extends Controller
{   
    public function __construct() {
        $this->middleware('active');
    }

    public function createCard(Request $request) {
        $response = ['status' => null];
        $validator = Validator::make($request->all(), [
            'matchs' => 'required|array|min:1',
            'price' => 'required|numeric',
            'type' => 'required|string|in:detailed,common',
            'name' => 'string',
            'host_percentage' => 'required|numeric|min:0|max:100|max_digits:3',
            'bonus' => 'nullable|numeric',
            'valuation' => 'required|numeric'
        ],[
            'matchs.rquired' => 'É necessario adicionar partidas para criar uma cartela.',
            'price.required' => 'A cartela precisa ter um preço.',
            'type.required' => 'É  necessario escolher o tipo da cartela.',
            'type.in' => 'Os valor do tipo deve ser detalhado ou comum.',
            'matchs.min' => 'É necessario no minimo uma partida para criar uma cartela.',
            'price.numeric' => 'O preço precisa ser um número.',
            'round.min' => 'A rodada não pode ser menos do que a 1ª',
            'round.max' => 'Não existem rodadas além da 38ª',
            'host_percentage.required' => 'É necessario a porcentagem do organizador.',
            'host_percentage.min' => 'A porcentagem do organizador não pode ser menor do que 0%.',
            'host_percentage.max' => 'A porcentagem do organizador não pode ser maior do que 100%.',
            'host_percentage.max_digits' => 'A  porcentagem deve ser  inserida com no maximo 3 digitos.',
            'valuation.required' => 'É necessario inserir a estimativa de premiação da cartela.',
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();
            return response()->json($response, 422);
        }

        if($request->endtime) {
            $endtime = date_create($request->endtime);
            $endtime = date_timestamp_get($endtime);
            if($endtime >= strtotime('now')) {
                $endtime = date('Y-m-d H:i:s', $endtime);

            } else {
                $response['status'] = 'error';
                $response['error'] = ['endtime' => ['A data de encerramento precisa ser depois de agora.']];
                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = ['endtime' => ['É necessario enviar a data e hora de encerramento']];
            return response()->json($response, 422);
        }

        $matchs = '';
        foreach($validator->validated()['matchs'] as $match) {
            $matchs .= $match.';';
        }

        $response['card'] = Card::create([
            'name' => $validator->validated()['name'],
            'matchs' => $matchs,
            'endtime' => $endtime,
            'price' => $validator->validated()['price'],
            'type' => $validator->validated()['type'],
            'host_percentage' => $validator->validated()['host_percentage'],
            'bonus' => $validator->validated()['bonus'],
            'valuation' => $validator->validated()['valuation'],
        ]);

        if($response['card']) {
            $response['status'] = 'success';
            return response()->json($response, 200);

        } else {
            $response['error'] = 'Erro interno ao salvar a cartela. Por favor tente novamente.';
            return response()->json($response, 500);
        }
    }

    public function deleteCard($id) {
        $response = ['status' => null];
        $card = Card::find($id);

        if($card) {
            $response['deleted'] = $card->delete();
            if($response['deleted']) {
                $response['status'] = 'success';
                 
                return response()->json($response, 200);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Erro interno ao tentar deletar. Por favor tente novamente.';

                return response()->json($response, 500);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }
    }

    public function userBets() {
        $response = ['status' => null];

        $user_cards = UserCard::select('*')->orderBy('created_at', 'DESC')->paginate(20);
        if(count($user_cards) > 0) {
           
            foreach($user_cards as $user_card){
                foreach($user_card->bets as $key => $bet) {
                    if($bet->match_src === 'API_FOOTBALL') {
                        $match = APIFootballHelper::requestMatch($bet->match_id);
    
                    } else if ($bet->match_src === 'Gazeta_Scrapper') {
                        $match = GazetaBrasileiraoScrapHelper::match($bet->match_round, $bet->match_id);
    
                    } else if($bet->match_src === 'Custom') {
                        $match = Game::find($bet->match_id);
                    }   

                    $bet->match = $match;
                }
                $user_card->card;
            }

            $response['status'] = 'success';
            $response['user_bets'] = $user_cards;

            return response()->json($response, 200);
            
            
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Ainda não há nenhuma aposta no sistema.';

            return response()->json($response, 200);
        }
    }

    public function updateUserCard($id ,Request $request) {
        $response = ['status' => null];

        $validator = Validator::make($request->all(), [
            'name' => 'string|min:2',
            'phone' => 'string|min:16|max:16',
            'bets' => 'array',
        ], [
            'name.min' => 'O nome precisao mínimo 2 caracteres.',
            'phone.min' => 'Número de celular inválido.',
            'phone.max' => 'Número de celular inválido',
        ]);
        if($validator->fails()) {
            $response['status'] = 'error';
            $response['error'] = $validator->errors();

            return response()->json($response, 422);
        }

        $data = $validator->validated();

        $user_card  = UserCard::find($id);
        if($user_card) {
            if(count($user_card->bets) === count($data['bets'])) {
                $user_card->name = $data['name'];
                $user_card->phone = $data['phone'];
                $user_card->save();

                if($user_card->card->type === 'common') {
                    foreach($user_card->bets as $bet) {
                        foreach($data['bets'] as $_bet) {
                            $_bet = json_decode($_bet);
                            if($bet->id === $_bet->id) {
                                $bet->bet = $_bet->bet;
                                $bet->save();
                            }
                        }
                    }
                }
                
                if($user_card->card->type === 'detailed') {
                    foreach($user_card->bets as $bet) {
                        foreach($data['bets'] as $_bet) {
                            $_bet = json_decode($_bet);

                            if($bet->id === $_bet->id) {
                                $bet->home_score = $_bet->home_score;
                                $bet->away_score = $_bet->away_score;
                                $bet->save();
                            }
                        }
                    }
                }
                
                unset($user_card->bets);
                $user_card->bets;

                $response['status'] = 'success';
                $response['user_card'] = $user_card;

                return response()->json($response, 200);

            } else {
                $response['status'] = 'error';
                $response['error'] = 'Número de aposta não coincide com o banco de dados.';

                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }

    }

    public function validateCard(Request $request) {
        $response = ['status' => null];

        $card = UserCard::find($request->id);
        if($card) {
            $card->validated = true;
            $response['validated'] = $card->save();
            $response['card'] = $card;
            $response['status'] = 'success';

            return response()->json($response, 200);

        } else{
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }
    }

    public function generateReceipt($id) {
        $user_card = UserCard::where('id', $id)
            ->where('validated', true)
        ->first();

        if($user_card) {

            foreach($user_card->bets as $bet) {
                if($bet->match_src === 'API_FOOTBALL') {
                    $bet->match = APIFootballHelper::requestMatch($bet->match_id);

                } else if ($bet->match_src === 'Gazeta_Scrapper') {
                    $bet->match = GazetaBrasileiraoScrapHelper::match($bet->match_round, $bet->match_id);
                    
                } else if($bet->match_src === 'Custom') {
                    $bet->match = Game::find($bet->match_id);
                }   
            }

            $data['date'] = date('d/m/Y H:i');
            $data['user_card'] = $user_card;

            $bets = json_encode($user_card->bets);

            $receipt = Receipt::where('user_card_id', $user_card->id)
                ->where('code', $user_card->code)
            ->first();
            if($receipt) {
                $receipt->bets = $bets;

            } else {
                $receipt = Receipt::create([
                    'user_card_id' => $user_card->id,
                    'code' => $user_card->code,
                    'bets' => $bets,
                    'name' => $user_card->name,
                    'phone' => $user_card->phone,
                ]);
            }

            $data['signature'] = URL::temporarySignedRoute('receipt.validate', now()->addMonths(4) ,[
                'id' => $receipt->id,
            ]);


            ini_set('max_execution_time', 300);

            $pdf = PDF::loadView('CardReceipt', $data);
            
            return $pdf->download("comprovante-$user_card->code.pdf");

        } else {
            return response()->json([
                'status' => 'error',
                'error' => 'Cartela inexistente.'
            ]);
        }

        
    }

    public function validateReceipt($id, Request $request) {
        if($request->hasValidSignature()) {
            $receipt = Receipt::find($id);

            if($receipt) {
                $receipt->bets = json_decode($receipt->bets);

                $user_card = UserCard::where('id', $receipt->user_card_id)
                    ->where('validated', true)
                ->first();
                if($user_card) {

                    foreach($receipt->bets as $bet) {
                        if($bet->match_src === 'API_FOOTBALL') {
                            $bet->match = APIFootballHelper::requestMatch($bet->match_id);
        
                        } else if ($bet->match_src === 'Gazeta_Scrapper') {
                            $bet->match = GazetaBrasileiraoScrapHelper::match($bet->match_round, $bet->match_id);
                            
                        } else if($bet->match_src === 'Custom') {
                            $bet->match = Game::find($bet->match_id);
                        }   
                    }

                    $user_card->bets = $receipt->bets;

                    $data['date'] = date('d/m/Y H:i', strtotime($receipt->created_at));
                    $data['user_card'] = $user_card;

                    $data['signature'] = URL::temporarySignedRoute('receipt.validate', now()->addMonths(4) ,[
                        'id' => $receipt->id,
                    ]);
                    
                    ini_set('max_execution_time', 300);

                    $pdf = PDF::loadView('CardReceipt', $data);
                    
                    return $pdf->download('comprovante.pdf');
                }
            }

        } else {
            return redirect()->route('401');
        }
    }

    public function deleteUserCard($id) {
        $response = ['status' => null];

        $card = UserCard::find($id);
        if($card) {
            $response['deleted'] = $card->delete();
            if($response['deleted']){
                $response['status'] = 'success';

                return response()->json($response, 200);

            }  else {
                $response['status'] = 'error';
                $response['error'] = 'Erro interno ao tentar completar ação. Por tente novamente.';

                return response()->json($response, 422);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);

        }
    }

    public function cardRanking($id, Request $request) {
        $response = ['status' => null];

        $page = $request->page ? intval($request->page) : 1;

        $card = Card::find($id);
        if($card) {
            $user_cards = [];
            foreach($card->userCards as $user_card) {
                if($user_card->validated == 1) {
                    $user_card->points = 0;
                    $user_card->card;

                    foreach($user_card->bets as $bet) {
                        if($bet->match_src === 'API_FOOTBALL') {
                            $match = APIFootballHelper::requestMatch($bet->match_id);
        
                        } else if ($bet->match_src === 'Gazeta_Scrapper') {
                            $match = GazetaBrasileiraoScrapHelper::match($bet->match_round, $bet->match_id);
                            
                        } else if($bet->match_src === 'Custom') {
                            $match = Game::find($bet->match_id);
                        }   
                        
                        if(is_array($match)) {
                            return response()->json(['match' => $match->id]);
                        }
                        if($match->finished){
                            if($card->type === 'common') {
                                if($match->home_score > $match->away_score){
                                    if($bet->bet === 'victory') {
                                        $bet->point = true;
                                        $user_card->points++;
                                    } else {
                                        $bet->point = false;
                                    }
                                }
                                if($match->home_score === $match->away_score){
                                    if($bet->bet === 'draw') {
                                        $bet->point = true;
                                        $user_card->points++;
                                    } else {
                                        $bet->point = false;
                                    }
                                }
                                if($match->home_score < $match->away_score){
                                    if($bet->bet === 'loss') {
                                        $bet->point = true;
                                        $user_card->points++;
                                    } else {
                                        $bet->point = false;
                                    }
                                }
                            }

                            if($card->type === 'detailed') {
                                if(
                                    $bet->home_score === $match->home_score
                                    && $bet->away_score === $match->away_score
                                ) {
                                    $bet->point = true;
                                    $user_card->points++;

                                } else {
                                    $bet->point = false;
                                }
                            }
                        } 

                        $bet->match = $match;
                    } 

                    $user_cards[] = $user_card;
                }
                    
            }

            foreach($user_cards as $key => $user_card) {
                foreach($user_cards as $_key => $_user_card) {
                    if($user_card->points > $_user_card->points && $key > $_key) {
                        array_splice($user_cards, $_key, 0, array_splice($user_cards, $key, 1));
                    }
                }
            }


            $award = (($card->price * count($user_cards)) / 100) * (100 - $card->host_percentage);

            $winners = [];
            if(count($user_cards) > 0 && $user_cards[0]->points > 0) {
                $winners[] = $user_cards[0];

                $first_winner_points = $user_cards[0]->points;

                for($i=1; $i<count($user_cards); $i++) {
                    if($user_cards[$i]->points === $first_winner_points) {
                        $winners[] = $user_cards[$i];

                    } else {
                        break;
                    }
                }
                
                foreach($winners as $winner) {
                    $winner->award = $award / count($winners);

                    if($winner->points === count($winner->bets)) {
                        $winner->award += $card->bonus;
                    }
                }
            }
            

            $pages_number = ceil(count($user_cards) / 30);

            $offset = $page === 1 ? (($page -1) * 30) : (($page -1 ) * 30) -1;
            $user_cards = array_slice($user_cards, $offset, 30);
        
            $response['pages']['number'] = $pages_number;
            for($l=1; $l==$pages_number; $l++) {
                $link = ['url' => null, 'active' => null];
                $link['url'] = url("/admin/card-ranking/$card->id?page=$l");
                $link['active'] = $l == $page ? true : false;
                $response['pages']['links'][] = $link;
            }
            $response['pages']['previous'] = $page == 1 || $pages_number === 1 ? null : url("/admin/card-ranking/$card->id?page=".$page-1);
            $response['pages']['next'] = $page == $pages_number || $pages_number === 1 ? null : url("/admin/card-ranking/$card->id?page=".$page+1);

            $response['status'] = 'success';

            $response['ranked_user_cards'] = $user_cards;
            $response['winners'] = $winners;

            return response()->json($response, 200);
           

        } else {
            $response['status'] = 'error';
            $response['error'] = 'Cartela inexistente.';

            return response()->json($response, 422);
        }
    }

    public function publicateRanking($id) {
        $response = ['status' => null];

        $card = Card::find($id);
        if($card) {
            $success = PublicRanking::create([
                'card_id' => $card->id
            ]);
            if($success) {
                $response['status'] = 'success';
                return response()->json($response);
            }
        } else {
            $response['status'] = 'error';
            $response['error'] = 'cartela inexistente.';

            return response()->json($response, 422);
        }
    }

    public function rankingPDF($id) {
        $card = Card::find($id); 
        if($card) {
            $user_cards = [];
            foreach($card->userCards as $user_card) {
                if($user_card->validated == 1) {
                    $user_card->points = 0;
                    $user_card->card;

                    foreach($user_card->bets as $bet) {
                        if($bet->match_src === 'API_FOOTBALL') {
                            $match = APIFootballHelper::requestMatch($bet->match_id);
        
                        } else if ($bet->match_src === 'Gazeta_Scrapper') {
                            $match = GazetaBrasileiraoScrapHelper::match($bet->match_round, $bet->match_id);
                            
                        } else if($bet->match_src === 'Custom') {
                            $match = Game::find($bet->match_id);
                        }   
                        
                        if(is_array($match)) {
                            return response()->json(['match' => $match->id]);
                        }
                        if($match->finished){
                            if($card->type === 'common') {
                                if($match->home_score > $match->away_score){
                                    if($bet->bet === 'victory') {
                                        $bet->point = true;
                                        $user_card->points++;
                                    } else {
                                        $bet->point = false;
                                    }
                                }
                                if($match->home_score === $match->away_score){
                                    if($bet->bet === 'draw') {
                                        $bet->point = true;
                                        $user_card->points++;
                                    } else {
                                        $bet->point = false;
                                    }
                                }
                                if($match->home_score < $match->away_score){
                                    if($bet->bet === 'loss') {
                                        $bet->point = true;
                                        $user_card->points++;
                                    } else {
                                        $bet->point = false;
                                    }
                                }
                            }

                            if($card->type === 'detailed') {
                                if(
                                    $bet->home_score === $match->home_score
                                    && $bet->away_score === $match->away_score
                                ) {
                                    $bet->point = true;
                                    $user_card->points++;

                                } else {
                                    $bet->point = false;
                                }
                            }
                        } 

                        $bet->match = $match;
                    } 

                    $user_cards[] = $user_card;
                }
                    
            }

            foreach($user_cards as $key => $user_card) {
                foreach($user_cards as $_key => $_user_card) {
                    if($user_card->points > $_user_card->points && $key > $_key) {
                        array_splice($user_cards, $_key, 0, array_splice($user_cards, $key, 1));
                    }
                }
            }


            $award = (($card->price * count($user_cards)) / 100) * (100 - $card->host_percentage);

            $winners = [];
            if(count($user_cards) > 0 && $user_cards[0]->points > 0) {
                $winners[] = $user_cards[0];

                $first_winner_points = $user_cards[0]->points;

                for($i=1; $i<count($user_cards); $i++) {
                    if($user_cards[$i]->points === $first_winner_points) {
                        $winners[] = $user_cards[$i];

                    } else {
                        break;
                    }
                }
                
                foreach($winners as $winner) {
                    $winner->award = $award / count($winners);
                }
            }

            $data['card'] = $card;
            $data['ranked_user_cards'] = $user_cards;
            $data['winners'] = $winners;

            $data['p_color'] = AdminOpt::select('value')->where('name', 'p_color')->first();
            $data['s_color'] = AdminOpt::select('value')->where('name', 's_color')->first();

            ini_set('max_execution_time', 300);

            $pdf = PDF::loadView('Ranking', $data);
            
            return $pdf->download("ranking-$card->name.pdf");

        } else {
            return response()->json([
                'status' => 'error',
                'error' => 'Cartela inexistente.'
            ]);
        }
    }

    public function attendantAttendancesPDF($id, Request $request) {
        $attendant = Attendant::find($id);
        
        if($attendant) {
            $last = $request->last ? date('Y-m-d H:i:s', strtotime("-$request-last days")) : null;

            if($request->filter && $last) {
                $data['attendances'] = Attendance::where('attendant_id', $id)
                    ->where('type', $request->filter)
                    ->where('created_at', '>', $last)
                ->get();

            } else if($request->filter) {
                $data['attendances'] = Attendance::where('attendant_id', $id)
                    ->where('type', $request->filter)
                ->get();

            } else if($last) {
                $data['attendances'] = Attendance::where('attendant_id', $id)
                    ->where('created_at', '>', $last)
                ->get();

            } else {
                $data['attendances'] = Attendance::where('attendant_id', $id)->get();
            }

            foreach($data['attendances'] as $attendance) {
                $attendance->date = date('d/m/Y H:i', strtotime($attendance->created_at));    
            }

            $data['date'] = $last ?? date('Y-m-d H:i:s', strtotime('-120 days'));
            
            $p_color = AdminOpt::select('value')->where('name', 'p_color')->first();
            $s_color = AdminOpt::select('value')->where('name', 's_color')->first();
            $data['p_color'] = $p_color->value;
            $data['s_color'] = $s_color->value;

            $pdf = PDF::loadView('attendancesReceipt', $data);
         
            return $pdf->download("Atendimentos-$attendant->name");
        }
    }

    public function attendantSalesPDF($id, Request $request) {
        $attendant = Attendant::find($id);

        if($attendant) {
            $filter = $request->filter ? date('Y-m-d H:i:s', strtotime("-$request->filter days")) : null;
            
            if($filter) {
                $sales = Sale::where('attendant_id', $attendant->id)->where('created_at', '>', $filter)->get();
            } else {
                $sales = Sale::where('attendant_id', $attendant->id)->get();
            }

            $data['date'] = $filter ?? date('Y-m-d H:i:s', strtotime('-120 days'));
            $data['sales'] = $sales;
            
            $p_color = AdminOpt::select('value')->where('name', 'p_color')->first();
            $s_color = AdminOpt::select('value')->where('name', 's_color')->first();
            $data['p_color'] = $p_color->value;
            $data['s_color'] = $s_color->value;

            $pdf = PDF::loadView('salesReceipt', $data);

            return $pdf->download("Vendas-$attendant->name");
        }
    }

    public function searchUserCard($q) {
        $response = ['status' => null];

        $user_cards = UserCard::where('code', $q)->get();
        if(count($user_cards) === 0) {
            $user_cards = UserCard::where('name', 'like', "%$q%")->get();
        }
        if($user_cards) {
            
            foreach($user_cards as $user_card){
                $user_card->card;
                foreach($user_card->bets as $key => $bet) {
                    if($bet->match_src === 'API_FOOTBALL') {
                        $bet->match = APIFootballHelper::requestMatch($bet->match_id);
    
                    } else if ($bet->match_src === 'Gazeta_Scrapper') {
                        $bet->match = GazetaBrasileiraoScrapHelper::match($bet->match_round, $bet->match_id);
                        
                    } else if($bet->match_src === 'Custom') {
                        $bet->match = Game::find($bet->match_id);
                    }   
                    
                }
            }
            $response['status'] = 'success';
            $response['user_cards'] = $user_cards;

            return response()->json($response, 200);
            
            
        } else {
            $response['status'] = 'error';
            $response['error'] = 'Não há nenhum registro que coincida com a busca.';

            return response()->json($response, 200);
        }
    }
}

