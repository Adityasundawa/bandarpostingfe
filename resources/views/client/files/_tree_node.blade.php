{{-- resources/views/client/files/_tree_node.blade.php --}}
<div id="tree-{{ $node['id'] }}"
     class="fm-tree-item"
     style="padding-left: {{ 12 + $depth * 14 }}px"
     onclick="navigateTo({{ $node['id'] }})">
    <i class="fas fa-folder{{ count($node['children']) ? '-open' : '' }}" style="font-size:11px;color:#fbbf24"></i>
    <span style="flex:1;overflow:hidden;text-overflow:ellipsis">{{ $node['name'] }}</span>
    @if($node['count'] > 0)
        <span style="font-size:9px;background:rgba(255,255,255,.07);padding:1px 5px;border-radius:10px;color:#5a5a7a">{{ $node['count'] }}</span>
    @endif
</div>
@if(count($node['children']))
    <div class="fm-tree-children">
        @foreach($node['children'] as $child)
            @include('client.files._tree_node', ['node' => $child, 'depth' => $depth + 1])
        @endforeach
    </div>
@endif
