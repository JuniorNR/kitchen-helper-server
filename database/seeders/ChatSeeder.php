<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Chat;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $support = User::where('email', 'support@mail.ru')->first();
        $user = User::where('email', 'kitchenHelper@mail.ru')->first();

        if (! $support || ! $user) {
            return;
        }

        $chat = Chat::updateOrCreate(
            ['name' => 'Поддержка'],
            ['created_by' => $support->id]
        );

        $chat->users()->syncWithoutDetaching([$support->id, $user->id]);
    }
}



