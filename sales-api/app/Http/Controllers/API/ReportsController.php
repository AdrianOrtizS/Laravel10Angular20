<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ReportsService;

class ReportsController extends Controller
{
    protected $reports;


	public function __construct(ReportsService $reports)
	{
		$this->reports = $reports;
	}


	public function salesMonthly()
	{
		return response()->json($this->reports->salesMonthly());
	}


	public function sales10Daily()
	{
		return response()->json($this->reports->sales10Daily());
	}


	public function top10Products()
	{
		return response()->json($this->reports->top10Products());
	}


	public function purchasesMonthly()
	{
		return response()->json($this->reports->purchasesMonthly());
	}


	public function lowStock()
	{
		return response()->json($this->reports->lowStock());
	}

}
