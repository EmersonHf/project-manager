<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Lease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['lease'])
            ->latest()
            ->paginate(10);

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $leases = Lease::where('status', 'active')->get();
        return view('payments.create', compact('leases'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:rent,deposit,maintenance,late_fee,other',
            'due_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:2048',
        ]);

        $validated['status'] = 'pending';

        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->store('payments', 'public');
                $attachments[] = $path;
            }
            $validated['attachments'] = $attachments;
        }

        $payment = Payment::create($validated);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['lease']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $leases = Lease::where('status', 'active')
            ->orWhere('id', $payment->lease_id)
            ->get();
        return view('payments.edit', compact('payment', 'leases'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'lease_id' => 'required|exists:leases,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:rent,deposit,maintenance,late_fee,other',
            'status' => 'required|in:pending,completed,failed,refunded',
            'due_date' => 'required|date',
            'paid_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:2048',
        ]);

        if ($request->hasFile('attachments')) {
            // Delete old attachments
            if ($payment->attachments) {
                foreach ($payment->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment);
                }
            }

            $attachments = [];
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->store('payments', 'public');
                $attachments[] = $path;
            }
            $validated['attachments'] = $attachments;
        }

        $payment->update($validated);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        // Delete attachments
        if ($payment->attachments) {
            foreach ($payment->attachments as $attachment) {
                Storage::disk('public')->delete($attachment);
            }
        }

        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}
