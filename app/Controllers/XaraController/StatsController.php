<?php

class StatsController extends BaseController
{

    public function getIndex()
    {
        return View::make('intelligence.trends');
    }

    public function getAPI()
    {
        $days = Input::get('days', 7);

        $range = \Carbon\Carbon::now()->subDays($days);

        $stats = DB::table('loanaccounts')
            ->where('date_disbursed', '>=', $range)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->remember(1440) // Cache the data for 24 hours
            ->get([
                DB::raw('Date(date_disbursed) as date'),
                DB::raw('COUNT(*) as value')
            ]);

        return $stats;
    }

    public function selectProduct(){
        $data=Input::all();
        $product=$data['trend_product'];
        $graph=$data['trend_graph'];
        
    }
}