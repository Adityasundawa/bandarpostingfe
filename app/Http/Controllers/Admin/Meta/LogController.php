<?php

namespace App\Http\Controllers\Admin\Meta;

use App\Http\Controllers\Controller;
use App\Services\MetaApiService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function __construct(protected MetaApiService $api) {}

    public function index(Request $request)
    {
        $filters = array_filter([
            'status_code' => $request->input('status_code'),
            'token_id'    => $request->input('token_id'),
            'endpoint'    => $request->input('endpoint'),
            'date_from'   => $request->input('date_from'),
            'date_to'     => $request->input('date_to'),
            'limit'       => $request->input('limit', 100),
            'offset'      => $request->input('offset', 0),
        ], fn($v) => $v !== null && $v !== '');

        $result = $this->api->getLogs($filters);

        $logs    = $result['success'] ? ($result['data']['data'] ?? []) : [];
        $total   = $result['success'] ? ($result['data']['total'] ?? 0) : 0;
        $error   = $result['success'] ? null : ($result['data']['message'] ?? 'Gagal mengambil log.');

        return view('admin.meta.logs.index', compact('logs', 'total', 'filters', 'error'));
    }
}
