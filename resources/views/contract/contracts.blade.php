<table class="w-100 border-collapse border border-gray-300 " style="width: 100%;font-size:12px">
    <thead>
        <tr class="bg-gray-100" style="white-space: nowrap">
            {{-- №	Shartnoma raqami	Holati	Kompaniya nomi	Qiymat	Qarzdorlik	Muddati (oy)	Avtoto‘lov	Yaratilgan sana --}}
            <th class="border border-gray-300 px-4 py-2">Shartnoma raqami</th>
            <th class="border border-gray-300 px-4 py-2">Holati</th>
            <th class="border border-gray-300 px-4 py-2">Kompaniya nomi</th>
            <th class="border border-gray-300 px-4 py-2">Qiymat</th>
            <th class="border border-gray-300 px-4 py-2">Qarzdorlik</th>
            <th class="border border-gray-300 px-4 py-2">Muddati (oy)</th>
            <th class="border border-gray-300 px-4 py-2">Avtoto‘lov</th>
            <th class="border border-gray-300 px-4 py-2">Yaratilgan sana</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contracts as $key => $contract)
        <tr style="border-bottom:1px solid #929292;white-space: nowrap;">
            <td class="border-gray-300 p-2"><a href="/business/contracts/{{$contract->id}}/edit" style="color:#3EB0C0">№{{ $contract->id}}</a></td>
            <td class="border-gray-300 p-2" style="color: {{$contract->status->color}}">{{ $contract->status->name}}</td>
            <td class="border-gray-300 p-2">{{ $contract->branch->name}}</td>
            <td class="border-gray-300 p-2">{{ number_format($contract->amount,2,'.',' ')}}</td>
            <td class="border-gray-300 p-2">{{ number_format($contract->amount,2,'.',' ')}}</td>
            <td class="border-gray-300 p-2">{{ $contract->period_month}}</td>
            <td class="border-gray-300 p-2">-</td>
            <td class="border-gray-300 p-2">{{ $contract->created_at}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
