<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function all()
    {
        return User::all();
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function findByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function create($data)
    {
        return User::create($data);
    }

    public function update($id, $data)
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }
        
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return false;
        }
        
        return $user->delete();
    }

    public function authenticate($email, $password)
    {
        $user = $this->findByEmail($email);
        if (!$user || !$user->checkPassword($password)) {
            return false;
        }
        
        return $user;
    }

    public function createSession($userId, $token, $ipAddress, $userAgent)
    {
        $payload = json_encode([
            'user_id' => $userId,
            'token' => $token,
            'created_at' => time()
        ]);

        return DB::table('sessions')->insert([
            'id' => $token,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'payload' => $payload,
            'last_activity' => time()
        ]);
    }

    public function findByToken($token)
    {
        $session = DB::table('sessions')
            ->where('id', $token)
            ->where('last_activity', '>', time() - (24 * 60 * 60)) // 24 horas
            ->first();

        if (!$session) {
            return null;
        }

        DB::table('sessions')
            ->where('id', $token)
            ->update(['last_activity' => time()]);

        return $this->find($session->user_id);
    }

    public function deleteSession($token)
    {
        return DB::table('sessions')->where('id', $token)->delete();
    }

    public function deleteUserSessions($userId)
    {
        return DB::table('sessions')->where('user_id', $userId)->delete();
    }
}
