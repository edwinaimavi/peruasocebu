<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index() {}

    public function create() {}

    public function store(Request $request) {}

    public function show(ContactMessage $contactMessage) {}

    public function edit(ContactMessage $contactMessage) {}

    public function update(Request $request, ContactMessage $contactMessage) {}

    public function destroy(ContactMessage $contactMessage) {}
}
