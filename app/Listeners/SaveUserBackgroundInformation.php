<?php

namespace App\Listeners;

use App\Events\UserSaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Details;

class SaveUserBackgroundInformation
{

    public int $userId;

    /**
     * Create the event listener.
     */
    public function __construct(
        public Details $details
    ) {}

    /**
     * Handle the event.
     */
    public function handle(UserSaved $event): void
    {
        $user = $event->user;
        $this->userId = $user['id'];

        $this->createDetails("Full name", $user['fullname']);
        $this->createDetails("Middle Initial", $user['middleinitial']);
        $this->createDetails("Avatar", $user['photo']);
        $this->createDetails("Gender", "Male");
    }
    /**
     * 
     */
    private function createDetails ($key, $value): void 
    {
        $this->details->create([
            'user_id' => $this->userId,
            'key' => $key,
            'value' => $value,
            'type' => 'bio',
        ]);
    }
}
