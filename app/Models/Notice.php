<?php
// app/Models/Notice.php
namespace App\Models;

use App\Core\Model;

class Notice extends Model {
    protected $table = 'notices';
    protected $fillable = [
        'title',
        'content',
        'type',
        'target_type',
        'target_ids',
        'created_by',
        'published_at',
        'expires_at',
        'views'
    ];
    
    public function getActiveNotices() {
        $sql = "SELECT * FROM {$this->table} 
                WHERE published_at <= NOW() 
                AND (expires_at IS NULL OR expires_at >= NOW())
                ORDER BY 
                    CASE type 
                        WHEN 'Khẩn cấp' THEN 1
                        WHEN 'Quan trọng' THEN 2
                        ELSE 3
                    END,
                    published_at DESC";
        return $this->db->fetchAll($sql);
    }
}