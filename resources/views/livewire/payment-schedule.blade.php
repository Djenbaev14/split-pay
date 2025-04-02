<div class="p-4 bg-white rounded-lg shadow">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-200">
                <tr class="text-left">
                    <th class="border border-gray-300 px-4 py-2">#</th>
                    <th class="border border-gray-300 px-4 py-2">Oy</th>
                    <th class="border border-gray-300 px-4 py-2">Qiymat</th>
                    <th class="border border-gray-300 px-4 py-2">Foiz</th>
                    <th class="border border-gray-300 px-4 py-2">Jami</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($schedule as $row)
                    <tr class="border border-gray-300">
                        <td class="px-4 py-2 text-center">{{ $row['id'] }}</td>
                        <td class="px-4 py-2">{{ $row['date'] }}</td>
                        <td class="px-4 py-2 ">{{ number_format($row['monthlyPayment'], 2, '.', ' ') }} so‘m</td>
                        <td class="px-4 py-2 ">{{ number_format($row['interest'], 2, '.', ' ') }} so‘m</td>
                        <td class="px-4 py-2  font-semibold">{{ number_format($row['total'], 2, '.', ' ') }} so‘m</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-100 font-semibold">
                <tr>
                    <td colspan="2" class="border border-gray-300 px-4 py-2 text-right">Jami:</td>
                    <td class="border border-gray-300 px-4 py-2 text-right">{{ number_format(collect($schedule)->sum('monthlyPayment'), 2, '.', ' ') }} so‘m</td>
                    <td class="border border-gray-300 px-4 py-2 text-right">{{ number_format(collect($schedule)->sum('interest'), 2, '.', ' ') }} so‘m</td>
                    <td class="border border-gray-300 px-4 py-2 text-right">{{ number_format(collect($schedule)->sum('total'), 2, '.', ' ') }} so‘m</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
