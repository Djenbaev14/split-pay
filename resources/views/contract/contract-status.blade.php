<div class="custom-container" style="display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 10px;
            border: 1px dashed {{$contract->status->color}};
            border-radius: 5px;
            width: fit-content;">
    <span class="custom-number" style="font-weight: bold;
            "># {{$contract->id}}</span>
    <span class="custom-status" style="background-color: #f5f5f5; color: {{$contract->status->color}}; padding: 2px 8px; border-radius: 3px; font-size: 0.9rem;">
        {{$contract->status->name}}
    </span>
</div>