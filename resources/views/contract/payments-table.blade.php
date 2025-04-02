<table class="w-100 w-full border-collapse border border-gray-300">
    <thead>
        <tr class="bg-gray-100">
            <th class="border border-gray-300 px-4 py-2">№</th>
            <th class="border border-gray-300 px-4 py-2">To‘lov sanasi</th>
            <th class="border border-gray-300 px-4 py-2">Jami summa</th>
            <th class="border border-gray-300 px-4 py-2">Umumiy qarzdorlik</th>
            <th class="border border-gray-300 px-4 py-2">Umumiy to‘langan summa</th>
            <th class="border border-gray-300 px-4 py-2">To'lanadigan asosiy miqdor</th>
            <th class="border border-gray-300 px-4 py-2">To'lanadigan foiz miqdori</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $key => $payment)
        <tr>
            <td class="border border-gray-300 px-4 py-2">{{ $key + 1 }}</td>
            <td class="border border-gray-300 px-4 py-2">{{ $payment->due_date }}</td>
            <td class="border border-gray-300 px-4 py-2 text-green-500">{{ number_format($payment->total_amount, 2) }}</td>
            <td class="border border-gray-300 px-4 py-2 text-red-500">{{ number_format($payment->total_amount, 2) }} </td>
            <td class="border border-gray-300 px-4 py-2">{{ number_format(0, 2) }} </td>
            <td class="border border-gray-300 px-4 py-2 text-red-500">{{ number_format($payment->principal_amount, 2) }} </td>
            <td class="border border-gray-300 px-4 py-2 text-red-500">{{ number_format($payment->interest_amount, 2) }} </td>
        </tr>
        @endforeach
    </tbody>
</table>
