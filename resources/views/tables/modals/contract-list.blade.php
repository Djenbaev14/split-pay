<style>
    input[name="collapse"] {
        display:none; 
    }
   .wrappervideo {
     display: none; 
   }
    input[name="collapse"]:checked ~ .wrappervideo {
        display: initial; 
    }
</style>

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
                <th ></th>
                <th style="padding:7px;">Shartnoma raqami</th>
                <th style="padding:7px;">Holati</th>
                <th style="padding:7px;">Kompaniya nomi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($contracts as $key=> $contract)
                <tr>
                    <td class="handle"><label for="handle{{$key}}">+</label></td>
                    <td style="padding:7px;"><a style="color: #3EB0C0" href="contracts/{{ $contract->id }}/edit"> {{ $contract->id }}</a></td>
                    <td style="padding:7px;color:{{ $contract->status->color }};font-size: 12px">{{ $contract->status->name }}</td>
                    <td style="padding:7px;">{{ $contract->branch->company_name }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="content">
                        <input type="checkbox" name="collapse" id="handle{{$key}}" />
                        <div class="wrappervideo">
                            <p class="p-1">Qiymat &nbsp;&nbsp;&nbsp;<span style="color: #3EB0C0">{{ number_format($contract->amount-$contract->down_payment, 2,'.',' ') }}</span></p>
                            <p class="p-1">Qarzdorlik &nbsp;&nbsp;&nbsp;<span style="color:#d84d4d">{{ number_format(round($contract->paymentSchedule->sum('principal_amount')) - $contract->paymentTransactions->sum('paid_principal_amount'), 2,'.',' ') }}</span></p>
                            <p class="p-1">Muddati (oy) &nbsp;&nbsp;&nbsp;<span>{{ $contract->period_month }}</span></p>
                            <p class="p-1">Yaratilgan sana &nbsp;&nbsp;&nbsp;<span>{{ $contract->created_at->format('M d, Y H:i') }}</span></p>
                        {{-- Qiymat	8 300 000
Qarzdorlik	8 300 000.04
Muddati (oy)	12
Avtoto‘lov	-
Yaratilgan sana	Apr 10, 2025 at 6:10 --}}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>