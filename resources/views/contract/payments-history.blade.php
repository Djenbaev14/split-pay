<table class="w-100 " style="width: 100%;font-size:12px">
    <thead>
        <tr class="bg-gray-100" style="white-space: nowrap">
            {{-- №		Umumiy to'langan summa	To'lov turi	Dastlabki to'lov	Xodim	Status	Yaratilgan --}}
            <th class="border border-gray-300 px-4 py-2">№</th>
            <th class="border border-gray-300 px-4 py-2">Umumiy to'langan summa</th>
            <th class="border border-gray-300 px-4 py-2">To'lov turi</th>
            <th class="border border-gray-300 px-4 py-2">Dastlabki to'lov</th>
            <th class="border border-gray-300 px-4 py-2">Xodim</th>
            <th class="border border-gray-300 px-4 py-2">Status</th>
            <th class="border border-gray-300 px-4 py-2">Yaratilgan sana</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contract->payments as $key => $payment)
        <tr style="border-bottom:1px solid #929292;white-space: nowrap;">
            <td class="border-gray-300 p-2">{{ $payment->id}}</td>
            <td class="border-gray-300 p-2" style="color: #81c784;">{{ number_format($payment->total_amount,2,'.',' ')}}</td>
            <td class="border-gray-300 p-2">{{ $payment->paymentMethod->name}}</td>
            <td class="border-gray-300 p-2">{{ number_format($contract->down_payment,2,'.',' ')}}</td>
            <td class="border-gray-300 p-2">-</td>
            <td class="border-gray-300 p-2">-</td>
            <td class="border-gray-300 p-2">{{ $payment->created_at}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
