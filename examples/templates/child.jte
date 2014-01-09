boot {
    extend('base.jte');
}

markup title {
    Child title
    123123
}

logic menu {
    replace('menu_items')->with(iterator(param('menu'), function($item, $count) {
        if ($count % 2 == 1) {
            $class = 'bg-1';
        } else {
            $class = 'bg-2';
        }

        $item['class'] = $class;

        return self($item);
    }));
}

markup menu {
    <ul class="head-menu">
        [[ menu_items ]] {{
            <li class="[[ =class ]]"><a href="[[ =url ]]">[[ =name ]]</a></li>
        }}
    </ul>
}

markup content {
    Some content!
}

markup footer {
    [[ parent ]]
    <div>User count: [[ =user_count ]]</div>
}