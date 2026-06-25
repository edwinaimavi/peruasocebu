<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CattlePhoto;
use Illuminate\Http\Request;

class CattlePhotoController extends Controller
{
    public function index() {}

    public function create() {}

    public function store(Request $request) {}

    public function show(CattlePhoto $cattlePhoto) {}

    public function edit(CattlePhoto $cattlePhoto) {}

    public function update(Request $request, CattlePhoto $cattlePhoto) {}

    public function destroy(CattlePhoto $cattlePhoto) {}
}
