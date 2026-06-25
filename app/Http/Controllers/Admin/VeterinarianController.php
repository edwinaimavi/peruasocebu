<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Veterinarian;
use Illuminate\Http\Request;

class VeterinarianController extends Controller
{
    public function index() {}

    public function create() {}

    public function store(Request $request) {}

    public function show(Veterinarian $veterinarian) {}

    public function edit(Veterinarian $veterinarian) {}

    public function update(Request $request, Veterinarian $veterinarian) {}

    public function destroy(Veterinarian $veterinarian) {}
}
