<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VeterinaryRecord;
use Illuminate\Http\Request;

class VeterinaryRecordController extends Controller
{
    public function index() {}

    public function create() {}

    public function store(Request $request) {}

    public function show(VeterinaryRecord $veterinaryRecord) {}

    public function edit(VeterinaryRecord $veterinaryRecord) {}

    public function update(Request $request, VeterinaryRecord $veterinaryRecord) {}

    public function destroy(VeterinaryRecord $veterinaryRecord) {}
}
