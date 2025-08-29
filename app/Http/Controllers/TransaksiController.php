<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Transaksi;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaksis = Transaksi::orderBy('tanggal', 'desc')->get();
        $brands = Brand::all(['id', 'nama_brand', 'pemilik', 'logo_path'])
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'nama_brand' => $brand->nama_brand,
                    'pemilik' => $brand->pemilik,
                    'logo_path' => $brand->logo_path,
                    'namaBrand' => $brand->nama_brand,
                    'namaCV' => $brand->pemilik,
                    'logoPath' => $brand->logo_path
                ];
            });
        
        return Inertia::render('transaksi/TransaksiList', [
            'transaksis' => $transaksis,
            'brands' => $brands,
            'canEdit' => auth()->user()->canEdit(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::all(['id', 'nama_brand', 'pemilik', 'logo_path'])
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'nama_brand' => $brand->nama_brand,
                    'pemilik' => $brand->pemilik,
                    'logo_path' => $brand->logo_path,
                    'namaBrand' => $brand->nama_brand, // For frontend compatibility
                    'namaCV' => $brand->pemilik, // For frontend compatibility
                    'logoPath' => $brand->logo_path // For frontend compatibility
                ];
            });
        $transaksis = Transaksi::orderBy('tanggal', 'desc')->get();
        
        return Inertia::render('transaksi/TransaksiInput', [
            'brands' => $brands,
            'transaksis' => $transaksis,
            'canEdit' => auth()->user()->canEdit(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'brand' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
        ]);

        Transaksi::create([
            'tanggal' => $validated['tanggal'],
            'brand' => $validated['brand'],
            'nominal' => $validated['nominal'],
        ]);

        return redirect()->route('transaksi.input')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        return Inertia::render('transaksi/TransaksiShow', [
            'transaksi' => $transaksi
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $brands = Brand::all(['id', 'nama_brand', 'pemilik']);
        
        return Inertia::render('transaksi/TransaksiEdit', [
            'transaksi' => $transaksi,
            'brands' => $brands
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'brand' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
        ]);

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'tanggal' => $validated['tanggal'],
            'brand' => $validated['brand'],
            'nominal' => $validated['nominal'],
        ]);

        return redirect()->route('transaksi.input')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->route('transaksi.input')->with('success', 'Transaksi berhasil dihapus.');
    }
}
