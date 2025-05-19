<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveVoucher;
use App\Models\LiveStream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiveVoucherController extends Controller
{
    public function index()
    {
        $vouchers = LiveVoucher::with('liveStream')
            ->latest()
            ->paginate(10);
            
        return view('admin.streaming.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $liveStreams = LiveStream::where('status', 'scheduled')
            ->orWhere('status', 'active')
            ->get();
            
        return view('admin.streaming.vouchers.create', compact('liveStreams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:live_vouchers,code',
            'discount_type' => 'required|in:percentage,amount',
            'discount_value' => 'required|numeric|min:0',
            'live_stream_id' => 'required|exists:live_streams,id',
            'description' => 'nullable|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        try {
            DB::beginTransaction();

            LiveVoucher::create([
                'code' => strtoupper($request->code),
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'live_stream_id' => $request->live_stream_id,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'active' => true
            ]);

            DB::commit();

            return redirect()
                ->route('admin.streaming.vouchers.index')
                ->with('success', 'Voucher berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal membuat voucher: ' . $e->getMessage());
        }
    }

    public function edit(LiveVoucher $voucher)
    {
        $liveStreams = LiveStream::where('status', 'scheduled')
            ->orWhere('status', 'active')
            ->get();
            
        return view('admin.streaming.vouchers.edit', compact('voucher', 'liveStreams'));
    }

    public function update(Request $request, LiveVoucher $voucher)
    {
        $request->validate([
            'code' => 'required|string|unique:live_vouchers,code,' . $voucher->id,
            'discount_type' => 'required|in:percentage,amount',
            'discount_value' => 'required|numeric|min:0',
            'live_stream_id' => 'required|exists:live_streams,id',
            'description' => 'nullable|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        try {
            DB::beginTransaction();

            $voucher->update([
                'code' => strtoupper($request->code),
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'live_stream_id' => $request->live_stream_id,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.streaming.vouchers.index')
                ->with('success', 'Voucher berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui voucher: ' . $e->getMessage());
        }
    }

    public function toggleStatus(LiveVoucher $voucher)
    {
        $voucher->update(['active' => !$voucher->active]);

        return back()->with('success', 
            $voucher->active ? 'Voucher berhasil diaktifkan' : 'Voucher berhasil dinonaktifkan'
        );
    }

    public function destroy(LiveVoucher $voucher)
    {
        try {
            if ($voucher->liveOrders()->exists()) {
                throw new \Exception('Voucher tidak dapat dihapus karena sudah digunakan dalam pesanan');
            }

            $voucher->delete();

            return redirect()
                ->route('admin.streaming.vouchers.index')
                ->with('success', 'Voucher berhasil dihapus');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus voucher: ' . $e->getMessage());
        }
    }
}
