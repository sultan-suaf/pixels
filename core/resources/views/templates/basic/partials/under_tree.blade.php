<ul @if ($isFirst) class="firstList" @endif>
    @foreach ($user->allReferrals as $under)
        @if ($loop->first)
            @php $layer++ @endphp
        @endif
        <li>{{ $under->fullname }} ( {{ $under->username }} )
            @if ($layer < $maxLevel && $under->allReferrals->count() > 0)
                @include($activeTemplate . 'partials.under_tree', ['user' => $under, 'layer' => $layer, 'isFirst' => false])
            @endif
        </li>
    @endforeach
</ul>
