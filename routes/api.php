<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketController;

Route::prefix('tickets')->group(function () {
    Route::get('/export', [TicketController::class, 'exportCsv']);
    Route::post('/', [TicketController::class, 'store']); 
    Route::get('/', [TicketController::class, 'index']);  
    Route::get('{id}', [TicketController::class, 'show']); 
    Route::patch('{id}', [TicketController::class, 'update']); 
    Route::post('{ticket}/classify', [TicketController::class, 'classify']); 
    Route::get('{id}/classification-status', [TicketController::class, 'checkClassificationStatus']); 
    Route::post('bulk-classify', [TicketController::class, 'bulkClassify']); 
});

Route::get('/stats', [TicketController::class, 'stats']);
Route::get('/queue-status', [TicketController::class, 'queueStatus']);
