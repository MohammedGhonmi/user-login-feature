<x-guest-layout>
    <table class="table table-sm">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Firstname</th>
                <th scope="col">Lastname</th>
                <th scope="col">Email</th>
                <th scope="col">Restore</th>
                <th scope="col">Permanently Delete</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <th scope="row">{{ $user->id }}</th>
                <td>{{ $user->firstname }}</td>
                <td>{{ $user->lastname }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <form method="post" action="{{ route('user.restore', ['id' => $user->id]) }}">
                        @csrf
                        @method('patch')
                        <x-primary-button type="submit" >{{ __('Restore') }} </x-primary-button>
                    </form>            
                </td>
                <td>
                    <form method="post" action="{{ route('user.delete', ['id' => $user->id]) }}">
                        @csrf
                        @method('delete')
                        <x-primary-button type="submit" >{{ __('Delete') }} </x-primary-button>
                    </form>            
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-guest-layout>
