<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Diagnosis, MedicalRecord, Patient, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalRecord::with(['patient', 'createdBy']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', fn($q) =>
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
            );
        }

        if ($request->filled('from')) {
            $query->whereDate('tanggal_kunjungan', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('tanggal_kunjungan', '<=', $request->to);
        }

        $records = $query->latest()->paginate(15)->withQueryString();

        return view('admin.medical-records.index', compact('records'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('nama')->get(['id', 'no_rm', 'nama']);
        $dokters  = User::role(['super_admin', 'admin'])->orderBy('name')->get(['id', 'name']);
        $selectedPatient = $request->patient_id ? Patient::find($request->patient_id) : null;
        $selectedDiagnosis = $request->diagnosis_id ? Diagnosis::find($request->diagnosis_id) : null;
        $diagnoses = Diagnosis::orderBy('name')->get();

        return view('admin.medical-records.create', compact('patients', 'dokters', 'selectedPatient', 'selectedDiagnosis', 'diagnoses'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // 1. VALIDASI 
            $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'tanggal_kunjungan' => 'required|date',
                'keluhan' => 'nullable|string|max:255',
            ]);

            // 2. CREATE MEDICAL RECORD
            $record = MedicalRecord::create([
                'patient_id' => $request->patient_id,
                'user_id' => Auth::id(),
                'visit_date' => $request->tanggal_kunjungan,
                'complaint' => $request->keluhan,
            ]);

            // 3. SPLIT DATA BERDASARKAN PREFIX
            $oldData = $this->extractData($request->all(), 'old_');
            $refData = $this->extractData($request->all(), 'ref_');
            $rxData  = $this->extractData($request->all(), 'rx_');
            // dd(compact('oldData', 'refData', 'rxData'));

            // 4. SIMPAN JIKA ADA ISI

            if ($this->hasValue($oldData)) {
                $record->oldGlasses()->create($oldData);
            }

            if ($this->hasValue($refData)) {
                $record->refraction()->create($refData);
            }

            if ($this->hasValue($rxData)) {
                $record->prescription()->create($rxData);
            }

            DB::commit();

            return redirect()->route('medical-records.index')
                ->with('success', 'Rekam medis berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['patient', 'createdBy', 'transaction.items.product']);

        return view('admin.medical-records.show', compact('medicalRecord'));
    }

    public function detail(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['patient', 'createdBy', 'transaction.items.product']);

        return view('admin.medical-records.detail', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord, Request $request)
    {
        $dokters = User::role(['super_admin', 'admin'])
            ->orderBy('name')
            ->get(['id', 'name']);

        // Selected patient
        $selectedPatient = $request->patient_id
            ? Patient::select('id', 'nama', 'no_rm')->find($request->patient_id)
            : $medicalRecord->patient;

        // Selected diagnosis
        $selectedDiagnosis = $request->diagnosis_id
            ? Diagnosis::find($request->diagnosis_id)
            : $medicalRecord->diagnosis ?? null;

        $diagnoses = Diagnosis::orderBy('name')->get();

        // dd(compact('medicalRecord', 'dokters', 'selectedPatient', 'selectedDiagnosis', 'diagnoses'));
        return view('admin.medical-records.edit', compact(
            'medicalRecord',
            'dokters',
            'selectedPatient',
            'selectedDiagnosis',
            'diagnoses'
        ));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        // dd($request->all());
        DB::beginTransaction();

        try {
            // 1. VALIDASI BASIC
            $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'tanggal_kunjungan' => 'required|date',
                'keluhan' => 'nullable|string|max:255',
            ]);

            // 2. UPDATE MEDICAL RECORD
            $medicalRecord->update([
                'patient_id' => $request->patient_id,
                'visit_date' => $request->tanggal_kunjungan,
                'complaint' => $request->keluhan,
            ]);

            // 3. SPLIT DATA
            $oldData = $this->extractData($request->all(), 'old_');
            $refData = $this->extractData($request->all(), 'ref_');
            $rxData  = $this->extractData($request->all(), 'rx_');

            // 4. UPDATE / CREATE RELASI

            // OLD GLASSES
            if ($this->hasValue($oldData)) {
                $medicalRecord->oldGlasses()->updateOrCreate(
                    ['medical_record_id' => $medicalRecord->id],
                    $oldData
                );
            }

            // REFRACTION
            if ($this->hasValue($refData)) {
                $medicalRecord->refraction()->updateOrCreate(
                    ['medical_record_id' => $medicalRecord->id],
                    $refData
                );
            }

            // PRESCRIPTION
            if ($this->hasValue($rxData)) {
                $medicalRecord->prescription()->updateOrCreate(
                    ['medical_record_id' => $medicalRecord->id],
                    $rxData
                );
            }

            DB::commit();

            return redirect()->route('medical-records.index')
                ->with('success', 'Rekam medis berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();
        return redirect()->route('medical-records.index')
            ->with('success', 'Rekam medis berhasil dihapus.');
    }

    // --- HELPER METHODS ---
    private function extractData($data, $prefix)
    {
        return collect($data)
            ->filter(fn($v, $k) => str_starts_with($k, $prefix))
            ->mapWithKeys(function ($value, $key) use ($prefix) {
                return [str_replace($prefix, '', $key) => $value];
            })
            ->toArray();
    }

    private function hasValue($data)
    {
        return collect($data)->filter(fn($v) => !is_null($v) && $v !== '')->isNotEmpty();
    }
}
