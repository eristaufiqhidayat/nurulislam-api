<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\TabunganQurbanService;
use Illuminate\Http\Request;

class TabunganQurbanController extends Controller
{
    protected $service;

    public function __construct(TabunganQurbanService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return response()->json(
            $this->service->list($request->get('per_page', 10))
        );
    }

    public function show($id)
    {
        return response()->json(
            $this->service->detail($id)
        );
    }

    public function store(Request $request)
    {
        return response()->json(
            $this->service->create($request->all()),
            201
        );
    }

    public function update(Request $request, $id)
    {
        return response()->json(
            $this->service->update($id, $request->all())
        );
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Deleted']);
    }
    public function destroyDetail($id)
    {
        $this->service->deleteDetail($id);
        return response()->json(['message' => 'Deleted']);
    }
    public function addSetoran(Request $request)
    {
        return response()->json(
            $this->service->addSetoran($request->all()),
            201
        );
    }
    public function detail($id)
    {
        return response()->json(
            $this->service->detail($id)
        );
    }
}
