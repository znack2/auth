<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repository\UserRepository;

class LogRegisteredUser
{
    protected $userRepository;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
         $this->userRepository = $userRepository;
    }    

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->userRepository->setLastLoginAt($user);
    }
}
