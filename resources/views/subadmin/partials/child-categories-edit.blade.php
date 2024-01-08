@foreach($children as $child)
    <li class="category-item" style="margin-left: {{ $level * 20 }}px;">
        @if($child->childrenRecursive->count() > 0)
            <span class="toggle-icon">▶</span>
        @endif
        <input type="checkbox" class="category-checkbox child" name="selected_categories[]" value="{{$child->id}}" data-id="{{ $child->id }}" data-parent-id="{{ $parentId }}"
            {{ in_array($child->id, $productCategoryIds) ? 'checked' : '' }} />
        <label>{{ $child->name }}</label>
        @if($child->childrenRecursive->count() > 0)
            <ul class="sub-categories">
                @include('subadmin.partials.child-categories-edit', ['children' => $child->childrenRecursive, 'parentId' => $child->id, 'level' => $level + 1, 'productCategoryIds' => $productCategoryIds])
            </ul>
        @endif
    </li>
@endforeach
