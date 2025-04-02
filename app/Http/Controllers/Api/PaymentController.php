<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::all();
        return response()->json($payments);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lease_id' => 'required|exists:leases,id',
            'amount' => 'required|numeric',
            'type' => 'required|in:rent,deposit,maintenance,other',
            'due_date' => 'required|date',
            'payment_method' => 'required|string',
            'transaction_id' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $payment = Payment::create($request->all());
        return response()->json($payment, 201);
    }

    public function show(Payment $payment)
    {
        return response()->json($payment);
    }

    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'lease_id' => 'sometimes|exists:leases,id',
            'amount' => 'sometimes|numeric',
            'type' => 'sometimes|in:rent,deposit,maintenance,other',
            'due_date' => 'sometimes|date',
            'payment_method' => 'sometimes|string',
            'transaction_id' => 'sometimes|string',
            'notes' => 'nullable|string',
            'status' => 'sometimes|in:pending,completed,failed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $payment->update($request->all());
        return response()->json($payment);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json(null, 204);
    }
} 