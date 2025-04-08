<table class="w-100 border-collapse border border-gray-300 ">
    <thead>
        <tr class="bg-gray-100">
            <th class="border border-gray-300 px-4 py-2">№</th>
            <th class="border border-gray-300 px-4 py-2">To‘lov sanasi</th>
            <th class="border border-gray-300 px-4 py-2">Jami summa</th>
            <th class="border border-gray-300 px-4 py-2">Umumiy qarzdorlik</th>
            <th class="border border-gray-300 px-4 py-2">Umumiy to‘langan summa</th>
            <th class="border border-gray-300 px-4 py-2">To'lanadigan asosiy miqdor</th>
            <th class="border border-gray-300 px-4 py-2">To'lanadigan foiz miqdori</th>
            <th class="border border-gray-300 px-4 py-2">Asosiy qarzdorlik</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $key => $payment)
        <tr style="border-bottom:1px solid #929292;">
            <td class="border-gray-300" >{{ $key + 1 }}</td>
            <td class="border-gray-300" >{{ $payment->due_date }}</td>
            <td class="border-gray-300" style="color: #16B5CA;font-weight: normal">{{ number_format($payment->total_amount, 2,'.',' ') }}</td>
            <td class="border-gray-300" style="color: #d84d4d;font-weight: normal">{{ number_format($payment->total_amount, 2,'.',' ') }} </td>
            <td class="border-gray-300" style="color: #81c784;font-weight: normal">{{ number_format(0, 2) }} </td>
            <td class="border-gray-300" style="font-weight: normal">{{ number_format($payment->principal_amount, 2,'.',' ') }} </td>
            <td class="border-gray-300" style="font-weight: normal">{{ number_format($payment->interest_amount, 2,'.',' ') }} </td>
            <td class="border-gray-300" style="color: #d84d4d;font-weight: normal">{{ number_format($payment->principal_amount, 2,'.',' ') }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
