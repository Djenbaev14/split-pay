<div>
    @if($contracts->isEmpty())
        <p class="text-gray-500">Bu mijozga tegishli shartnomalar mavjud emas.</p>
    @else
    <div style="display: flex; justify-content: end; align-items: center; margin-bottom: 10px; padding-right: 20px;">
        <span style="font-size: 13px;">{{ count($contracts) }} shartnomalar topildi</span>
        <a href="{{ route('contracts.export',$client_id) }}" style="text-decoration: none; padding: 6px 12px; background: #3EB0C0; color: white; border-radius: 4px;font-size: 12px;margin-left:10px">
            Excel yuklash
        </a>
    </div>
    <table style="width: 100%; border-collapse: collapse; text-align: left;font-size: 14px">
        <thead>
            <tr style="background-color: #f3f3f3;">
                <th style="padding:7px;">Shartnoma raqami</th>
                <th style="padding:7px;">Holati</th>
                <th style="padding:7px;">Kompaniya nomi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($contracts as $contract)
                <tr>
                    <td style="padding:7px;"><a style="color: #3EB0C0" href="contracts/{{ $contract->id }}/edit">№ {{ $contract->id }}</a></td>
                    <td style="padding:7px;color:{{ $contract->status->color }};font-size: 12px">{{ $contract->status->name }}</td>
                    <td style="padding:7px;">{{ $contract->branch->company_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>