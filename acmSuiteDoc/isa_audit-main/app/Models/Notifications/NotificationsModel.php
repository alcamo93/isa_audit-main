<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Model;
use App\Classes\StatusConstants;

class NotificationsModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    /**
     * Unread notifications
     */
    public function scopeGetNotificationsDT($query, $page, $rows, $search, $draw, $order, $idUser, $status){
        $query->where('notifications.notifiable_id', $idUser)
            ->orderBy('notifications.updated_at', 'DESC');
        if ($status == 'unread') {
            $query->whereNull('notifications.read_at');
        }
        else{
            $query->whereNotNull('notifications.read_at');
        }
        $queryCount = $query->get();
        $result = $query->limit($rows)->offset($page)->get()->toArray();
        $total = $queryCount->count();
        $data['data'] = ( sizeof($result) > 0) ? $result : array();
        $data['recordsTotal'] = $total;
        $data['draw'] = (int) $draw;
        $data['recordsFiltered'] = $total;
        return $data;
    }
}