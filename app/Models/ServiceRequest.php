<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $table = 'service_requests';

    protected $fillable = [
        'user_id',
        'service_id',
        'service_slug',
        'reference_code',
        'status',
        'payment_status',
        'payment_reference',
        'service_fee',
        'govt_fee',
        'tax',
        'amount',
        'paid_at',
        'payment_response',
        'submitted_at',
        'source',
        'request_success',
        'completed_files',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'completed_files' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public static function generateReferenceCode($service)
    {
        $prefix = strtoupper(Str::slug($service->short_code, '-'));

        $lastCode = self::where('service_id', $service->id)
            ->whereNotNull('reference_code')
            ->orderBy('id', 'desc')
            ->value('reference_code');

        $nextNumber = 1;
        if ($lastCode) {
            preg_match('/(\d+)$/', $lastCode, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function requestSubmission()
    {
        return $this->hasOne(RequestRequestSubmission::class, 'service_request_id');
    }

    public function legalTranslation()
    {
        return $this->hasOne(RequestLegalTranslation::class, 'service_request_id');
    }
    public function annualAgreement()
    {
        return $this->hasOne(RequestAnnualAgreement::class, 'service_request_id');
    }

    public function immigrationRequest()
    {
        return $this->hasOne(RequestImmigration::class, 'service_request_id');
    }

    public function courtCase()
    {
        return $this->hasOne(RequestCourtCase::class, 'service_request_id');
    }

    public function criminalComplaint()
    {
        return $this->hasOne(RequestCriminalComplaint::class, 'service_request_id');
    }

    public function powerOfAttorney()
    {
        return $this->hasOne(RequestPowerOfAttorney::class, 'service_request_id');
    }

    public function lastWill()
    {
        return $this->hasOne(RequestLastWill::class, 'service_request_id');
    }

    public function memoWriting()
    {
        return $this->hasOne(RequestMemoWriting::class, 'service_request_id');
    }

    public function expertReport()
    {
        return $this->hasOne(RequestExpertReport::class, 'service_request_id');
    }

    public function contractDrafting()
    {
        return $this->hasOne(RequestContractDrafting::class, 'service_request_id');
    }

    public function companySetup()
    {
        return $this->hasOne(RequestCompanySetup::class, 'service_request_id');
    }

    public function escrowAccount()
    {
        return $this->hasOne(RequestEscrowAccount::class, 'service_request_id');
    }

    public function debtCollection()
    {
        return $this->hasOne(RequestDebtCollection::class, 'service_request_id');
    }

    public function installments()
    {
        return $this->hasMany(AnnualAgreementInstallment::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ServiceRequestTimeline::class)->orderBy('id', 'desc');
    }

    public function getCurrentStatusAttribute()
    {
        return $this->statusHistories()->value('status');
    }

    public function getLatestRejectionDetails()
    {
        return $this->statusHistories()
            ->where('status', 'rejected')
            ->latest('id')
            ->first();
    }
}
