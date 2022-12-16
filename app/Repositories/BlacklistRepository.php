<?php

namespace App\Repositories;

use App\Exceptions\GeneralException;
use App\Models\Blacklist;

class BlacklistRepository extends BaseRepository
{
    public function model()
    {
        return Blacklist::class;
    }

    /**
     * get all blacklisted users
     *
     * @return mixed
     */
    public function all()
    {
        $list = $this->model->with(['user'])->get();

        return $list;
    }

    /**
     * get item details
     *
     * @param  int  $id
     * @return mixed
     */
    public function show($id)
    {
        $item = $this->model->with(['user'])
            ->find($id);

        return $item;
    }

    /**
     * add user to blacklist
     *
     * @param  int  $id
     * @param  int|null  $expire
     * @return bool
     */
    public function create($id, $expire = 0)
    {
        $check = $this->model->where('user_id', $id)->count();
        if ($check > 0) {
            throw new GeneralException('already added');
        }
        $item = $this->model->create([
            'user_id' => $id,
            'expire' => $expire,
        ]);

        return $item;
    }

    /**
     * remove user from blacklist
     *
     * @param  int  $id
     * @return bool
     */
    public function remove($id)
    {
        $this->model->where('id', $id)->delete();

        return true;
    }
}
