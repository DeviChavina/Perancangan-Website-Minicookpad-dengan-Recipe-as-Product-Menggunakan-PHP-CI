<?php
namespace App\Models;
use CodeIgniter\Model;

class ChefVerificationModel extends Model
{
    protected $table         = 'chef_verifications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id','verification_type','target_role',
        'id_card_number','id_card_photo','certificate_photo',
        'portfolio_url','specialization','experience',
        'status','admin_note','reviewed_at','created_at','updated_at'
    ];

    public function getUserVerification(int $userId, string $type = 'basic'): ?array
    {
        return $this->where('user_id', $userId)
            ->where('verification_type', $type)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /** Cek apakah user punya verifikasi basic yang sudah approved */
    public function hasApprovedBasic(int $userId): bool
    {
        return (bool) $this->where('user_id', $userId)
            ->where('verification_type', 'basic')
            ->where('status', 'approved')
            ->first();
    }

    /** Ambil semua verifikasi user (bisa ada basic + advanced) */
    public function getAllByUser(int $userId): array
    {
        return $this->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll();
    }
}
