@foreach ($users as $user)
    <li>
        <a href="{{ route('member.images', $user->$relation->username) }}" class="d-inline-block user__img user__img--lg" data-bs-toggle="tooltip"
            title="{{ __($user->$relation->fullname) }}">
            <img src="{{ getImage(getFilePath('userProfile') . '/' . $user->$relation->image, null, 'user') }}" alt="@lang('follower')"
                class="user__img-is">
        </a>
    </li>
@endforeach
