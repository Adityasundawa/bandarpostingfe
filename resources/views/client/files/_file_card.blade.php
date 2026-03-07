{{-- resources/views/client/files/_file_card.blade.php --}}
@php
$tIcon = match($f->type){ 'video'=>'🎬','image'=>'🖼️','pdf'=>'📄','archive'=>'🗜️','text'=>'📝',default=>'📁' };
$tColor= match($f->type){ 'video'=>'#a855f7','image'=>'#1877F2','pdf'=>'#EF4444','archive'=>'#F59E0B','text'=>'#10B981',default=>'#555' };
@endphp
<div class="fc t-{{ $f->type }}" onclick="selectCard(this)">
    <div class="fc-thumb">
        <span>{{ $tIcon }}</span>
        <span class="fc-ext" style="background:{{ $tColor }}">{{ $f->extension ?: $f->type }}</span>
    </div>
    <div class="fc-name" title="{{ $f->original_name }}">{{ $f->original_name }}</div>
    <div class="fc-size">{{ $f->human_size }}</div>
    <div class="fc-actions">
        <a href="{{ $f->download_url }}" class="fa-btn" title="Download" onclick="event.stopPropagation()"><i class="fas fa-download"></i></a>
        <button class="fa-btn" title="Edit" onclick="event.stopPropagation();openRename({{ $f->id }},'{{ addslashes($f->original_name) }}','{{ addslashes($f->description??'') }}')"><i class="fas fa-pen"></i></button>
        <button class="fa-btn del" title="Hapus" onclick="event.stopPropagation();deleteFile({{ $f->id }},this)"><i class="fas fa-trash-alt"></i></button>
    </div>
</div>
