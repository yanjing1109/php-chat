<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $table = 'news';
    protected $dateFormat = 'Y-m-d H:i:s';

    public function saveNews($data)
    {
        $self = new self();
        if (is_array($data)) {
            foreach ($data as $key => $v) {
                $self-> $key = $v;
            }
        }
        $self->save();
    }

    public function getList()
    {

    }


}
