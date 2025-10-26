<?php

namespace App\Filament\Resources\HikerResource\Pages;

use App\Filament\Resources\HikerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class EditHiker extends EditRecord
{
    protected static string $resource = HikerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $hiker = $this->record;

        return DB::transaction(function () use ($data, $hiker) {
            $user = $hiker->user;

            if ($user) {
                if (
                    User::where('email', $data['email'])
                        ->where('id', '!=', $user->id)
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'email' => 'Email is already in use by another account.',
                    ]);
                }

                $user->name = $data['name'];
                $user->email = $data['email'];

                if (array_key_exists('password', $data)) {
                    $user->password = Hash::make($data['password']);
                }

                $user->save();
            } else {
                if (User::where('email', $data['email'])->exists()) {
                    throw ValidationException::withMessages([
                        'email' => 'Email is already in use by another account.',
                    ]);
                }

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password'] ?? Str::random(16)),
                ]);

                $user->assignRole('hiker');
                $data['user_id'] = $user->id;
            }

            if (array_key_exists('password', $data)) {
                unset($data['password']);
            }

            return $data;
        });
    }
}
