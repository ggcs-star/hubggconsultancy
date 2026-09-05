@foreach ($children as $child)
    <x-team-tree-node
        :node="$child"
        :color-index="$colorIndex"
        :progress-by-user-id="$progressByUserId"
        :is-first="$loop->first"
        :is-last="$loop->last"
    />
@endforeach
