<table id="expense-table" class="table table-bordered  table-hover">
    <thead>
        <tr>
            <th>Name</th>
            <th>Groups</th>
            <th>Total Amount</th>
            <th>Settele</th>
        </tr>
    </thead>
    <tbody class="tbody">
        @foreach ($friends as $friend)
            <tr class="data_{{ $friend['id'] }}">
                <td>{{ $friend['name'] }}</td>
                <td class="groupIdContainer">
                    <div class="row">
                        @foreach ($friendsGroupsWithUsers as $groups)
                            {{-- {{ dd($groups) }} --}}
                            @foreach ($groups['users'] as $users)
                                {{-- {{$users['id']}} --}}
                                {{-- if group users id matches the friend id then show that groups name --}}
                                @if ($users['id'] == $friend['id'])
                                    <div class="mr-3" data-group-id="{{ $groups['id'] }}">
                                        <h4>
                                            <span class="badge badge-light">{{ $groups['title'] }}</span>
                                        </h4>
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </td>
                <td
                    @isset($friend['status'])
                    @if ($friend['status'] == 'owe') class="text-success data_amount_{{ $friend['id'] }}"
                    @elseif($friend['status'] == 'pay') class="text-danger data_amount_{{ $friend['id'] }}"
                    @endif @endisset>
                    @php
                        $friend['remainigAmount'] = isset($friend['remainigAmount']) ? $friend['remainigAmount'] : 0;
                    @endphp
                    {{ $friend['remainigAmount'] }}
                </td>
                <td id="actions">
                    {{-- settelment modal button --}}
                    <button name="settle" value="Settle" class="btn  mr-2  text-primary open-settel-modal"
                        data-friend-id="{{ $friend['id'] }}">
                        <i class="fa fa-money" style="font-size:24px"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
