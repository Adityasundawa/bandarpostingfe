{{-- resources/views/client/files/_file_row.blade.php --}}
@php
$tIcon = match($f->type){ 'video'=>'🎬','image'=>'🖼️','pdf'=>'📄','archive'=>'🗜️','text'=>'📝',default=>'📁' };
$tColor= match($f->type){ 'video'=>'#a855f7','image'=>'#1877F2','pdf'=>'#EF4444','archive'=>'#F59E0B','text'=>'#10B981',default=>'var(--text-muted)' };
@endphp
<div class="fl-row">
    <div class="fl-ico t-{{ $f->type }}" style="font-size:14px;text-align:center">{{ $tIcon }}</div>
    <div>
        <div class="fl-name" title="{{ $f->original_name }}">{{ $f->original_name }}</div>
        @if($f->description)<div class="fl-meta" style="margin-top:1px">{{ $f->description }}</div>@endif
    </div>
    <div class="fl-meta">{{ $f->human_size }}</div>
    <div><span style="background:rgba(255,255,255,.05);color:{{ $tColor }};padding:1px 6px;border-radius:3px;font-size:10px;font-weight:600">{{ $f->extension ?: $f->type }}</span></div>
    <div class="fl-meta">{{ $f->created_at->format('d M Y, H:i') }}</div>
    <div class="fl-actions">
        <a href="{{ $f->download_url }}" class="fa-btn" title="Download"><i class="fas fa-download"></i></a>
        <button class="fa-btn" title="Edit" onclick="openRename({{ $f->id }},'{{ addslashes($f->original_name) }}','{{ addslashes($f->description??'') }}')"><i class="fas fa-pen"></i></button>
        <button class="fa-btn del" title="Hapus" onclick="deleteFile({{ $f->id }},this)"><i class="fas fa-trash-alt"></i></button>
    </div>
</div>
