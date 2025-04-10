@forelse ($cards as $card)
    <div class="mb-2 p-4 rounded-lg  from-teal-400 to-blue-600 text-white shadow-lg max-w-sm mx-auto" style="background: linear-gradient(153.94deg,#17BDC0,#18275D)">
        <div class="flex justify-between items-center">
            <img src="{{asset('images/atmos.svg')}}" alt="" style="max-width: 75px">
            
            <div class="flex space-x-2">
                <span>&#x21bb;</span> <!-- Refresh Icon -->
                <span>&#x22EE;</span> <!-- More Options Icon -->
            </div>
        </div>
        <div class="mt-4 text-xl font-semibold tracking-widest">
            {{ substr($card->car_number, 0, 4) }} **** **** {{ substr($card->car_number, -4) }}
        </div>
        <div class="mt-2 mb-1 text-sm">
            {{ substr($card->phone, 0, 4) . ' ' . substr($card->phone, 4, 2) . ' ' . substr($card->phone, 6, 3) . ' ' . substr($card->phone, 9, 2) . ' ' . substr($card->phone, 11, 2) }}
        </div>
        <div class="mt-4 font-semibold">
            {{ strtoupper($card->contract->client->first_name) }} {{ strtoupper($card->contract->client->last_name) }}
        </div>
    </div>
@empty
    
@endforelse