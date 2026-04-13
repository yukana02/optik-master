@extends('layouts.admin')

@section('title', 'Point of Sale — Wizard')

@push('styles')
    @include('admin.transactions.partials.pos-styles')
@endpush

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <form action="{{ route('transactions.store') }}" method="POST" id="pos-form">
                @csrf
                <input type="hidden" name="transaction_data" id="transaction_data" value="">
                <input type="hidden" name="id" id="trx_id">
                <input type="hidden" name="patient_id" id="patient_id">
                <input type="hidden" name="cart_data" id="cart_data" value="[]">

                {{-- Global action bar --}}
                @include('admin.transactions.partials.action-bar')

                {{-- Wizard Steps Indicator --}}
                @include('admin.transactions.partials.step-indicator')

                {{-- Panels --}}
                @include('admin.transactions.partials.step-1')
                @include('admin.transactions.partials.step-2')
                @include('admin.transactions.partials.step-3')
                @include('admin.transactions.partials.step-4')

            </form>
        </div>
    </div>

    @include('admin.transactions.partials.pos-modals')
    <iframe id="printFrame" src=""></iframe>
@endsection

@push('scripts')
    @include('admin.transactions.partials.pos-scripts')
@endpush