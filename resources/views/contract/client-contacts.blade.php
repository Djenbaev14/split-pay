<table class="w-100 border-collapse border border-gray-300" style="width: 100%">
    <thead>
        <tr class="bg-gray-100">
            <th class="border border-gray-300 px-4 py-2">FIO</th>
            <th class="border border-gray-300 px-4 py-2">Telefon</th>
            <th class="border border-gray-300 px-4 py-2">Munosabat</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clientContacts as $key => $contact)
        <tr>
            <td class="border border-gray-300 p-2">{{ $contact->fio}}</td>
            <td class="border border-gray-300 p-2">{{ $contact->phone}}</td>
            <td class="border border-gray-300 p-2">{{ $contact->relation}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
