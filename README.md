Jedi template engine
===

Fast and flexible template engine

See [example](https://github.com/vitkovskii/jte/tree/master/examples)

Basic usage
---
```
$jte = new Jte(__DIR__ . '/templates', ['useCache' => true, 'dir' => __DIR__ . '/cache/']);

echo $jte->render('template.jte', 'main', ['param1' => 'value1', 'param2' => 'value2']);
```

Template syntax
---

Template consists of sections. There are three section types:

* Boot section
* Block markup section
* Block logic section

### Boot section
This section is executed then template is booting. Body of the section is pure PHP code.

If you want template extends another, you can specify it in this section:
```
boot {
    extend('base.jte');
}
```

Dynamic inheritance:
```
boot {
    extend(param('parent'));
}
```

More complex:
```
boot {
    if (param('some') % 2 = 0) {
        extend('foo.jte');
    } else {
        extend('baz.jte');
    }
}
```

### Block markup section
Block is an independent chunk of HTML or something else :)
```
markup some_block_name {
    <html>
        <head>
        </head>
        <body>
        </body>
    </html>
}
```

You can include references to another blocks into block:
```
markup some_block_name {
    <html>
        <head>
        </head>
        <body>
            [[ body_block ]]
        </body>
    </html>
}
```

Also you can include tree of blocks:
```
markup some_block_name {
    <html>
        <head>
        </head>
        <body>
            [[ body_block ]] {{
                <div>
                    [[ menu_block ]]
                </div>
                <div>
                    [[ footer_block ]]
                </div>
            }}
        </body>
    </html>
}
```

If you want to include passed param into template do this:
```
markup footer {
    <div>
        User count: [[ user_count_block = user_count_param ]]
    </div>
}
```

Or anonymous variant:
```
markup footer {
    <div>
        User count: [[ =user_count_param ]]
    </div>
}
```

### Block logic section
With this block you can dynamically generate block contents from params or another blocks.
Body of this block is pure PHP code.

Replace block with passed param:
```
markup footer {
    <div>
        User count: [[ user_count_block ]]
    </div>
}

logic footer {
    replace('user_count_block')->with(param('user_count'));
}
```

Replace block with another block:
```
markup item {
    Foo :)
}

markup footer {
    <div>
        [[ footer_content ]]
    </div>
}

logic footer {
    replace('footer_content')->with(block('item'));
}
```

Replace block with iterator:
```
markup item {
    Foo :) [[ =count ]]
}

markup footer {
    <div>
        [[ footer_content ]]
    </div>
}

logic footer {
    replace('footer_content')->with(iterator([1, 2, 3], function($item, $count) {
        return block('item', ['count' => $count]);
    }));
}
```

Self replacement:
```
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
```
