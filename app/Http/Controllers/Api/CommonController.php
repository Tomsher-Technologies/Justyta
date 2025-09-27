<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Emirate;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function getEmirates(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $litigation_type   = $request->litigation_type ?? NULL;
        $litigation_place   = $request->litigation_place ?? NULL;
        $service            = $request->service ?? NULL;

        $emirates   = Emirate::whereHas('emirate_litigations', function ($q) use ($service, $litigation_type) {
                        $q->where('slug', $service)->where('status', 1);
                        if (in_array($service, ['court-case-submission', 'criminal-complaint', 'expert-report', 'memo-writing', 'online-live-consultancy', 'request-submission'])) {
                            if ($litigation_type === 'federal') {
                                $q->where('is_federal', 1);
                            } elseif ($litigation_type === 'local') {
                                $q->where('is_local', 1);
                            }
                        }
                    })->get();

        $response['emirates'] = $emirates->map(function ($emirate) use($lang) {
                return [
                    'id'    => $emirate->id,
                    'value' => $emirate->getTranslation('name',$lang),
                ];
        });

        if(in_array($service, ['court-case-submission', 'memo-writing','online-live-consultancy','annual-retainer-agreement'])) {
            $litigation_place = 'court';
        }elseif($service == 'criminal-complaint') {
            $litigation_place = 'public_prosecution';
        }

        $response['caseTypes'] = [];
        if($litigation_place){
            $response['caseTypes'] = getCaseTypes($litigation_type, $litigation_place, $lang);
        }
        
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $response,
        ], 200);
    }

    public function getCaseTypes(Request $request){
        $lang       = $request->header('lang') ?? env('APP_LOCALE', 'en');

        $litigation_type   = $request->litigation_type ?? NULL;
        $litigation_place   = $request->litigation_place ?? NULL;
        $service            = $request->service ?? NULL;

        if(in_array($service, ['court-case-submission', 'memo-writing'])) {
            $litigation_place = 'court';
        }else{
            $litigation_place = 'public_prosecution';
        }

        $caseTypes = getCaseTypes($litigation_type, $litigation_place, $lang);

        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => $caseTypes,
        ], 200);

    }
}
