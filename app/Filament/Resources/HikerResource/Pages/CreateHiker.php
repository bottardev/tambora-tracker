<?php

namespace App\Filament\Resources\HikerResource\Pages;

use App\Filament\Resources\HikerResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CreateHiker extends CreateRecord
{
    protected static string $resource = HikerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email is already in use by another account.',
            ]);
        }

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $user->assignRole('hiker');

            $data['user_id'] = $user->id;
            unset($data['password']);

            return $data;
        });
    }
}
