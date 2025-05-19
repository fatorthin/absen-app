<div x-data="{}" x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('qrcode')), @js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('qrcode-scan'))]">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div style="width: 100px height:100px" id="reader"></div>

</div>
