<?php

namespace App\Http\Controllers;

class PagesController extends Controller
{
	function root() {
		return view('pages.root');
	}
}
