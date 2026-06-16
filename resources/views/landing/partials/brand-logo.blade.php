@if(app_setting('app_logo'))
    <img src="{{ asset('storage/' . app_setting('app_logo')) }}"
         alt="{{ app_setting('app_name', 'Perpustakaan') }}"
         style="width:34px;height:34px;object-fit:contain;background:#fff;border:2px solid var(--comic-dark);box-shadow:2px 2px 0 var(--comic-dark);padding:2px;">
@else
    <span class="brand-icon">📚</span>
@endif
<span class="brand-text fw-black">{{ app_setting('app_name', 'Perpustakaan') }}</span>
