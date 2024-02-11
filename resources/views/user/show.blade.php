<x-guest-layout>
    <div class="container">
        <div class="row">
          <div class="col">
            User ID
          </div>
          <div class="col">
            {{ $user->id }}
          </div>
        </div>
        <div class="row">
            <div class="col">
              Photo
            </div>
            <div class="col">
              {{ $user->photo }}
            </div>
        </div>
        <div class="row">
          <div class="col">
            Full Name
          </div>
          <div class="col">
            {{ $user->prefixname }}. {{ $user->fullname }} 
          </div>
        </div>
        <div class="row">
            <div class="col">
              Email
            </div>
            <div class="col">
              {{ $user->email }}
            </div>
        </div>  
        <div class="row">
            <div class="col">
              Username
            </div>
            <div class="col">
              {{ $user->username }}
            </div>
        </div>
      </div>
</x-guest-layout>
