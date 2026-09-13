<?php

namespace App\Services;

use App\Enums\LeadStatus;
use App\Enums\NegotiationApprovalStatus;
use App\Models\Lead;
use App\Models\Negotiation;
use App\Models\PropertyUnit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NegotiationService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Submit a new negotiation & discount request.
     */
    public function createNegotiation(array $data, User $sales): Negotiation
    {
        return DB::transaction(function () use ($data, $sales) {
            $unit = PropertyUnit::findOrFail($data['property_unit_id']);
            $initialPrice = (float) $unit->selling_price;

            $customerOfferPrice = isset($data['customer_offer_price']) && (float) $data['customer_offer_price'] > 0
                ? (float) $data['customer_offer_price']
                : null;

            $discountAmount = isset($data['discount_amount']) && (float) $data['discount_amount'] > 0
                ? (float) $data['discount_amount']
                : 0;

            if ($customerOfferPrice !== null) {
                $finalPrice = $customerOfferPrice;
                $discountAmount = max(0, $initialPrice - $finalPrice);
            } else {
                $finalPrice = max(0, $initialPrice - $discountAmount);
                $customerOfferPrice = $finalPrice;
            }

            $discountPercentage = $initialPrice > 0
                ? round(($discountAmount / $initialPrice) * 100, 2)
                : 0;

            // If discount is 0 or within negligible threshold, can be auto-approved, otherwise requires manager approval
            $status = $discountAmount > 0
                ? NegotiationApprovalStatus::PENDING
                : NegotiationApprovalStatus::APPROVED;

            $negotiation = Negotiation::create([
                'lead_id' => $data['lead_id'],
                'property_unit_id' => $unit->id,
                'sales_id' => $sales->id,
                'initial_price' => $initialPrice,
                'customer_offer_price' => $customerOfferPrice,
                'final_price' => $finalPrice,
                'discount_amount' => $discountAmount,
                'discount_percentage' => $discountPercentage,
                'promo_description' => $data['promo_description'] ?? null,
                'approval_status' => $status,
                'approved_by_id' => $status === NegotiationApprovalStatus::APPROVED ? $sales->id : null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Update lead status to NEGOTIATION if not already won/lost
            $lead = Lead::find($data['lead_id']);
            if ($lead && in_array($lead->status, [LeadStatus::NEW, LeadStatus::CONTACTED, LeadStatus::QUALIFIED, LeadStatus::SITE_VISIT])) {
                $lead->update(['status' => LeadStatus::NEGOTIATION]);
            }

            $this->auditLogService->log(
                action: 'create_negotiation',
                entity: 'Negotiation',
                before: null,
                after: [
                    'initial_price' => $initialPrice,
                    'final_price' => $finalPrice,
                    'discount_amount' => $discountAmount,
                    'discount_percentage' => $discountPercentage,
                    'status' => $status->value,
                ],
                entityId: $negotiation->id
            );

            return $negotiation;
        });
    }

    /**
     * Approve a negotiation and requested discount.
     */
    public function approveNegotiation(Negotiation $negotiation, User $approver, ?string $notes = null): Negotiation
    {
        if (! $approver->isManagerial()) {
            throw ValidationException::withMessages([
                'authorization' => 'Hanya Manajer Penjualan, Team Leader, atau Direksi yang berwenang menyetujui diskon.',
            ]);
        }

        if ($negotiation->approval_status === NegotiationApprovalStatus::APPROVED) {
            throw ValidationException::withMessages([
                'status' => 'Pengajuan negosiasi ini sudah disetujui sebelumnya.',
            ]);
        }

        return DB::transaction(function () use ($negotiation, $approver, $notes) {
            $beforeStatus = $negotiation->approval_status->value;

            $updatedNotes = $negotiation->notes;
            if ($notes) {
                $updatedNotes = ($updatedNotes ? $updatedNotes . "\n\n" : '') . "[Catatan Approval " . now()->format('d/m/Y H:i') . "]: " . $notes;
            }

            $negotiation->update([
                'approval_status' => NegotiationApprovalStatus::APPROVED,
                'approved_by_id' => $approver->id,
                'notes' => $updatedNotes,
            ]);

            $this->auditLogService->log(
                action: 'approve_negotiation',
                entity: 'Negotiation',
                before: ['status' => $beforeStatus],
                after: ['status' => NegotiationApprovalStatus::APPROVED->value, 'approver_id' => $approver->id],
                entityId: $negotiation->id
            );

            return $negotiation;
        });
    }

    /**
     * Reject a negotiation / discount request.
     */
    public function rejectNegotiation(Negotiation $negotiation, User $approver, ?string $reason = null): Negotiation
    {
        if (! $approver->isManagerial()) {
            throw ValidationException::withMessages([
                'authorization' => 'Hanya Manajer Penjualan, Team Leader, atau Direksi yang berwenang menolak pengajuan diskon.',
            ]);
        }

        return DB::transaction(function () use ($negotiation, $approver, $reason) {
            $beforeStatus = $negotiation->approval_status->value;

            $updatedNotes = $negotiation->notes;
            if ($reason) {
                $updatedNotes = ($updatedNotes ? $updatedNotes . "\n\n" : '') . "[Alasan Penolakan " . now()->format('d/m/Y H:i') . "]: " . $reason;
            }

            $negotiation->update([
                'approval_status' => NegotiationApprovalStatus::REJECTED,
                'approved_by_id' => $approver->id,
                'notes' => $updatedNotes,
            ]);

            $this->auditLogService->log(
                action: 'reject_negotiation',
                entity: 'Negotiation',
                before: ['status' => $beforeStatus],
                after: ['status' => NegotiationApprovalStatus::REJECTED->value, 'approver_id' => $approver->id],
                entityId: $negotiation->id
            );

            return $negotiation;
        });
    }
}
