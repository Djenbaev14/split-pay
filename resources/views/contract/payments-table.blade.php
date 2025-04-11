<style>
    .section{
        overflow-x: auto;
        max-width: 100%;
    }
    .table-wrapper {
        width: 100%;
        font-size: 13px;
        white-space:nowrap;
        font-weight:normal;
    }
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
<div class="section">
    <table class="table-wrapper">
        <thead>
            <tr class="bg-gray-100" >
                <th class="border border-gray-300 px-4 py-2"></th>
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
                <td class="handle"><label for="handle{{$key}}">+</label></td>
                <td class="border-gray-300">{{ $key + 1 }}</td>
                <td class="border-gray-300" >{{ $payment->due_date }}</td>
                <td class="border-gray-300" style="color: #16B5CA">{{ number_format($payment->total_amount, 2,'.',' ') }}</td>
                <td class="border-gray-300" style="color: #d84d4d">{{ number_format($payment->total_amount, 2,'.',' ') }} </td>
                <td class="border-gray-300" style="color: #81c784">{{ number_format($payment->paymentTransactions->sum('paid_total_amount'), 2) }} </td>
                <td class="border-gray-300">{{ number_format($payment->principal_amount, 2,'.',' ') }} </td>
                <td class="border-gray-300">{{ number_format($payment->interest_amount, 2,'.',' ') }} </td>
                <td class="border-gray-300" style="color: #d84d4d">{{ number_format($payment->principal_amount, 2,'.',' ') }} </td>
            </tr>
            <tr>
                <td colspan="5" class="content">
                <input type="checkbox" name="collapse" id="handle{{$key}}" />
                <div class="wrappervideo">
                    <p class="p-1">Foiz bo'yicha qarzdorlik <span style="color: #d84d4d">{{ number_format($payment->principal_amount - $payment->paymentTransactions->sum('paid_principal_amount'), 2,'.',' ') }}</span></p>
                    <p class="p-1">To'langan asosiy summa <span style="color: #81c784">{{ number_format($payment->paymentTransactions->sum('paid_interest_amount'), 2,'.',' ') }}</span></p>
                    <p class="p-1">To'langan foiz miqdori <span style="color: #81c784">{{ number_format($payment->paymentTransactions->sum('paid_principal_amount'), 2,'.',' ') }}</span></p>
                </div>
                
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
